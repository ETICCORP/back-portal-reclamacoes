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

    public function forwardComplaint(array $data)
    {
        $complaintProvider = null;

        try {
            DB::beginTransaction();

            $complaintProvider = $this->model->create([
                'complaint_id' => $data['complaint_id'],
                'provider_id'  => $data['provider_id'],
                'summary'      => $data['summary'],
                'notes'        => $data['notes'],
                'sent_at'      => now(),
                'deadline'     => Carbon::now()->addWeekdays(20),
                'status'       => 'sent'
            ]);

            // 1. Centralizando o status com o Enum
            $status = ClaimStatus::ENCAMINHADO_PROVEDOR;

            $data['status']  = $status->value; // 'Encaminhado ao Provedor'
            $data['comment'] = $data['notes'];

            // Atualiza o status da reclamação principal
            $this->complaintRepository->updateStatus($data, $data['complaint_id']);

            // Carrega as relações necessárias para o envio do e-mail
            $complaintProvider->load([
                "complaint",
                "provider"
            ]);

            DB::commit();

            // Envio de e-mail ao Provedor
            Mail::to($complaintProvider->provider->email)
                ->queue(new ComplaintForwardedMail($complaintProvider));

            logs()->info('Reclamação encaminhada para o provedor', [
                'email do provedor' => $complaintProvider->provider->email,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($complaintProvider)) {
                logs()->error('Erro ao encaminhar para o provedor', [
                    'email do provedor'     => $complaintProvider->provider?->email ?? 'Não disponível',
                    'complaint_provider_id' => $complaintProvider->id,
                    'error'                 => $e->getMessage(),
                ]);
            }

            throw new \Exception('Erro ao encaminhar para o provedor 2');
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
