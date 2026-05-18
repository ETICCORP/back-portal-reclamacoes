<?php

namespace Database\Seeders;

use App\Models\Permission\Permission;
use App\Models\Permission\Role;
use Illuminate\Database\Seeder;

class AddChangePasswordPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. CRIAR OU ATUALIZAR A PERMISSÃO (Idempotente)
        |--------------------------------------------------------------------------
        | Usamos updateOrCreate para garantir que, se o Seeder correr duas vezes,
        | a permissão é apenas atualizada em vez de lançar um erro de duplicados.
        */
        $permission = Permission::updateOrCreate(
            ['name' => 'usuario-change-password'],
            [
                'description' => 'Alterar Senha Usuário: Permitir a redefinição forçada de credenciais de utilizadores no sistema.',
                'is_active'   => true
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 2. VINCULAR AO PERFIL DE ADMINISTRADOR (Sem desvincular o resto)
        |--------------------------------------------------------------------------
        | Localizamos o perfil de Administrador. Se ele existir, associamos a nova
        | permissão usando 'syncWithoutDetaching' para não apagar as antigas.
        */
        $adminRole = Role::where('name', 'Administrador')->first();

        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching([$permission->id]);
            
            $this->command->info("✅ Sucesso: Permissão 'usuario-change-password' injetada no perfil Administrador.");
        } else {
            $this->command->error("❌ Erro: Perfil 'Administrador' não foi localizado. Execute o PermissionSeed primeiro.");
        }
    }
}