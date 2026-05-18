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
        Schema::table('comment', function (Blueprint $table) {
            // Modifica a coluna para aceitar valores nulos
            $table->unsignedBigInteger('fk_user')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comment', function (Blueprint $table) {
            // Reverte a coluna para NOT NULL (obrigatória) caso dê rollback
            $table->unsignedBigInteger('fk_user')->nullable(false)->change();
        });
    }
};