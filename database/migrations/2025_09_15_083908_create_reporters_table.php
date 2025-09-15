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
        Schema::create('reporter', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_complaint');
        
            $table->string('fullName');
            $table->string('email')->nullable();
            $table->string('departament')->nullable();
            $table->string('phone')->nullable();;
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporter');
    }
};
