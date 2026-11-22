<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            // Vendor
            $table->unsignedInteger('vendor_id');

            // Category
            $table->unsignedInteger('category_id');

            // Subcategory
            $table->unsignedInteger('subcategory_id')
                ->nullable();

            // Price Type
            $table->unsignedInteger('price_type_id');

            // Unit
            $table->unsignedInteger('unit_id')
                ->nullable();

            // Service Details
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();

            // Pricing
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('min_price', 12, 2)->nullable();
            $table->decimal('max_price', 12, 2)->nullable();

            // Service Image
            $table->string('image', 255)->nullable();

            // Experience
            $table->unsignedInteger('experience')->nullable();

            // Service Coverage
            $table->text('service_area')->nullable();
            $table->text('pincode')->nullable();

            // Additional Service Information
            $table->json('attributes')->nullable();

            // Status
            $table->boolean('status')
                ->default(true);

            $table->boolean('is_verified')
                ->default(false);

            // Display Ordering
            $table->integer('sort_order')
                ->default(0);

            // Created By
            $table->unsignedInteger('created_by')
                ->nullable();

            // Updated By
            $table->unsignedInteger('updated_by')
                ->nullable();
            
            // Timestamps
            $table->timestamps();

            // Foreign Key - Vendor
            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
                ->cascadeOnDelete();

            // Foreign Key - Category
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->restrictOnDelete();

            // Foreign Key - Subcategory
            $table->foreign('subcategory_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete();

            // Foreign Key - Price Type
            $table->foreign('price_type_id')
                ->references('id')
                ->on('prices')
                ->restrictOnDelete();

            // Foreign Key - Unit
            $table->foreign('unit_id')
                ->references('id')
                ->on('units')
                ->nullOnDelete();

            // Foreign Key - Created By
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // Foreign Key - Updated By
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // Indexes
            $table->index('vendor_id');
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('price_type_id');
            $table->index('unit_id');
            $table->index('status');
            $table->index('is_verified');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};