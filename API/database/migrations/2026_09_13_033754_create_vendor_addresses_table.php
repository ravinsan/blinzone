<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_addresses', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            // Vendor
            $table->unsignedInteger('vendor_id');

            // Address
            $table->string('address_1', 255);
            $table->string('address_2', 255)->nullable();
            $table->string('sector', 100)->nullable();
            $table->string('landmark', 255)->nullable();

            // Location
            $table->unsignedInteger('country_id');
            $table->unsignedInteger('state_id');
            $table->unsignedInteger('city_id');

            // Delivery Zone
            $table->unsignedInteger('delivery_zone_id');

            // Business Documents / Images
            $table->string('shop_image', 255)->nullable();
            $table->string('pan_card_image', 255)->nullable();
            $table->string('aadhaar_front_image', 255)->nullable();
            $table->string('aadhaar_back_image', 255)->nullable();

            // Business Information
            $table->string('gst_number', 50)->nullable();
            $table->string('pan_number', 50)->nullable();
            $table->string('alternate_phone', 30)->nullable();
            $table->string('business_email', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->text('description')->nullable();

            // Location Coordinates
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // FSSAI
            $table->string('fssai_number', 100)->nullable();
            $table->date('fssai_expiry_date')->nullable();

            // Status
            $table->boolean('status')
                ->default(true);

            // Timestamps
            $table->timestamps();

            // Foreign Key - Vendor
            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
                ->cascadeOnDelete();

            // Foreign Key - Country
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->restrictOnDelete();

            // Foreign Key - State
            $table->foreign('state_id')
                ->references('id')
                ->on('states')
                ->restrictOnDelete();

            // Foreign Key - City
            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->restrictOnDelete();

            // Foreign Key - Delivery Zone
            $table->foreign('delivery_zone_id')
                ->references('id')
                ->on('delivery_zones')
                ->restrictOnDelete();

            // Indexes
            $table->index('vendor_id');
            $table->index('country_id');
            $table->index('state_id');
            $table->index('city_id');
            $table->index('delivery_zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_addresses');
    }
};