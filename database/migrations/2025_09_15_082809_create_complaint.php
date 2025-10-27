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
        Schema::create('complaint', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('code');
            $table->string('incidentDateTime');
            $table->string('location');
            $table->string('suggestionAttempt');
            $table->string('relationship');
            $table->date('due_date')->nullable();
            $table->string('responsible_area')->nullable();
            $table->string('justification')->nullable();
            $table->string('urgency')->nullable();
            $table->string('contract_number')->nullable();
            $table->string('status');
            $table->string('entity');
            $table->string('gravity')->nullable();
            $table->string('responsible_analyst')->nullable();
            $table->longText('description')->nullable();
            $table->string('isAnonymous');
            $table->softDeletes();
            $table->timestamps();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint');
    }
};
