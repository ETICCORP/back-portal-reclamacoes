<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    private array $map = [
        'complaint_deadlines'           => 'complaint_id',
        'complaint_interactions'        => 'complaint_id',
        'complaint_opinions'            => 'complaint_id',
        'complaint_provider'            => 'complaint_id',
        'complaint_provider_response'   => 'complaint_id',
        'complaint_responses'           => 'complaint_id',
        'complaint_triages'             => 'complaint_id',
        'complaintattachment'           => 'fk_complaint',
        'description'                   => 'fk_complaint',
        'log'                           => 'complaint_id',
        'soluction'                     => 'fk_complaint',
    ];

    public function up(): void
    {
        $corte = Carbon::create(2026, 05, 10, 0, 0, 0);
        $parentTable = 'complaint';

        foreach ($this->map as $table => $column) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) continue;

            // 1. Limpeza total de órfãos (Removi o filtro de data para garantir que NADA bloqueie a FK)
            DB::table($table)
                ->whereNotExists(function ($query) use ($parentTable, $table, $column) {
                    $query->select(DB::raw(1))
                        ->from($parentTable)
                        ->whereColumn("{$parentTable}.id", "{$table}.{$column}");
                })->delete();

            // 2. Normalização de Tipo (Resolve o Erro 3780)
            // Forçamos a coluna a ser Big Integer Unsigned para casar com o ID do Laravel
            Schema::table($table, function (Blueprint $blueprint) use ($column) {
                $blueprint->unsignedBigInteger($column)->change();
            });

            // 3. Adição da FK
            $foreignKeys = Schema::getForeignKeys($table);
            $fkExists = collect($foreignKeys)->contains(fn($fk) => in_array($column, $fk['columns']));

            if (!$fkExists) {
                Schema::table($table, function (Blueprint $blueprint) use ($column, $parentTable) {
                    $blueprint->foreign($column)
                        ->references('id')
                        ->on($parentTable)
                        ->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->map as $table => $column) {
            if (!Schema::hasTable($table)) continue;
            $fkName = "{$table}_{$column}_foreign";
            if (collect(Schema::getForeignKeys($table))->contains(fn($fk) => $fk['name'] === $fkName)) {
                Schema::table($table, fn(Blueprint $b) => $b->dropForeign($fkName));
            }
        }
    }
};
