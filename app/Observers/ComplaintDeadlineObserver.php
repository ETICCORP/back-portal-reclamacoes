<?php

namespace App\Observers;

use App\Enum\ClaimStatus;
use App\Models\Complaint\ComplaintDeadline;
use App\Models\Complaint\ComplaintDeadlineLog;
use Illuminate\Support\Facades\Auth;

class ComplaintDeadlineObserver
{
    /**
     * Evento disparado imediatamente APÓS o prazo ser criado no banco de dados.
     * (Cria o Item 0 de Auditoria)
     */
    /**
     * Item 0 da Fase Inicial (PENDENTE_PT)
     */
    public function created(ComplaintDeadline $deadline): void
    {
        // Carrega a reclamação caso não esteja na memória
        $deadline->load('complaint');

        ComplaintDeadlineLog::create([
            'complaint_deadline_id' => $deadline->id,
            'days'                  => $deadline->days,
            'start_date'            => $deadline->start_date,
            'end_date'              => $deadline->end_date,
            'status'                => $deadline->complaint->status->value ?? ClaimStatus::PENDENTE_PT, // Guarda a fase atual
            'reason'                => 'Processo submetido e prazo inicial de resposta estabelecido institucionalmente.',
            'user_id'               => Auth::id(),
        ]);
    }

    /**
     * Evento disparado imediatamente ANTES de gravar qualquer alteração (Update).
     * Captura a razão temporária injetada dinamicamente pelo repositório.
     */
/**
     * Executado quando o prazo sofre o prolongamento de 5 dias
     */
    public function updating(ComplaintDeadline $deadline): void
    {
        if ($deadline->isDirty(['days', 'end_date'])) {
            
            $deadline->load('complaint');
            $reason = $deadline->ext_reason ?? 'Alteração de parâmetros de prazo do processo.';

            // Limpa o atributo dinâmico para evitar o erro de SQL Column Not Found
            unset($deadline->ext_reason);

            ComplaintDeadlineLog::create([
                'complaint_deadline_id' => $deadline->id,
                'days'                  => $deadline->days,
                'start_date'            => $deadline->start_date,
                'end_date'              => $deadline->end_date,
                'status'                => $deadline->complaint->status->value, // Vincula o log à fase atual
                'reason'                => $reason,
                'user_id'               => Auth::id(),
            ]);
        }
    }
}
