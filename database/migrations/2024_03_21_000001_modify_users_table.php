<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstName')->after('id');
            $table->string('lastName')->after('firstName');
            $table->foreignUuid('salon_id')->nullable()->after('role')->constrained('salons');

            // Suppression des colonnes qui ne sont plus nécessaires
            $table->dropColumn(['name', 'user_plan', 'user_subscription_status']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('user_plan')->nullable();
            $table->string('user_subscription_status')->nullable();

            // Suppression des nouvelles colonnes
            $table->dropForeign(['salon_id']);
            $table->dropColumn(['firstName', 'lastName', 'salon_id']);
        });
    }
};
