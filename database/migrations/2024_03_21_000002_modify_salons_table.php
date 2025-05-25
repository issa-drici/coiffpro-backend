<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            // Renommer logo_url en email
            $table->renameColumn('logo_url', 'email');
        });
    }

    public function down(): void
    {
        Schema::table('salons', function (Blueprint $table) {
            // Restaurer le nom original de la colonne
            $table->renameColumn('email', 'logo_url');
        });
    }
};
