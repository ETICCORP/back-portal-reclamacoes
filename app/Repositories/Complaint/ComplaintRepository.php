<?php

namespace App\Repositories\Complaint;

use App\Models\Complaint\Complaint;
use App\Repositories\AbstractRepository;
use App\Repositories\Comment\CommentRepository;
use App\Repositories\Complaintattachment\ComplaintattachmentRepository;
use App\Repositories\Description\DescriptionRepository;

use App\Repositories\Reporter\ReporterRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\Jobs\AlertJob;
use App\Jobs\GenerateAlertsJob;
use App\Mail\ReportAlertMail;
use Illuminate\Support\Facades\Mail;

class ComplaintRepository extends AbstractRepository
{

    protected ReporterRepository $reporter;
    protected ComplaintattachmentRepository $attachments;
    protected CommentRepository $commentRepository;
    protected DescriptionRepository $description;
    protected ComplaintDeadlineRepository $complaintDeadlineRepository;

    public function __construct(
        Complaint $model,

        ReporterRepository $reporter,
        ComplaintDeadlineRepository $complaintDeadlineRepository,
        DescriptionRepository $description,
        ComplaintattachmentRepository $attachments,
        CommentRepository $commentRepository
    ) {
        $this->description        = $description;
        $this->attachments        = $attachments;
        $this->commentRepository  = $commentRepository;
        $this->complaintDeadlineRepository  = $complaintDeadlineRepository;
        parent::__construct($model);
    }

    /**
     * Armazena uma nova denúncia
     */
    /**
     * Regista uma nova reclamação com anexos, prazos e alertas.
     */
    public function storeData(array $data): Complaint
    {
        // 1. Operação Transacional Securizada
        return DB::transaction(function () use ($data) {

            $randomCode = $this->generateUniqueCode(6);

            // Determina o utilizador logado de forma segura
            $userId = Auth::check() ? Auth::id() : null;

            // 2. Criação da Reclamação
            $complaint = $this->model->create([
                'source'           => $data['source'] ?? 'portal',
                'user_id'          => $userId,
                'policy_number'    => $data['policy_number'] ?? null,
                'description'      => $data['description'] ?? null,
                'code'             => (string) $randomCode,
                'full_name'        => $data['full_name'] ?? null,
                'complainant_role' => $data['complainant_role'] ?? null,
                'contact'          => $data['contact'] ?? null,
                'email'            => $data['email'] ?? null,
                'entity'           => $data['entity'] ?? null,
                'incidentDateTime' => $data['incidentDateTime'] ?? null,
                'location'         => $data['location'] ?? null,
                'type'             => $data['type'] ?? null,
                'status'           => 'Pendente',
                'representative'   => $data['representative'] ?? null,
            ]);

            // 3. Processamento de Anexos
            if (!empty($data['attachments'])) {
                $this->handleAttachments($data['attachments'], $complaint->id);
            }

            // 4. Cálculo dos 15 dias úteis de forma limpa
            $startDate = Carbon::now();
            $endDate = $this->calculateBusinessDays($startDate, 15);

            // 5. Criação do Prazo de Resposta
            $this->complaintDeadlineRepository->model->create([
                'complaint_id' => $complaint->id,
                'days'         => 15,
                'start_date'   => $startDate,
                'end_date'     => $endDate,
                'status'       => 'Pendente',
                'notified_at'  => null,
            ]);

            // 6. Carregamento dos relacionamentos necessários para o Job/Mail
            $complaint->load(['attachments', 'typeReport']);

            // 7. Disparos Assíncronos (Evita lentidão no ecrã do utilizador)
            AlertJob::dispatch($complaint->id);

            if (!empty($data['email'])) {
                // Forçamos o uso do ->queue() para libertar o PHP imediatamente
                Mail::to($data['email'])->queue(new ReportAlertMail($complaint));
            }

            return $complaint;
        });
    }

    /**
     * Calcula a data final com base em dias úteis (pula fins de semana).
     */
    private function calculateBusinessDays(Carbon $startDate, int $daysToAdd): Carbon
    {
        $date = $startDate->copy();

        while ($daysToAdd > 0) {
            $date->addDay();
            if (!$date->isWeekend()) {
                $daysToAdd--;
            }
        }

        return $date;
    }


