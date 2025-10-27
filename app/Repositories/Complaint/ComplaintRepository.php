<?php

namespace App\Repositories\Complaint;

use App\Models\Complaint\Complaint;
use App\Repositories\AbstractRepository;
use App\Repositories\Comment\CommentRepository;
use App\Repositories\Complaintattachment\ComplaintattachmentRepository;
use App\Repositories\Description\DescriptionRepository;
use App\Repositories\InvolveColleagues\InvolveColleaguesRepository;
use App\Repositories\Reporter\ReporterRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ComplaintRepository extends AbstractRepository
{
    protected InvolveColleaguesRepository $involveColleagues;
    protected ReporterRepository $reporter;
    protected ComplaintattachmentRepository $attachments;
    protected CommentRepository $commentRepository;
    protected DescriptionRepository $description;

    public function __construct(
        Complaint $model,
        InvolveColleaguesRepository $involveColleagues,
        ReporterRepository $reporter,
        DescriptionRepository $description,
        ComplaintattachmentRepository $attachments,
        CommentRepository $commentRepository
    ) {
        $this->involveColleagues  = $involveColleagues;
        $this->reporter           = $reporter;
        $this->description        = $description;
        $this->attachments        = $attachments;
        $this->commentRepository  = $commentRepository;

        parent::__construct($model);
    }

    /**
     * Armazena uma nova denúncia
     */
    public function storeData(array $data): Complaint
    {

        $randomCode = $this->generateUniqueCode(6);

        $complaint = $this->model->create([
            'due_date' => $data['due_data']?? null,
            'responsible_area' => $data['responsible_area'] ?? null,
            'justification' => $data['justification'] ?? null,
            'urgency' => $data['urgency'] ?? null,
            'gravity' => $data['gravity'] ?? null,
            'responsible_analyst' => $data['responsible_analyst']?? null,
            'entity' => $data['entity'],
            'contract_number' => $data['contract_number'] ?? null,
            
            'type' => $data['type'],
            'code' => $randomCode,
            'description' => $data['description'] ?? null,
            'incidentDateTime'  => $data['incidentDateTime'],
            'location' => $data['location'],
            'suggestionAttempt' => $data['suggestionAttempt'],

            'relationship' => $data['relationship'],
            'status' => "Pendente",
            'isAnonymous' => $data['isAnonymous'],
        ]);

        // 👥 Colaboradores envolvidos
        if (!empty($data['involveColleagues'])) {
            $this->involveColleagues->handleInvolvedColleagues(
                $complaint->id,
                $data['involveColleagues']
            );
        }

        // 🧑‍💼 Denunciante
        if (!empty($data['reporter'])) {
            $this->reporter->handleReporter($data['reporter'], $complaint->id);
        }

        // 📎 Anexos
        $this->handleAttachments($data['attachments'] ?? null, $complaint->id);

        $complaint->load([
            "involveds",
            "reports",
            "attachments",
            "soluctions",
            "typeReport"
        ]);

        return $complaint;
    }

    public function updateComplaint(array $data, int $id): ?Complaint
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


        $model = $this->model->findOrFail($id);

        $model->update(['status' => $data['status']]);

        $this->commentRepository->model::create([
            "comment"   => $data['comment'],
            "report_id" => $id,
            "fk_user" => Auth::user()->id
        ]);

        // 📎 Anexos
        $this->handleAttachments($data['attachments'] ?? null, $id);

        return $model;
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

    // ⏱️ 2. Tempo médio de resposta
    public function timeResponse(Request $request)
    {
        $start = $request->get('start_date', Carbon::now()->subMonth());
        $end = $request->get('end_date', Carbon::now());

        $avgHours = Complaint::whereBetween('created_at', [$start, $end])
            ->whereNotNull('response_date')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, response_date)) as avg_hours'))
            ->value('avg_hours');

        return response()->json([
            'avg_response_time_hours' => round($avgHours, 2)
        ]);
    }

    // 📅 3. Percentagem dentro do prazo legal
    public function percentagemPrazoLegal(Request $request)
    {
        $start = $request->get('start_date', Carbon::now()->subMonth());
        $end = $request->get('end_date', Carbon::now());
        $prazoLegal = 15; // dias

        $complaints = Complaint::whereBetween('created_at', [$start, $end])
            ->whereNotNull('response_date')
            ->get();

        $total = $complaints->count();
        $withinDeadline = $complaints->filter(fn($c) =>
            $c->response_date->diffInDays($c->created_at) <= $prazoLegal
        )->count();

        $percent = $total > 0 ? round(($withinDeadline / $total) * 100, 2) : 0;

        return response()->json([
            'percent_within_legal_deadline' => $percent
        ]);
    }

    // 📊 4. Reclamações por tipologia
    public function reclamacoesPorTipologia(Request $request)
    {
        $start = $request->get('start_date', Carbon::now()->subMonth());
        $end = $request->get('end_date', Carbon::now());

        $byType = Complaint::whereBetween('created_at', [$start, $end])
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        return response()->json([
            'complaints_by_type' => $byType
        ]);
    }

    // 📨 5. Reclamações reencaminhadas ao Provedor
    public function reclamacoesProvedor(Request $request)
    {
        $start = $request->get('start_date', Carbon::now()->subMonth());
        $end = $request->get('end_date', Carbon::now());

        $count = Complaint::whereBetween('created_at', [$start, $end])
            ->where('forwarded_to_provider', true)
            ->count();

        return response()->json([
            'forwarded_to_provider' => $count
        ]);
    }

    // 🔁 6. Reclamações reincidentes por cliente
    public function reclamacoesReincidentes(Request $request)
    {
        $reincidents = Complaint::select('customer_id', DB::raw('count(*) as total'))
            ->groupBy('customer_id')
            ->having('total', '>', 1)
            ->get()->count();

        return response()->json([
            'reincidents_by_customer' => $reincidents
        ]);
    }

    //========================================

    /**
     * Retorna denúncia pelo código
     */
    public function getByCode(string $code)
    {
        $complaint = $this->model::with([
            "involveds",
            "reports",
            "attachments",
            "soluctions",
            "typeReport"
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
            ->selectRaw('SUM(CASE WHEN isAnonymous = true THEN 1 ELSE 0 END) as anonymous')
            ->selectRaw('SUM(CASE WHEN isAnonymous = false THEN 1 ELSE 0 END) as identified')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get()
            ->map(fn($item) => [
                'date'        => $item->date,
                'total'       => (int) $item->total,
                'anonymous'   => (int) $item->anonymous,
                'identified'  => (int) $item->identified,
            ]);
    }
}
