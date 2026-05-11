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
        Schema::table('complaint_triages', function (Blueprint $table) {
            // Adicionando os campos após o motivo da recusa por organização
            $table->boolean('is_returned')->default(false);
            $table->text('return_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaint_triages', function (Blueprint $table) {
            $table->dropColumn(['is_returned', 'return_reason']);
        });
    }
};