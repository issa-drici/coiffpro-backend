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
        Schema::table('queue_clients', function (Blueprint $table) {
            $table->foreignUuid('barber_id')->nullable()->after('salon_id')->constrained('barbers')->nullOnDelete();

            // Index pour optimiser les recherches
            $table->index(['barber_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue_clients', function (Blueprint $table) {
            $table->dropForeign(['barber_id']);
            $table->dropIndex(['barber_id', 'status']);
            $table->dropColumn('barber_id');
        });
    }
};
