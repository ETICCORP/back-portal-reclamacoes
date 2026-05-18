<?php

namespace App\Repositories\ComplaintTriages;

use App\Models\ComplaintTriages\ComplaintTriages;
use App\Repositories\AbstractRepository;
use App\Repositories\Complaint\ComplaintRepository;
use App\Enum\ClaimStatus;

class ComplaintTriagesRepository extends AbstractRepository
{
    public $complaintRepository;
    public function __construct(ComplaintTriages $model, ComplaintRepository $complaintRepository)
    {
        parent::__construct($model);
        $this->complaintRepository = $complaintRepository;
    }

    public function store(array $data)
    {
        // 1. Determina o Enum de status com base nas regras de negócio
        $status = ClaimStatus::APROVADA_CLASSIFICACAO;

        if (data_get($data, 'is_refused')) {
            $status = ClaimStatus::NEGADA_CLASSIFICACAO;
        } elseif (data_get($data, 'is_returned')) {
            $status = ClaimStatus::DEVOLVIDA_RECLAMANTE;
        }

        // 2. Prepara os dados. Usamos '->value' para salvar a string original no banco
        $data['status']  = $status->value;
        $data['comment'] = "Classificação da Triagem: " . $status->value;

        // Cria o registro na tabela complaint_triages
        $model = $this->model->create($data);

        // 3. Atualiza o status da reclamação relacionada
        if (!empty($data['complaint_id'])) {
            // Passamos o valor do enum ('Aprovada Classificação', etc) para manter a compatibilidade
            $data['status'] = $status->value;

            $this->complaintRepository->updateStatus(
                $data,
                $data['complaint_id']
            );
        }

        return $model;
    }
}
