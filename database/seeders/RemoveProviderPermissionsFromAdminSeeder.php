<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission\Role;
use App\Models\Permission\Permission;
use App\Helpers\Helper;

class RemoveProviderPermissionsFromAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Localizar o Admin (Usando nome ou ID para garantir)
        $adminRole = Role::where('name', 'Administrador')->orWhere('id', 1)->first();

        if (!$adminRole) {
            $this->command->error("Role Administrador não encontrada.");
            return;
        }

        // 2. Slugs específicos
        $slugs = [
            Helper::formatarString('Provedor'),
            Helper::formatarString('Provedor Reclamações')
        ];

        // 3. Buscar IDs de forma agrupada e segura
        // Usamos uma função anônima no where para agrupar o OR (WHERE (name LIKE ... OR name LIKE ...))
        $permissionsToRemove = Permission::where(function ($query) use ($slugs) {
            foreach ($slugs as $slug) {
                $query->orWhere('name', 'like', "{$slug}-%");
            }
        })->pluck('permission.id')->toArray();

        if (empty($permissionsToRemove)) {
            $this->command->info("Nenhuma permissão encontrada para os slugs informados.");
            return;
        }

        // 4. IDEMPOTÊNCIA: Verificar quais dessas permissões o Admin REALMENTE tem antes de tentar remover
        // Isso evita que o banco tente processar remoções de algo que já não existe
        $currentAdminPermissions = $adminRole->permissions()
            ->whereIn('permission_id', $permissionsToRemove)
            ->pluck('permission.id')
            ->toArray();

        if (empty($currentAdminPermissions)) {
            $this->command->comment("O Admin já não possui mais essas permissões. Nada a fazer.");
            return;
        }

        // 5. Remover apenas as que ele possui
        $adminRole->permissions()->detach($currentAdminPermissions);

        $this->command->info(count($currentAdminPermissions) . " permissões de provedor removidas do Administrador.");
    }
}