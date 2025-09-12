<?php
namespace App\Services\Permission;

use App\Repositories\Permission\PermissionRoleRepository;
use App\Services\AbstractService;

class PermissionRoleService extends AbstractService
{
    public function __construct(PermissionRoleRepository $repository)
    {
        parent::__construct($repository);
    }
}