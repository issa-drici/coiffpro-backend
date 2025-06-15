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
        Schema::create('barbers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('salon_id')->constrained('salons')->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index(['salon_id', 'is_active']);
            $table->index(['user_id']);

            // Contrainte unique pour éviter qu'un user soit barber dans plusieurs salons
            $table->unique(['user_id', 'salon_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barbers');
    }
};
