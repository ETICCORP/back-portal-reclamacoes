<?php
namespace App\Repositories\Log;

use App\Models\Log\Log;
use App\Repositories\AbstractRepository;

class LogRepository extends AbstractRepository
{
    public function __construct(Log $model)
    {
        parent::__construct($model);
    }
}