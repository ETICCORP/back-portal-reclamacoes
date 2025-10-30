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
        Schema::create('complaint_triages', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_id')->constrained()->cascadeOnDelete();
            $table->string('classification_type')->nullable();
            $table->string('severity')->nullable();
            $table->string('urgency')->nullable();
            $table->string('responsible_area')->nullable();
            $table->boolean('is_refused')->default(false);
            $table->text('refusal_reason')->nullable();
            $table->string('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_triages');
    }
};
