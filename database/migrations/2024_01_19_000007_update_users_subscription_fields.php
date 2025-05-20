<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajouter les champs de gestion d'abonnement aux utilisateurs.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Modifier les colonnes existantes
            $table->string('user_plan')->default('basic')->change(); // basic, premium, expert
            $table->string('user_subscription_status')->default('trialing')->change(); // trialing, active, canceled, incomplete
            
            // Ajouter les nouvelles colonnes
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
        });
    }

    /**
     * Annuler les modifications.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'trial_ends_at',
                'subscription_ends_at'
            ]);
            
            // Remettre les colonnes dans leur état d'origine
            $table->string('user_plan')->nullable()->change();
            $table->string('user_subscription_status')->default('trialing')->change();
        });
    }
}; 