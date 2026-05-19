<?php

namespace App\Repositories\Complaint;

use App\Enum\ClaimStatus;
use App\Models\Complaint\ComplaintDeadline;
use App\Models\Complaint\ComplaintDeadlineLog;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ComplaintDeadlineRepository extends AbstractRepository
{
    public function __construct(ComplaintDeadline $model)
    {
        parent::__construct($model);
    }


    /**
     * Prolonga o prazo de resposta com base no contexto/fase atual do processo.
     */
    public function extendDeadline(int $complaintId, int $additionalDays, ?string $reason = null): bool
    {
        return DB::transaction(function () use ($complaintId, $additionalDays, $reason) {

            // 1. Procura o prazo atual ativo da reclamação
            $deadline = $this->model
                ->with('complaint')
                ->where('complaint_id', $complaintId)
                ->where('status', 'Pendente') // O prazo em si continua com controle de fluxo pendente
                ->first();

            if (!$deadline) {
                throw new \Exception("Não existe nenhum prazo pendente ativo para esta reclamação.");
            }

            // 🔒 REGRA EXTRA: Não pode estender se foi negado na classificação
            if ($deadline->complaint && $deadline->complaint->status === ClaimStatus::NEGADA_CLASSIFICACAO) {
                throw new \Exception("Ação recusada. Não é permitido prolongar o prazo de uma reclamação que foi negada.");
            }

            // 🔒 IDEMPOTÊNCIA POR FASE (Status da Reclamação Principal)
            // Captura o status atual da reclamação principal (ex: 'PENDENTE_PT' ou 'ENCAMINHADO_PROVEDOR')
            $currentComplaintStatus = $deadline->complaint->status->value;

            // Contamos quantos logs já foram criados para este prazo limite especificamente nesta fase (status)
            $phaseLogsCount = ComplaintDeadlineLog::where('complaint_deadline_id', $deadline->id)
                ->where('status', $currentComplaintStatus)
                ->count();

            // Explicação matemática:
            // Quando a fase inicia, o Observer grava o log inicial da fase (count = 1).
            // Se tentar estender, o count é 1, então permite. Após estender, o count passa a 2.
            // Se tentar estender uma segunda vez na mesma fase, o count será > 1 e o sistema barra.
            if ($phaseLogsCount > 1) {
                throw new \Exception("Ação recusada. O prazo para a fase atual ({$currentComplaintStatus}) já foi prolongado anteriormente.");
            }

            // 2. Validação cronológica: Verifica se o prazo original já expirou
            $endDateOriginal = Carbon::parse($deadline->end_date);

            if (Carbon::now()->greaterThan($endDateOriginal)) {
                throw new \Exception("Ação não permitida. O prazo original deste processo já expirou.");
            }

            // 3. Atualiza os dados injetando na memória para o Observer ler
            $deadline->ext_reason = $reason;
            $deadline->days       = $deadline->days + $additionalDays;
            $deadline->end_date   = $this->calculateBusinessDays($endDateOriginal, $additionalDays);

            return $deadline->save();
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

    public function percentageServicedWithinDeadline()
    {
        $dados = DB::table('complaint_deadlines')
            ->select(
                DB::raw("YEAR(start_date) as ano"),

                DB::raw("SUM(CASE WHEN status = 'concluido' AND updated_at <= end_date THEN 1 ELSE 0 END) as percentage"),
                DB::raw("COUNT(*) as total"),
                DB::raw("ROUND(SUM(CASE WHEN status = 'concluido' AND updated_at <= end_date THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as percentage")
            )
            ->where('status', '!=', 'expirado')
            ->groupBy(DB::raw("YEAR(start_date), MONTH(start_date)"))
            ->orderBy(DB::raw("YEAR(start_date), MONTH(start_date)"))
            ->get();

        setlocale(LC_TIME, 'pt_PT.UTF-8');

        foreach ($dados as $dado) {
            $dado->mes = Carbon::createFromDate($dado->ano,  1)
                ->locale('pt_PT')
                ->translatedFormat('F');
        }

        return $dados;
    }
}
