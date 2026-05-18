<?php

namespace App\Observers;

use App\Enum\ClaimStatus;
use App\Mail\ComplaintNeedMoreInfoMail;
use App\Mail\ComplaintUpdatedMail;
use App\Models\Complaint\Complaint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class ComplaintObserver
{

    /**
     * Interceta o processo ANTES da gravação no banco de dados.
     */
    /**
     * Interceta o processo ANTES da gravação no banco de dados.
     */
    public function updating(Complaint $complaint): void
    {
        $statusOriginal = $complaint->getOriginal('status');

        // Se o status for do tipo Enum, extraímos o valor string, caso contrário usamos diretamente
        $statusValue = $statusOriginal instanceof ClaimStatus
            ? $statusOriginal->value
            : $statusOriginal;

        // Lista estrita de campos cadastrais que pertencem exclusivamente à retificação do reclamante
        $camposCadastraisReclamante = [
            'full_name',
            'complainant_role',
            'source',
            'location',
            'type',
            'policy_number',
            'entity',
            'description',
            'incidentDateTime',
            'representative',
        ];

        // Atributos técnicos padrão que o Laravel altera automaticamente ou que controlam o fluxo
        $atributosTecnicosPermitidos = ['status', 'updated_at'];

        // Captura todos os atributos que estão a ser modificados nesta requisição
        $camposAlterados = array_keys($complaint->getDirty());

        // ---------------------------------------------------------------------
        // CASO 1: O processo ESTÁ em "Devolvida ao Reclamante"
        // ---------------------------------------------------------------------
        if ($statusValue === ClaimStatus::DEVOLVIDA_RECLAMANTE->value) {

            // Juntamos o cadastro com as flags técnicas para o array de total de permitidos
            $camposPermitidosNesteStatus = array_merge($camposCadastraisReclamante, $atributosTecnicosPermitidos);

            foreach ($camposAlterados as $campo) {
                // Se tentar mudar algo do sistema (ex: user_id, internal_notes) neste status, barra!
                if (!in_array($campo, $camposPermitidosNesteStatus)) {
                    throw ValidationException::withMessages([
                        'autorizacao' => ["Ação não permitida. No estado de devolução, apenas os dados cadastrais da exposição podem ser corrigidos."]
                    ]);
                }
            }

            return; // Regra processada com sucesso para este status, sai do método.
        }

        // ---------------------------------------------------------------------
        // CASO 2: O processo NÃO ESTÁ em "Devolvida ao Reclamante" (Qualquer outro status)
        // ---------------------------------------------------------------------
        foreach ($camposAlterados as $campo) {
            // Se tentar mexer em qualquer dado do reclamante quando o processo está Pendente/Em Análise, barra!
            if (in_array($campo, $camposCadastraisReclamante)) {
                throw ValidationException::withMessages([
                    'autorizacao' => ["Os dados cadastrais desta exposição encontram-se bloqueados para edição no estado atual do processo."]
                ]);
            }
        }
    }

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
                $frontendUrl = self::generateFrontendSignedUrl($complaint, 5);
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
        $frontendUrl = config('app.frontend_url');

        // 4. Monta o link final que o React vai ler
        return rtrim($frontendUrl, '/') . "/reclamacoes/editar/{$complaint->id}?{$queryString}";
    }
}
