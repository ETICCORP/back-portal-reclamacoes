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
        Schema::create('model_email', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('subject')->nullable();
            $table->longText('body');
              $table->string('user_id');
            $table->string('signature_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_email');
    }
};
