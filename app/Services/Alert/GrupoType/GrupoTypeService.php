<?php
namespace App\Services\Alert\GrupoType;

use App\Repositories\Alert\GrupoType\GrupoTypeRepository;
use App\Repositories\Complaint\TypeComplaintsRepository;
use App\Services\AbstractService;

class GrupoTypeService extends AbstractService
{
    public  $typeComplaintsRepository;
    public function __construct(GrupoTypeRepository $repository, TypeComplaintsRepository $typeComplaintsRepository)
    {
        $this->typeComplaintsRepository = $typeComplaintsRepository;
        parent::__construct($repository);
    }
    
    public function listTypGrup()
    {
        return $this->typeComplaintsRepository->getAll();
    }
    
}