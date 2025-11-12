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
        Schema::create('complaint_deadlines', function (Blueprint $table) {
            $table->id();
       
            $table->string('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->integer('days')->default(15); // prazo configurável
            $table->date('start_date'); // quando começa a contar
            $table->date('end_date');   // data limite calculada
            $table->string('status');
            $table->timestamp('notified_at')->nullable(); // quando foi enviado alerta
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_deadlines');
    }
};
