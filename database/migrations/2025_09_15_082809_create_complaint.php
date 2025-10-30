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
            $table->string('full_name');
            $table->string('complainant_role');
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('policy_number')->nullable();
            $table->string('entity');
            $table->string('code');
            $table->longText('description');
            $table->string('incidentDateTime');
            $table->string('location');
            $table->string('status')->nullable();
                   $table->string('type');
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
