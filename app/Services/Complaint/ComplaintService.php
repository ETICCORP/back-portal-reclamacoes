<?php

namespace App\Services\Complaint;

use App\Enum\ClaimStatus;
use App\Mail\ComplaintUpdatedAnalistaMail;
use App\Mail\ComplaintUpdatedReclamanteMail;
use App\Repositories\Complaint\ComplaintRepository;
use App\Repositories\Complaintattachment\ComplaintattachmentRepository;
use App\Services\AbstractService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ComplaintService extends AbstractService
{
    private ComplaintattachmentRepository $complaintattachmentRepository;

    public function __construct(
        ComplaintRepository $repository,
        ComplaintattachmentRepository $complaintattachmentRepository
    ) {
        $this->complaintattachmentRepository = $complaintattachmentRepository;
        parent::__construct($repository);
    }

    public function storeData(array $data)
    {
        return $this->repository->storeData($data);
    }


    public function updateData(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            // 1. Localiza o registo atual na base de dados para validar o estado
            $complaint = $this->repository->findOrFail($id);

            // VALIDAÇÃO DE ESTADO: O reclamante só pode retificar se o processo estiver devolvido
            // Comparamos o valor string do Enum para evitar problemas de tipagem
            $currentStatus = $complaint->status instanceof ClaimStatus ? $complaint->status->value : $complaint->status;

            if ($currentStatus !== ClaimStatus::DEVOLVIDA_RECLAMANTE->value) {
                throw new \Exception("Ação recusada. Esta reclamação não se encontra pendente de retificação pelo reclamante.");
            }

            // 2. Passou a validação? Atualiza os dados cadastrais na tabela principal
            $complaint = $this->update($data, $id);

            // 3. Atualiza o status para Pendente (Silent mode ativo)
            $this->repository->updateStatus([
                'status'  => ClaimStatus::PENDENTE_PT->value,
                'comment' => 'O reclamante atualizou os dados cadastrais da sua exposição.'
            ], (int) $id, true);

            // 4. Envia a confirmação exclusiva e controlada para o reclamante
            Mail::to($complaint->email)
                ->queue(new ComplaintUpdatedReclamanteMail($complaint));

            logs()->info('E-mail enviado para o reclamante: ' . $complaint->email);

            // 5. Localiza o analista responsável através da relação de triagem
            $lastTriage = $complaint->triages()->latest()->with('assignedUser')->first();
            $analista   = $lastTriage?->assignedUser;

            if ($analista && !empty($analista->email)) {
                // Se encontrou o analista que fez a devolução, envia diretamente para ele
                Mail::to($analista->email)
                    ->queue(new ComplaintUpdatedAnalistaMail($complaint, $analista));

                logs()->info("E-mail de retificação enviado ao analista responsável ({$analista->email})");
            }

            return $complaint;
        });
    }


    public function showFile($id)
    {
        return $this->complaintattachmentRepository->showFile($id);
    }

    public function total()
    {
        return $this->repository->total();
    }

    public function timeResponse()
    {
        return $this->repository->timeResponse();
    }
    public function totalForCurrentWeek()
    {
        return $this->repository->totalForCurrentWeek();
    }
    public function totalForLastWeek()
    {
        return $this->repository->totalForLastWeek();
    }
    public function getTopTypes()
    {
        return $this->repository->getTopTypes();
    }
    public function getBycode($code)
    {
        return $this->repository->getBycode($code);
    }

    public function countByDate(?string $startDate, ?string $endDate)
    {
        $startDate = $startDate ?? now()->subDays(7)->toDateString();
        $endDate   = $endDate ?? now()->toDateString();
        return $this->repository->countByDate($startDate, $endDate);
    }

    public function updateStatus(array $data, $id)
    {
        return $this->repository->updateStatus($data, $id);
    }

    public function byManth()
    {
        return $this->repository->byManth();
    }
    public function repeatOffenders()
    {
        return $this->repository->repeatOffenders();
    }
}
