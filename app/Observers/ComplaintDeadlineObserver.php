<?php

namespace App\Observers;

use App\Models\Complaint\ComplaintDeadline;
use App\Models\Complaint\ComplaintDeadlineLog;
use Illuminate\Support\Facades\Auth;

class ComplaintDeadlineObserver
{
    /**
     * Evento disparado imediatamente APÓS o prazo ser criado no banco de dados.
     * (Cria o Item 0 de Auditoria)
     */
    public function created(ComplaintDeadline $deadline): void
    {
        ComplaintDeadlineLog::create([
            'complaint_deadline_id' => $deadline->id,
            'days'                  => $deadline->days,
            'start_date'            => $deadline->start_date,
            'end_date'              => $deadline->end_date,
            'status'                => $deadline->status,
            'reason'                => 'Processo submetido e prazo inicial de resposta estabelecido institucionalmente.',
            'user_id'               => Auth::id(),
        ]);
    }

    /**
     * Evento disparado imediatamente ANTES de gravar qualquer alteração (Update).
     * Captura a razão temporária injetada dinamicamente pelo repositório.
     */
    public function updating(ComplaintDeadline $deadline): void
    {
        // Só gera log se campos críticos de prazo ou estado forem realmente modificados
        if ($deadline->isDirty(['days', 'end_date', 'status'])) {

            // Recupera a razão que injetámos dinamicamente no objeto da model no repositório
            $reason = $deadline->ext_reason ?? 'Alteração de parâmetros de prazo do processo.';

            logs()->info('observer executado');

            // Removemos o atributo da lista de colunas do Eloquent para o SQL ignorá-lo
            unset($deadline->ext_reason);

            ComplaintDeadlineLog::create([
                'complaint_deadline_id' => $deadline->id,
                'days'                  => $deadline->days,
                'start_date'            => $deadline->start_date,
                'end_date'              => $deadline->end_date,
                'status'                => $deadline->status,
                'reason'                => $reason,
                'user_id'               => Auth::id(), // Técnico autenticado que aprovou o prolongamento
            ]);
        }
    }
}