    public function updateComplaint(array $data, int $id)
    {
        $complaint = $this->model->find($id);

        if ($complaint) {
            $complaint->update(Arr::only($data, [
                'due_date',
                'responsible_area',
                'justification',
                'urgency',
                'gravity',
                'responsible_analyst',
            ]));

            return $complaint;
        }
    }

    /**
     * Atualiza status da denúncia e cria comentário
     */
    public function updateStatus(array $data, int $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $model = $this->model->findOrFail($id);

            $model->update(['status' => $data['status']]);

            if (isset($data['comment'])) {

                $this->commentRepository->model::create([
                    "comment"   => $data['comment'],
                    "report_id" => $id,
                    "fk_user" => Auth::user()->id
                ]);
            }

            // 📎 Anexos
            $this->handleAttachments($data['attachments'] ?? null, $id);

            return $model;
        });
    }

    /**
     * Processa anexos de denúncia
     */
    private function handleAttachments($attachments, int $complaintId): void
    {
        if (empty($attachments)) {
            return;
        }

        if (is_string($attachments)) {
            $attachments = json_decode($attachments, true);
        }

        if (is_array($attachments)) {
            $this->attachments->createComplaintAttachment($attachments, $complaintId);
        }
    }

    /**
     * Gera código único de denúncia
     */
    private function generateUniqueCode(int $length): string
    {
        do {
            $randomCode = $this->model::generateCustomRandomCode($length);
        } while ($this->model::where('code', $randomCode)->exists());

        return $randomCode;
    }

    /**
     * Total geral de denúncias
     */
    public function total(): int
    {
        return $this->model::count();
    }

    //========================================

    public function timeResponse()
    {
        $avgHours = $this->model::select(
            DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
        )->value('avg_hours');

        return response()->json([
            'avg_response_time_hours' => round($avgHours, 2)
        ]);
    }


    /**
     * Retorna denúncia pelo código
     */
    public function getByCode(string $code)
    {
        $complaint = $this->model::with([
            "attachments",
            "soluctions",
            "typeReport",
            "interaction",
            "opinions",
            "triages",
            "deadlines",
            "proverResponse",
            "forwardProvider",
            "entitiyResponse"
        ])->where('code', $code)->firstOrFail();

        return $complaint;
    }

    /**
     * Total de denúncias na semana atual
     */
    public function totalForCurrentWeek(): int
    {
        return $this->model::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->count();
    }

    /**
     * Total de denúncias na semana anterior
     */
    public function totalForLastWeek(): int
    {
        return $this->model::whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek(),
        ])->count();
    }

    /**
     * Retorna comparação semanal
     */
    public function compareCurrentAndPreviousWeek(): array
    {
        return $this->comparePeriod(
            $this->totalForCurrentWeek(),
            $this->totalForLastWeek()
        );
    }

    /**
     * Lógica genérica de comparação entre períodos
     */
    private function comparePeriod(int $current, int $previous): array
    {
        $percentageChange = $previous > 0
            ? (($current - $previous) / $previous) * 100
            : ($current > 0 ? 100 : 0);

        return [
            'current'           => $current,
            'previous'          => $previous,
            'percentage_change' => round($percentageChange, 2),
        ];
    }

    /**
     * Top N tipos de denúncia
     */
    public function getTopTypes(int $limit = 4)
    {
        return $this->model::select('type_complaints.name as type', \DB::raw('COUNT(*) as count'))
            ->join('type_complaints', 'type_complaints.id', '=', 'complaint.type')
            ->groupBy('type_complaints.name')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }



    /**
     * Contagem de denúncias por data
     */
    public function countByDate(string $startDate, string $endDate)
    {
        return $this->model::selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as total')

            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get()
            ->map(fn($item) => [
                'date'        => $item->date,
                'total'       => (int) $item->total,

            ]);
    }


    public function byManth()
    {
        $complaintsByMonth = $this->model::select(
            DB::raw("DATE_FORMAT(created_at, '%M') as month"), // nome do mês
            DB::raw('COUNT(*) as total')
        )

            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($complaintsByMonth);
    }

    public function repeatOffenders()
    {
        return DB::table('complaint')
            ->select(
                DB::raw('full_name as entity'), // mapeia full_name para entity
                DB::raw('COUNT(*) as total_complaints')
            )
            ->groupBy('full_name')
            ->having('total_complaints', '>', 1)
            ->get();
    }
}
