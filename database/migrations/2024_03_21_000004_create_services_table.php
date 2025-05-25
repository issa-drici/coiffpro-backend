<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->integer('duration')->nullable(); // en minutes
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->foreignUuid('salon_id')->constrained('salons');
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index(['salon_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
