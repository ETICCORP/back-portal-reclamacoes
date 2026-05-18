<?php

namespace App\Repositories\Complaint\Proviver;

use App\Enum\ClaimStatus;
use App\Mail\ComplaintForwardedMail;
use App\Models\Complaint\Proviver\ComplaintProvider;
use App\Repositories\AbstractRepository;
use App\Repositories\Complaint\ComplaintRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class ComplaintProviderRepository extends AbstractRepository
{
    public $complaintRepository;

    public function __construct(ComplaintProvider $model, ComplaintRepository $complaintRepository)
    {
        parent::__construct($model);
        $this->complaintRepository = $complaintRepository;
    }

    /**
     * Encaminha de forma idempotente e segura uma reclamação para um provedor técnico.
     * * @param array $data ['complaint_id', 'provider_id', 'summary', 'notes']
     * @return \App\Models\Complaint\Proviver\ComplaintProvider
     * @throws \Exception
     */
    public function forwardComplaint(array $data)
    {
        // 1. Localiza a Reclamação Principal com segurança
        $complaint = $this->complaintRepository->model->find($data['complaint_id']);

        if (!$complaint) {
            throw new \Exception('A reclamação indicada não foi encontrada no sistema.');
        }

        // 🔍 REGRA 1: Validação de Triagem Obrigatória
        // Verifica se existe algum registo na relação hasMany 'triages'
        if ($complaint->triages()->count() === 0) {
            throw new \Exception('Operação rejeitada. Não é possível encaminhar uma reclamação que não passou pelo processo de triagem.');
        }

        // 🔄 REGRA 2: Idempotência Estrita (Evitar duplicados)
        // Procura se já existe um encaminhamento idêntico e pendente ativo ('sent')
        $existingForward = $this->model
            ->where('complaint_id', $data['complaint_id'])
            ->where('provider_id', $data['provider_id'])
            ->where('status', 'sent')
            ->first();

        if ($existingForward) {
            logs()->notice('Encaminhamento ignorado por duplicidade (Idempotência ativa)', [
                'complaint_id' => $data['complaint_id'],
                'provider_id'  => $data['provider_id']
            ]);

            // Devolve o registo que já existia sem duplicar dados na tabela nem reenviar emails
            return $existingForward->load(['complaint', 'provider']);
        }

        $complaintProvider = null;

        try {
            DB::beginTransaction();

            // 2. Criação do registo de encaminhamento
            $complaintProvider = $this->model->create([
                'complaint_id' => $data['complaint_id'],
                'provider_id'  => $data['provider_id'],
                'summary'      => $data['summary'],
                'notes'        => $data['notes'],
                'sent_at'      => now(),
                'deadline'     => Carbon::now()->addWeekdays(20),
                'status'       => 'sent'
            ]);

            // 3. Centralização do status com o Enum mapeado na Model
            $status = ClaimStatus::ENCAMINHADO_PROVEDOR;

            $updateData = [
                'status'  => $status->value, // Garante a string/int equivalente do Enum
                'comment' => $data['notes']
            ];

            // Atualiza o status da reclamação principal na tabela de histórico/status
            $this->complaintRepository->updateStatus($updateData, $complaint->id);

            // Carrega as relações necessárias para o disparo seguro do Mail Event
            $complaintProvider->load(["complaint", "provider"]);

            DB::commit();

            // 4. Envio Assíncrono do E-mail (Fora da transação SQL para evitar deadlocks de fila)
            if ($complaintProvider->provider && !empty($complaintProvider->provider->email)) {
                Mail::to($complaintProvider->provider->email)
                    ->queue(new ComplaintForwardedMail($complaintProvider));
            }

            logs()->info('Reclamação encaminhada para o provedor com sucesso.', [
                'complaint_id'      => $complaint->id,
                'email_provedor'    => $complaintProvider->provider?->email,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            logs()->error('Falha crítica ao encaminhar processo para o provedor', [
                'complaint_id' => $data['complaint_id'] ?? null,
                'error'        => $e->getMessage(),
                'trace'        => $e->getTraceAsString()
            ]);

            throw new \Exception('Erro interno ao processar o encaminhamento para o provedor.');
        }

        return $complaintProvider;
    }

    public function forward()
    {
        return  $this->model::count();
    }

    public function providersManth()
    {
        $complaintsByMonth = $this->model::select(
            DB::raw("DATE_FORMAT(created_at, '%M') as month"), // nome do mês
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($complaintsByMonth);
    }
}
