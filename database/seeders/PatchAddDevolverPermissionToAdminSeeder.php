<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Permission\Role;
use App\Models\Permission\Permission;
use Illuminate\Database\Seeder;

class PatchAddDevolverPermissionToAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Localizar a Role de Administrador
        $adminRole = Role::where('name', 'Administrador')->first();

        if (!$adminRole) {
            $this->command->error('Role "Administrador" não encontrada!');
            return;
        }

        // 2. Definir o módulo e as novas operações
        $moduleName = 'Reclamações';
        $moduleContext = 'Monitorar o fluxo geral de reclamações.';

        $newOperations = [
            'devolver' => 'Devolver',
        ];

        $newPermissionIds = [];

        foreach ($newOperations as $operation => $label) {
            // Segue a mesma lógica de formatação do seeder original
            $permissionName = Helper::formatarString($moduleName) . "-$operation";
            $description = "{$label} {$moduleName}: {$moduleContext}";

            $permission = Permission::updateOrCreate(
                ['name' => $permissionName],
                [
                    'description' => $description,
                    'is_active'   => true
                ]
            );

            $newPermissionIds[] = $permission->id;
        }

        // 3. Sincronizar (sem remover as antigas)
        // O método syncWithoutDetaching garante que o admin continue com o que já tinha
        $adminRole->permissions()->syncWithoutDetaching($newPermissionIds);

        $this->command->info('Novas permissões de Reclamação adicionadas ao Administrador com sucesso!');
    }
}
