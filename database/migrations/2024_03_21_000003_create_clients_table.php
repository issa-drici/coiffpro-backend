<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('firstName');
            $table->string('lastName')->nullable();
            $table->string('phoneNumber');
            $table->string('email')->nullable();
            $table->foreignUuid('salon_id')->constrained('salons');
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index(['salon_id', 'phoneNumber']);
            $table->index(['salon_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
