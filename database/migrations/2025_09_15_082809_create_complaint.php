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
            $table->string('status');
            $table->string('description')->nullable();
            
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
