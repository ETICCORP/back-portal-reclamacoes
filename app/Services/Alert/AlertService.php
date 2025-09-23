<?php

namespace App\Services\Alert;

use App\Services\AbstractService;
use App\Repositories\Alert\AlertRepository;
use App\Repositories\Entities\EntitiesRepository;
use App\Repositories\Entities\BeneficialOwnerRepository;


class AlertService extends AbstractService
{
    public function __construct(
        AlertRepository $repository,
        private EntitiesRepository $entitiesRepository,
        private BeneficialOwnerRepository $beneficialOwnerRepository
    ) {
        parent::__construct($repository);
    }

    public function index(?int $paginate, ?array $filterParams, ?array $orderByParams, $relationships = [])
    {
        $relationships =  [
            'entity:id,social_denomination,customer_number,policy_number'
        ];
        $orderByParams = $orderByParams ?? ['created_at' => 'desc'];
        return $this->repository->index($paginate, $filterParams, $orderByParams, $relationships);
    }
    public function getTotalAlerts(): array
    {
        return $this->repository->getTotalAlerts();
    }

}
