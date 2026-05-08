<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Permission\Role;
use Illuminate\Database\Seeder;
use App\Models\Permission\Permission;
use App\Models\User\User;

class PermissionSeed extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */
        $adminRole = Role::updateOrCreate(
            ['name' => 'Administrador'],
            ['description' => 'Administrador do sistema', 'is_active' => true]
        );

        $providerRole = Role::updateOrCreate(
            ['name' => 'Provedor'],
            ['description' => 'Provedor do sistema', 'is_active' => true]
        );

        /*
        |--------------------------------------------------------------------------
        | MÓDULOS E PERMISSÕES
        |--------------------------------------------------------------------------
        */
        $modules = [
            ['name' => 'Usuário'],
            ['name' => 'Estatística'],
            ['name' => 'Regra'],
            ['name' => 'Denúcias'],
            ['name' => 'Perfil'],
            ['name' => 'Alertas'],
            ['name' => 'Histórico'],
            ['name' => 'Provedor'],
            ['name' => 'Provedor Reclamações'],
        ];

        $operations = ['show', 'create', 'edit', 'delete'];
        $adminPermissions = [];
        $providerPermissions = [];

        foreach ($modules as $module) {
            foreach ($operations as $operation) {
                $permissionName = Helper::formatarString($module['name']) . "-$operation";
                
                $permission = Permission::updateOrCreate(
                    ['name' => $permissionName],
                    ['description' => "Permite {$operation} {$module['name']}", 'is_active' => true]
                );

                // Lógica de Separação:
                // Se o módulo for "Provedor" ou "Provedor Reclamações", vai para o Provedor.
 ]               // Caso contrário, vai para o Administrador.
                if (in_array($module['name'], ['Provedor', 'Provedor Reclamações'])) {
                    $providerPermissions[] = $permission->id;
                } else {
                    $adminPermissions[] = $permission->id;
                }
            }
        }

        // Sincroniza garantindo exclusividade
        $adminRole->permissions()->sync($adminPermissions);
        $providerRole->permissions()->sync($providerPermissions);

        /*
        |--------------------------------------------------------------------------
        | CRIAÇÃO DE USUÁRIOS
        |--------------------------------------------------------------------------
        */

        // Criando Admin
        $this->createUser(
            'Evangelina',
            'Jaime',
            'evangelina.jaime@keepcomply.co.ao',
            'Evangelina123@@',
            '922222222',
            $adminRole->id
        );

        $this->createUser(
            'Vicente',
            'Eduardo',
            'vicente.eduardo@etic.co.ao',
            'Vicente123@@',
            '922222222',
            $adminRole->id
        );

        // Criando Provedor
        $this->createUser(
            'Evangelina Jaime',
            'Provedor',
            'keepcomply838@gmail.com',
            'Evangelina12Provedor@@',
            '933333333',
            $providerRole->id
        );
    }

    private function createUser(string $fname, string $lname, string $email, string $password, string $phone, int $roleId): void
    {
        User::updateOrCreate(
            ['email' => $email],
            [
                'first_name' => $fname,
                'last_name'  => $lname,
                'phone'      => $phone,
                'password'   => bcrypt($password),
                'role_id'    => $roleId,
                'is_active'  => true
            ]
        );
    }
}