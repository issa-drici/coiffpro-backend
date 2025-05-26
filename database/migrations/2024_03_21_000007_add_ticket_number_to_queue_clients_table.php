<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queue_clients', function (Blueprint $table) {
            $table->integer('ticket_number')->after('notes');

            // Ajout d'un index pour optimiser la recherche du dernier ticket par salon et date
            $table->index(['salon_id', 'created_at', 'ticket_number']);
        });
    }

    public function down(): void
    {
        Schema::table('queue_clients', function (Blueprint $table) {
            $table->dropIndex(['salon_id', 'created_at', 'ticket_number']);
            $table->dropColumn('ticket_number');
        });
    }
};