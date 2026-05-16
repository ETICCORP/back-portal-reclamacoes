<?php

namespace App\Repositories\ComplaintTriages;

use App\Models\ComplaintTriages\ComplaintTriages;
use App\Repositories\AbstractRepository;
use App\Repositories\Complaint\ComplaintRepository;

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
        // Determina a etiqueta de status para o comentário interno
        $label = "Aprovada Classificação";

        if (data_get($data, 'is_refused')) {
            $label = "Negada Classificação";
        } elseif (data_get($data, 'is_returned')) {
            $label = "Devolvida ao Reclamante";
        }

        // Prepara dados específicos do registro de triagem
        $data['status'] = $label;
        $data['comment'] = "Classificação da Triagem: " . $label;

        // Cria o registro na tabela complaint_triages
        $model = $this->model->create($data);

        // Atualiza o status da reclamação relacionada (se existir complaint_id)
        if (!empty($data['complaint_id'])) {

            $this->complaintRepository->updateStatus(
                $data,
                $data['complaint_id']
            );
        }

        return $model;
    }
}
