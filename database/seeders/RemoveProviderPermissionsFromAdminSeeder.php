<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission\Role;
use App\Models\Permission\Permission;
use App\Helpers\Helper;

class RemoveProviderPermissionsFromAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Localizar a Role de Administrador (ID 1)
        $adminRole = Role::find(1);

        if (!$adminRole) {
            $this->command->error("Role Administrador (ID 1) não encontrada.");
            return;
        }

        // 2. Definir os slugs dos módulos que o Admin NÃO deve ter
        // Geramos os nomes conforme a lógica do seu PermissionSeed original
        $providerSlug = Helper::formatarString('Provedor'); // ex: 'provedor'
        $providerComplaintsSlug = Helper::formatarString('Provedor Reclamações'); // ex: 'provedor-reclamacoes'

        // 3. Buscar os IDs das permissões que começam com esses slugs
        // Isso pegará 'provedor-show', 'provedor-edit', 'provedor-reclamacoes-show', etc.
        $permissionsToRemove = Permission::where('name', 'like', "{$providerSlug}-%")
            ->orWhere('name', 'like', "{$providerComplaintsSlug}-%")
            ->pluck('id')
            ->toArray();

        if (empty($permissionsToRemove)) {
            $this->command->info("Nenhuma permissão de provedor encontrada para remover.");
            return;
        }

        // 4. Remover (detach) essas permissões específicas da Role de Admin
        $adminRole->permissions()->detach($permissionsToRemove);

        $this->command->info("Permissões de provedor removidas do Administrador com sucesso!");
    }
}