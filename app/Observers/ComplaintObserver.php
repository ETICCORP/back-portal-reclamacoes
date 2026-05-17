<?php

namespace App\Observers;

use App\Enum\ClaimStatus;
use App\Mail\ComplaintNeedMoreInfoMail;
use App\Mail\ComplaintUpdatedMail;
use App\Models\Complaint\Complaint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ComplaintObserver
{
    public function updated(Complaint $complaint): void
    {
        // Só dispara se o status mudou
        if (!$complaint->wasChanged('status')) {
            return;
        }

        $status = $complaint->status instanceof ClaimStatus
            ? $complaint->status
            : ClaimStatus::tryFrom($complaint->status);

        if (!$status) {
            logs()->warning("Observer: Status inválido ou não mapeado recebido.");
            return;
        }

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

            // Se o status for Devolvida ao Reclamante, geramos a URL assinada para o React
            $frontendUrl = null;

            if ($status === ClaimStatus::DEVOLVIDA_RECLAMANTE) {
                $frontendUrl = $this->generateFrontendSignedUrl($complaint, 5);
            }

            // 5. Envia o e-mail apropriado com base na triagem utilizando o Enum
            $mailable = $isActionable
                ? new ComplaintNeedMoreInfoMail(
                    $complaint,
                    $latestTriage,
                    $status === ClaimStatus::NEGADA_CLASSIFICACAO ? 'refusal' : 'return',
                    $frontendUrl
                )
                : new ComplaintUpdatedMail(
                    $complaint,
                    $status->getDescription(),
                    $status->getSubject($complaint->code)
                );

            Mail::to($recipient)->queue($mailable);

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

    /**
     * Gera um link assinado temporário que aponta para o Front-end.
     *
     * @param Complaint $complaint
     * @param int $days Dias de validade do link
     * @return string URL completa do front-end com os tokens de assinatura da API
     */
    public static function generateFrontendSignedUrl(Complaint $complaint, int $days = 5): string
    {
        // 1. Gera a rota assinada relativa correspondente ao endpoint do Back-end
        $relativeApiUrl = URL::temporarySignedRoute(
            'complaints.update', // Nome da rota PUT definida no routes/api.php
            now()->addDays($days),
            ['id' => $complaint->id],
            false // Crucial: gera apenas o caminho relativo (/api/reports/...)
        );

        // 2. Extrai exclusivamente os parâmetros gerados (?expires=...&signature=...)
        $queryString = parse_url($relativeApiUrl, PHP_URL_QUERY);

        // 3. Recupera o domínio do Front-end do ficheiro .env
        $frontendUrl = env('FRONTEND_URL', 'https://keepcomply.co.ao');

        // 4. Monta o link final que o React vai ler
        return rtrim($frontendUrl, '/') . "/reclamacoes/editar/{$complaint->id}?{$queryString}";
    }
}
