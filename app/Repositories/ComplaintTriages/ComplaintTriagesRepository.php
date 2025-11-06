<?php
namespace App\Repositories\ComplaintTriages;

use App\Models\ComplaintTriages\ComplaintTriages;
use App\Repositories\AbstractRepository;
use App\Repositories\Complaint\ComplaintRepository;

class ComplaintTriagesRepository extends AbstractRepository
{ public $complaintRepository;
    public function __construct(ComplaintTriages $model,ComplaintRepository $complaintRepository)
    {
        parent::__construct($model);
        $this->complaintRepository = $complaintRepository;
    }

    public function store(array $data)
    {
        if (!empty($data['is_refused']) && $data['is_refused'] === true) {
            $data['status'] = "Negada Classificação";
        } else {
            $data['status'] = "Aprovada Classificação";
        }
    
       
            $data['comment'] = "Classificação da Triagem: " . ($data['is_refused'] ? "Negada" : "Aprovada");
        
    
        // Cria a triagem com todos os dados já definidos
        $triagem = $this->model->create($data);
    
        // Atualiza o status da reclamação relacionada (se existir complaint_id)
        if (!empty($data['complaint_id'])) {
            $this->complaintRepository->updateStatus($data, $data['complaint_id']);
        }
    
        return $triagem;
    }
}