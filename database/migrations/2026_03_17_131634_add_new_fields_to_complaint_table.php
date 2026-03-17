<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint', function (Blueprint $table) {

            // Origem da reclamação (portal, carta, email, etc.)
            $table->string('source')->default('portal')->after('type');

            // Quem registrou (gestor ou sistema)
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('source')
                  ->constrained('users')
                  ->nullOnDelete();


            // Data de recepção da reclamação
            $table->timestamp('received_at')
                  ->nullable()
                  ->after('representative');
        });
    }

    public function down(): void
    {
        Schema::table('complaint', function (Blueprint $table) {
            if (Schema::hasColumn('complaint', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('complaint', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('complaint', 'received_at')) {
                $table->dropColumn('received_at');
            }
        });
    
    }
};