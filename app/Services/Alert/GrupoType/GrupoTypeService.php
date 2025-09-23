<?php
namespace App\Services\Alert\GrupoType;

use App\Repositories\Alert\GrupoType\GrupoTypeRepository;
use App\Services\AbstractService;

class GrupoTypeService extends AbstractService
{
    public function __construct(GrupoTypeRepository $repository)
    {
        parent::__construct($repository);
    }
    public function listTypGrup(){
        return [
            ['name' => "PEP", 'info_pt' => "Pessoa Politicamente Exposta"],
            ['name' => "Sanctions List", 'info_pt' => "Lista de Sanções"],
            ['name' => "Transaction Monitoring", 'info_pt' => "Monitoramento de Transações"],
            ['name' => "Customer Profile and Behavior", 'info_pt' => "Perfil e Comportamento do Cliente"],
            ['name' => "Linked to Award and Payment", 'info_pt' => "Ligados ao Prémio e Pagamento"],
            ['name' => "Regulatory Compliance", 'info_pt' => "Correspondência com listas de sanções internacionais"],
        ];
        
    }
}