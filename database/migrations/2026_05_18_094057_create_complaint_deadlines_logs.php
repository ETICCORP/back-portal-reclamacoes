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
        Schema::dropIfExists('complaint_deadlines_logs');

        Schema::create('complaint_deadlines_logs', function (Blueprint $table) {
            $table->id();

            // Relacionamento com a tabela de prazos pai
            $table->foreignId('complaint_deadline_id')
                ->constrained('complaint_deadlines')
                ->onDelete('cascade');

            // Fotografia dos dados naquele exato momento do log
            $table->integer('days');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('status', 50);

            // Razão da extensão ou criação (Auditoria)
            $table->text('reason');

            // Quem fez a ação (Null se for o registo inicial feito pelo portal público)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamps(); // Cria 'created_at' que servirá como data do log

            // Índice para relatórios rápidos de auditoria
            $table->index('complaint_deadline_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_deadlines_logs');
    }
};
