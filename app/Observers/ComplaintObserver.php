<?php

namespace App\Observers;

use App\Actions\StatusAction;
use App\Mail\ComplaintNeedMoreInfoMail;
use App\Mail\ComplaintUpdatedMail;
use App\Models\Complaint\Complaint;
use Illuminate\Support\Facades\Mail;

class ComplaintObserver
{
    public function updated(Complaint $complaint): void
    {
        // Só dispara se o status mudou
        if (!$complaint->wasChanged('status')) {
            return;
        }

        $status = $complaint->status;

        try {
            // 2. Define o destinatário
            $recipient = $this->determineRecipient($complaint);

            if (!$recipient) {
                logs()->warning("Observer: Nenhum destinatário encontrado para Reclamação #{$complaint->code}");
                return;
            }

            // 3. Busca a última triagem
            $latestTriage = $complaint->triages()->latest()->first();

            // Log para depuração da triagem
            if (!$latestTriage) {
                logs()->info("Observer: Nenhuma triagem encontrada para Reclamação #{$complaint->code}");
                return;
            }

            // 4. Fluxo Pragmático com Operadores Ternários
            $isActionable = $latestTriage && ($latestTriage->is_returned || $latestTriage->is_refused);

            // Se for status relacionado ao provedor, não é acionável para o reclamante
            $isActionable = str_contains($status->value, 'Provedor') ? false : $isActionable;

            // 5. Envia o e-mail apropriado com base na triagem
            $isActionable
                ? Mail::to($recipient)->queue(
                    new ComplaintNeedMoreInfoMail(
                        $complaint,
                        $latestTriage,
                        $latestTriage->is_refused ? 'refusal' : 'return'
                    )
                )
                : Mail::to($recipient)->queue(
                    new ComplaintUpdatedMail(
                        $complaint,
                        StatusAction::getStatusDescription($status),
                        StatusAction::getStatusSubject($status, $complaint->code)
                    )
                );

            logs()->info("E-mail processado via triagem para {$recipient} (Protocolo #{$complaint->code})");
        } catch (\Exception $e) {
            logs()->error("Erro crítico no ComplaintObserver: " . $e->getMessage(), [
                'complaint_id' => $complaint->id
            ]);
        }
    }

    private function determineRecipient(Complaint $complaint): ?string
    {
        // Se a reclamação foi encaminhada para o provedor, o e-mail já foi enviado no momento do encaminhamento
        // portanto, o email do reclamante é o destinatário para notificações futuras
        return $complaint->email;
    }
}
