<?php
namespace App\Services\Permission;

use App\Repositories\Permission\PermissionRepository;
use App\Services\AbstractService;

class PermissionService extends AbstractService
{
    public function __construct(PermissionRepository $repository)
    {
        parent::__construct($repository);
    }
}