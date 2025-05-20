<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));

            // Abonnement par restaurant
            $table->foreignUuid('salon_id')->constrained('salons')->cascadeOnDelete();

            $table->string('plan', 50); // ex: 'basic', 'premium'
            $table->string('status', 50)->default('trialing'); // ex: 'active', 'canceled', 'trialing'

            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
