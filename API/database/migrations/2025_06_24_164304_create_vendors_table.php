<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            // Primary Key
            $table->increments('id');

            // User
            $table->unsignedInteger('user_id');

            // Vendor details
            $table->string('business_name', 255);
            $table->string('owner_name', 255);
            $table->string('phone', 30);
            $table->string('email', 255);
            $table->string('logo', 255)->nullable();
            $table->text('description')->nullable();

            // Offers
            $table->string('offers', 255)->nullable();

            // Distance & delivery charges
            $table->decimal('total_distance_cover', 5, 2)->nullable();
            $table->decimal('delivery_free_km', 5, 2)->nullable();
            $table->decimal('delivery_charge_per_km', 10, 2)->nullable();

            // Expected service/delivery time
            $table->string('delivery_time', 100)->nullable();

            // Vendor status
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();

            // Foreign Key
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            // Index
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};