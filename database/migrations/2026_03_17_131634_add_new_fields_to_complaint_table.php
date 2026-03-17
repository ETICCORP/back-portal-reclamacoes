<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint', function (Blueprint $table) {

            // Adiciona a coluna 'source' apenas se não existir
            if (!Schema::hasColumn('complaint', 'source')) {
                $table->string('source')->default('portal')->after('type');
            }

            // Adiciona 'user_id' apenas se não existir
            if (!Schema::hasColumn('complaint', 'user_id')) {
                $table->foreignId('user_id')
                      ->nullable()
                      ->after('source')
                      ->constrained('users')
                      ->nullOnDelete();
            }

            // Adiciona 'received_at' apenas se não existir
            if (!Schema::hasColumn('complaint', 'received_at')) {
                // Ajuste o after para uma coluna existente, ex: 'representative' ou 'created_at'
                $afterColumn = Schema::hasColumn('complaint', 'representative') ? 'representative' : 'created_at';
                $table->timestamp('received_at')
                      ->nullable()
                      ->after($afterColumn);
            }
        });
    }

    public function down(): void
    {
        Schema::table('complaint', function (Blueprint $table) {
            if (Schema::hasColumn('complaint', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('complaint', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('complaint', 'received_at')) {
                $table->dropColumn('received_at');
            }
        });
    }
};