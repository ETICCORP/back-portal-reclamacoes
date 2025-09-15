<?php
namespace App\Repositories\Soluction;

use App\Models\Soluction\Soluction;
use App\Repositories\AbstractRepository;

class SoluctionRepository extends AbstractRepository
{
    public function __construct(Soluction $model)
    {
        parent::__construct($model);
    }
}