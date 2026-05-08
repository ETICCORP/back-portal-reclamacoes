<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission\Permission;
use Illuminate\Support\Facades\DB;

class UpdatePermissionNamesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->warn('Iniciando a migração de nomes: Denúcias -> Reclamações');

        DB::transaction(function () {
            // Buscamos especificamente pelo termo incorreto/antigo
            $oldPermissions = Permission::where('name', 'like', '%denucias%')->get();

            if ($oldPermissions->isEmpty()) {
                $this->command->info('Nenhuma permissão antiga encontrada para atualizar.');
                return;
            }

            foreach ($oldPermissions as $permission) {
                $oldName = $permission->name;
                $newName = str_replace('denucias', 'reclamacoes', $oldName);
                $newDescription = str_ireplace('denúcias', 'reclamações', $permission->description);

                // Verificamos se o nome de destino já está sendo usado por OUTRO ID
                $existing = Permission::where('name', $newName)
                    ->where('id', '!=', $permission->id)
                    ->first();

                if ($existing) {
                    /**
                     * CASO CRÍTICO: O nome novo já existe em outro ID.
                     * Não podemos deletar o atual nem renomear para um duplicado.
                     * Apenas avisamos e ignoramos para preservar os relacionamentos de ambos.
                     */
                    $this->command->error("Conflito: '{$oldName}' não pôde ser renomeado pois '{$newName}' já existe com ID {$existing->id}.");
                    continue;
                }

                // Executa a atualização
                $permission->update([
                    'name'        => $newName,
                    'description' => $newDescription
                ]);

                $this->command->line("<info>Atualizado:</info> {$oldName} <fg=gray>-></fg=gray> {$newName}");
            }
        });

        $this->command->info('Processo de atualização finalizado.');
    }
}
