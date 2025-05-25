<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_client_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('queue_client_id')->constrained('queue_clients')->onDelete('cascade');
            $table->foreignUuid('service_id')->constrained('services')->onDelete('cascade');
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index(['queue_client_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_client_services');
    }
};
