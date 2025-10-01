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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $role = Role::updateOrCreate(
            ['name' => 'Administrador'],
            [
                'name' => 'Administrador',
                'description' => 'Administrador do sistema',
                'is_active' => true,
            ]
        );


        // Definir módulos com suas descrições base
        $modules = [
            ['name' => 'Usuário', 'description' => 'Permite gerenciar usuários'],
            ['name' => 'Estatística', 'description' => 'Permite gerenciar estatísticas'],
            ['name' => 'Regra', 'description' => 'Permite gerenciar regras'],
            ['name' => 'Denúcias', 'description' => 'Permite gerenciar entidades'],
            ['name' => "Perfil", "description" => 'Permite gerenciar perfil'],
            ['name' => "Alertas", "description" => 'Permite gerenciar Alertas']
        ];

        // Operações básicas
        $operations = ['show', 'create', 'edit', 'delete'];
        $permission_id = [];

        foreach ($modules as $module) {
            foreach ($operations as $operation) {
                // Ajustar descrição para cada operação
                $operationDescriptions = [
                    'show' => "Permite listar {$module['name']}",
                    'create' => "Permite criar {$module['name']}",
                    'edit' => "Permite editar {$module['name']}",
                    'delete' => "Permite excluir {$module['name']}",
                ];

                // Gerar nome da permissão
                $permissionName = Helper::formatarString($module['name']) . "-$operation";

                // Criar ou atualizar permissão
                $permission = Permission::updateOrCreate(
                    ['name' => $permissionName],
                    [
                        'name' => $permissionName,
                        'description' => $operationDescriptions[$operation],
                        'is_active' => true,
                    ]
                );
                $permission_id[] = $permission->id;
                echo "Permissão {$permission->name} criada ou atualizada.\n";
            }
        }

        $role->permissions()->sync($permission_id);
        echo "Permissão {$permission->name} associada ao papel {$role->name}.\n";

        User::updateOrCreate([
            'email' => 'admin@gmail.com'
        ], [
            'first_name' => 'Administrador',
            'last_name' => 'Sistema',
            'phone' => '11999999999',
            'email' => 'marcia.buienga@etic.co.ao',
            'password' => bcrypt('12345678'),
            'role_id' => $role->id,
            'is_active' => true
        ]);
        echo "Usuário administrador criado ou atualizado.\n";
    }
}
