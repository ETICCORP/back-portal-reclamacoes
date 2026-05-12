<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('log', function (Blueprint $table) {
            // Alteramos a coluna para permitir NULL
            // Mantemos o unsignedBigInteger para não quebrar a FK existente
            $table->unsignedBigInteger('complaint_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log', function (Blueprint $table) {
            // Caso precise reverter, ela volta a ser obrigatória
            // Nota: Isso falhará se houver registros nulos no banco durante o rollback
            $table->unsignedBigInteger('complaint_id')->nullable(false)->change();
        });
    }
};
