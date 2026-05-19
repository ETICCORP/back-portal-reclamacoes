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

    /**
     * Regista a triagem inicial de uma reclamação (Operação Idempotente Única).
     *
     * @param array $data
     * @return ComplaintTriages
     * @throws \Exception
     */
    public function store(array $data)
    {
        $complaintId = data_get($data, 'complaint_id');

        if (empty($complaintId)) {
            throw new \Exception("Erro de integridade. O ID da reclamação é obrigatório para realizar a triagem.");
        }

        // 🔒 REGRA DE OURO: A triagem só pode acontecer uma única vez por processo
        // Fazemos um check de existência direto e ultra-rápido na base de dados
        $alreadyTriaged = $this->model->where('complaint_id', $complaintId)->exists();

        if ($alreadyTriaged) {

            logs()->notice("Tentativa de triagem duplicada bloqueada (Idempotência ativa)", [
                'complaint_id' => $complaintId
            ]);

            throw new \Exception("Ação recusada. Esta reclamação já passou pelo processo de triagem e não permite reclassificação.");
        }

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
        // Passamos o valor do enum ('Aprovada Classificação', etc) para manter a compatibilidade
        $data['status'] = $status->value;

        $this->complaintRepository->updateStatus(
            $data,
            $complaintId
        );

        return $model;
    }
}
