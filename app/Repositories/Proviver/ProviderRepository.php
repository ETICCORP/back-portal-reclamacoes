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
}