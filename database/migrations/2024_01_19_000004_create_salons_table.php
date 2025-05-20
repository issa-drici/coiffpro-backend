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
        Schema::create('salons', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));

            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('name');
            $table->string('name_slug')->nullable()->after('name');

            $table->string('address')->nullable();
            $table->string('postal_code', 10)->nullable()->after('address');
            $table->string('city')->nullable()->after('postal_code');
            $table->string('city_slug')->nullable()->after('city');
            $table->string('type_slug')->nullable()->after('city_slug');

            $table->string('phone', 50)->nullable();

            $table->string('logo_url')->nullable();

            $table->json('social_links')->nullable();
            $table->json('google_info')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salons');
    }
};
