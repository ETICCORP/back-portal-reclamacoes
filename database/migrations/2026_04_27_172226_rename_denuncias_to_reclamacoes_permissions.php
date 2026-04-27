<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission\Permission;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Esta função transforma "denuncias" em "reclamacoes".
     */
    public function up(): void
    {
        DB::transaction(function () {
            // Buscamos as permissões que contenham o termo técnico (sem acento)
            $permissions = Permission::where('name', 'like', '%denucias%')->get();

            foreach ($permissions as $permission) {
                $oldName = $permission->name;
                
                // 1. Atualiza o nome técnico (slug)
                $newName = str_replace('denucias', 'reclamacoes', $oldName);

                // 2. Atualiza a descrição (com suporte a acentuação e Case Sensitivity)
                $newDescription = str_ireplace('denúcias', 'reclamações', $permission->description);

                $permission->update([
                    'name' => $newName,
                    'description' => $newDescription
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     * Caminho de volta: Caso precise fazer rollback, o sistema volta a ser "denuncias".
     */
    public function down(): void
    {
        DB::transaction(function () {
            $permissions = Permission::where('name', 'like', '%reclamacoes%')->get();

            foreach ($permissions as $permission) {
                $permission->update([
                    'name' => str_replace('reclamacoes', 'denucias', $permission->name),
                    'description' => str_ireplace('reclamações', 'denúncias', $permission->description)
                ]);
            }
        });
    }
};