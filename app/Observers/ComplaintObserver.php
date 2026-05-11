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

        // 1. Obtemos a mensagem amigável da Action (que já configuramos anteriormente)
        $statusDescription = StatusAction::getStatusDescription($complaint->status);

        // 2. Definimos o assunto dinamicamente incluindo os novos estados
        $subject = match ($complaint->status) {
            "Aprovada Classificação"   => "Exposição Aprovada para Análise",
            "Negada Classificação"     => "Atualização: Exposição Não Classificada",
            "Devolvida ao Reclamante"  => "Ação Necessária: Complemento de Informações",
            "Devolvida ao Provedor"    => "Reclamação Reencaminhada para Revisão",
            "Respondida ao Reclamante" => "Resposta Final Disponível",
            "Encaminhado ao Provedor"  => "A sua exposição foi encaminhada à instituição",
            default                    => "Atualização de Status: Protocolo #{$complaint->code}"
        };

        // 3. Lógica de destino (Quem deve receber o e-mail?)
        $recipient = $this->determineRecipient($complaint);

        if ($recipient) {
            Mail::to($recipient)->send(
                new ComplaintUpdatedMail($complaint, $statusDescription, $subject)
            );
        }
    }

    /**
     * Determina quem deve receber a notificação com base no status.
     */
    private function determineRecipient(Complaint $complaint): ?string
    {
        // Se foi devolvida ao provedor, o e-mail deve ir para o e-mail do Provedor vinculado
        if ($complaint->status === "Devolvida ao Provedor") {
            return $complaint->forwardProvider?->provider?->email ?? null;
        }

        // Por padrão, envia para o reclamante
        return $complaint->email;
    }
}
