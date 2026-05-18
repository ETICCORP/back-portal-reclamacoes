<?php

namespace App\Repositories\Complaint;

use App\Models\Complaint\ComplaintDeadline;
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
     * Prolonga o prazo de resposta de uma reclamação ativa (Apenas uma única vez).
     *
     * @param int $complaintId ID da reclamação
     * @param int $additionalDays Dias úteis a somar
     * @param string|null $reason Motivo do prolongamento
     * @return bool
     * @throws \Exception
     */
    public function extendDeadline(int $complaintId, int $additionalDays, ?string $reason = null): bool
    {
        return DB::transaction(function () use ($complaintId, $additionalDays, $reason) {

            // 1. Procura o prazo atual ativo da reclamação
            $deadline = $this->model
                ->where('complaint_id', $complaintId)
                ->where('status', 'Pendente')
                ->first();

            if (!$deadline) {
                throw new \Exception("Não existe nenhum prazo pendente ativo para esta reclamação.");
            }

            // 🔒 Permitir o prolongamento APENAS uma vez
            $logsCount = \App\Models\Complaint\ComplaintDeadlineLog::where('complaint_deadline_id', $deadline->id)->count();

            if ($logsCount > 1) {
                throw new \Exception("Ação recusada. O prazo desta reclamação já foi prolongado anteriormente.");
            }

            // 2. Validação cronológica: Verifica se o prazo original já expirou
            $endDateOriginal = Carbon::parse($deadline->end_date);

            if (Carbon::now()->greaterThan($endDateOriginal)) {
                throw new \Exception("Ação não permitida. O prazo original deste processo já expirou.");
            }

            // 3. Calcula a nova data limite com base nos dias úteis
            $newEndDate = $this->calculateBusinessDays($endDateOriginal, $additionalDays);

            // 💡 AJUSTE DE SEGURANÇA: Injeta o atributo na memória e usa o save() para o SQL ignorar a coluna
            $deadline->ext_reason = $reason;
            $deadline->days       = $deadline->days + $additionalDays;
            $deadline->end_date   = $newEndDate;

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
