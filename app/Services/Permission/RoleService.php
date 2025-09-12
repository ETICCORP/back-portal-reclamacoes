<?php

namespace App\Services\Permission;

use App\Repositories\Permission\RoleRepository;
use App\Services\AbstractService;

class RoleService extends AbstractService
{
    public function __construct(RoleRepository $repository, private readonly PermissionService $permissionService)
    {
        parent::__construct($repository);
    }

    public function store(array $data)
    {
        $role = $this->repository->store($data);
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }
        return $role;
    }

    public function update(array $data, int $id)
    {
        $role = $this->repository->update($data, $id);
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }
        return $role;
    }
}
