<?php
namespace App\Services\Alert\UserGrupoAlert;

use App\Repositories\Alert\UserGrupoAlert\UserGrupoAlertRepository;
use App\Services\AbstractService;

class UserGrupoAlertService extends AbstractService
{
    public function __construct(UserGrupoAlertRepository $repository)
    {
        parent::__construct($repository);
    }

    public function storeMany(array $data)
    {
        return $this->repository->storeMany($data);
    
    }
}