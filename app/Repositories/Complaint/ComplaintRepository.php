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
            'type'              => $data['type'],
            'code'              => $randomCode,
            'description'       => $data['description'] ?? null,
            'incidentDateTime'  => $data['incidentDateTime'],
            'location'          => $data['location'],
            'suggestionAttempt' => $data['suggestionAttempt'],
            'relationship'      => $data['relationship'],
            'status'            => "Pendente",
            'isAnonymous'       => $data['isAnonymous'],
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
            "fk_user"=>Auth::user()->id
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

    // Se for JSON string vindo do frontend
    if (is_string($attachments)) {
        $attachments = json_decode($attachments, true);
    }

    if (is_array($attachments)) {
        $normalized = [];

        foreach ($attachments as $item) {
            if (is_string($item) && str_starts_with($item, 'data:image')) {
                // transforma string base64 em estrutura esperada
                $normalized[] = ['file' => $item];
            } elseif (is_array($item)) {
                $normalized[] = $item;
            }
        }

        if (!empty($normalized)) {
            $this->attachments->createComplaintAttachment($normalized, $complaintId);
        }
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

    /**
     * Retorna denúncia pelo código
     */
    public function getByCode(string $code): ?Complaint
    {
        return $this->model::where('code', $code)->first();
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
        return $this->model::select('type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('type')
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
