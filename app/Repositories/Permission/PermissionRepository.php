<?php
namespace App\Repositories\Permission;

use App\Models\Permission\Permission;
use App\Repositories\AbstractRepository;

class PermissionRepository extends AbstractRepository
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }
}