<?php

namespace App\Observers;

use App\Models\Complaint\Complaint;
use App\Mail\ComplaintUpdatedMail;
use App\Actions\StatusAction;
use Illuminate\Support\Facades\Mail;

class ComplaintObserver
{
    public function updated(Complaint $complaint): void
    {
        // Só dispara se o status mudou
        if (!$complaint->isDirty('status')) {
            return;
        }

        // 1. Obtemos a mensagem amigável da Action
        $statusDescription = StatusAction::getStatusDescription($complaint->status);

        // 2. Definimos o assunto dinamicamente
        $subject = match ($complaint->status) {
            "Aprovada Classificação"   => "Exposição Aprovada para Análise",
            "Negada Classificação"     => "Atualização: Exposição Não Classificada",
            "Respondida ao Reclamante" => "Resposta Final Disponível",
            default                    => "Atualização de Status: Protocolo #{$complaint->code}"
        };

        // 3. Enviamos o e-mail
        if ($complaint->email) {
            Mail::to($complaint->email)->send(
                new ComplaintUpdatedMail($complaint, $statusDescription, $subject)
            );
        }
    }
}
