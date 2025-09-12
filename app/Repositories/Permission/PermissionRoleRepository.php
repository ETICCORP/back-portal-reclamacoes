<?php
namespace App\Repositories\Permission;

use App\Models\Permission\PermissionRole;
use App\Repositories\AbstractRepository;

class PermissionRoleRepository extends AbstractRepository
{
    public function __construct(PermissionRole $model)
    {
        parent::__construct($model);
    }
}