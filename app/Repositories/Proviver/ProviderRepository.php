<?php
namespace App\Repositories\Proviver;

use App\Models\Proviver\Provider;
use App\Repositories\AbstractRepository;

class ProviderRepository extends AbstractRepository
{
    public function __construct(Provider $model)
    {
        parent::__construct($model);
    }

      public function index(?int $paginate, ?array $filterParams, ?array $orderByParams, $relationships = [])
    {

    

        $orderByParams = [
            'name' => 'asc'
        ];
        $query = $this->buildQuery(
            $paginate,
            $filterParams,
            $orderByParams,
            $relationships,
         
        );

      
        return $query;
    }
}