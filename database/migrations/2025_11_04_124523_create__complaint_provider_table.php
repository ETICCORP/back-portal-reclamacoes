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
        Schema::create('complaint_provider', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_id');
            $table->string('provider_id');
            $table->string('summary')->nullable();
            $table->string('notes')->nullable();
            $table->string('sent_at')->nullable();
            $table->string('deadline')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_complaint_provider');
    }
};
