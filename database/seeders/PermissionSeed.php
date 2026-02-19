<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Permission\Role;
use Illuminate\Database\Seeder;
use App\Models\Permission\Permission;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            [
                'description' => 'Administrador do sistema',
                'is_active' => true,
            ]
        );

        $providerRole = Role::updateOrCreate(
            ['name' => 'Provedor'],
            [
                'description' => 'Provedor do sistema',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | MÓDULOS
        |--------------------------------------------------------------------------
        */
        $modules = [
            ['name' => 'Usuário', 'description' => 'Permite gerenciar usuários'],
            ['name' => 'Estatística', 'description' => 'Permite gerenciar estatísticas'],
            ['name' => 'Regra', 'description' => 'Permite gerenciar regras'],
            ['name' => 'Denúcias', 'description' => 'Permite gerenciar denúncias'],
            ['name' => 'Perfil', 'description' => 'Permite gerenciar perfil'],
            ['name' => 'Alertas', 'description' => 'Permite gerenciar alertas'],
            ['name' => 'Histórico', 'description' => 'Permite visualizar histórico'],
            ['name' => 'Provedor', 'description' => 'Permite gerenciar provedores'],
        ];

        $operations = ['show', 'create', 'edit', 'delete'];

        $adminPermissions = [];
        $providerPermissions = [];

        /*
        |--------------------------------------------------------------------------
        | PERMISSÕES
        |--------------------------------------------------------------------------
        */
        foreach ($modules as $module) {
            foreach ($operations as $operation) {

                $permissionName = Helper::formatarString($module['name']) . "-$operation";

                $permission = Permission::updateOrCreate(
                    ['name' => $permissionName],
                    [
                        'description' => "Permite {$operation} {$module['name']}",
                        'is_active' => true,
                    ]
                );

                // Administrador recebe TODAS
                $adminPermissions[] = $permission->id;

                // Provedor recebe APENAS SHOW de Denúcias, Alertas e Histórico
                if (
                    $operation === 'show' &&
                    in_array($module['name'], ['Denúcias', 'Alertas', 'Histórico'])
                ) {
                    $providerPermissions[] = $permission->id;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ASSOCIAR PERMISSÕES
        |--------------------------------------------------------------------------
        */
        $adminRole->permissions()->sync($adminPermissions);
        $providerRole->permissions()->sync($providerPermissions);

        /*
        |--------------------------------------------------------------------------
        | USUÁRIO ADMIN
        |--------------------------------------------------------------------------
        */
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'Administrador',
                'last_name' => 'Sistema',
                'phone' => '11999999999',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('12345678'),
                'role_id' => $adminRole->id,
                'is_active' => true
            ]
        );
    }
}
