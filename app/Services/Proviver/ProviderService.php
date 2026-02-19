<?php
namespace App\Services\Proviver;

use App\Repositories\Proviver\ProviderRepository;
use App\Services\AbstractService;

class ProviderService extends AbstractService
{
    public function __construct(ProviderRepository $repository)
    {
        parent::__construct($repository);
    }


     public function index(?int $paginate, ?array $filterParams, ?array $orderByParams, $relationships = [])
    {

        
        return $this->repository->index($paginate, $filterParams, $orderByParams, $relationships);
    }
}