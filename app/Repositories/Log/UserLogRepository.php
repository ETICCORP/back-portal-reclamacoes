<?php
namespace App\Repositories\Log;

use App\Models\Log\UserLog;
use App\Repositories\AbstractRepository;

class UserLogRepository extends AbstractRepository
{
    public function __construct(UserLog $model)
    {
        parent::__construct($model);
    }
}