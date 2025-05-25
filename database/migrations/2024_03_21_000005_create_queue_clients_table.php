<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients');
            $table->enum('status', ['waiting', 'in_progress', 'completed', 'absent'])->default('waiting');
            $table->dateTime('estimatedTime');
            $table->decimal('amountToPay', 8, 2)->nullable();
            $table->foreignUuid('salon_id')->constrained('salons');
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index(['salon_id', 'status']);
            $table->index(['salon_id', 'estimatedTime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_clients');
    }
};
