<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            // Selected Service
            $table->unsignedInteger('service_id')
                ->nullable();

            // Category
            $table->unsignedInteger('category_id');

            // Subcategory
            $table->unsignedInteger('subcategory_id')
                ->nullable();

            // Customer / Visitor Details
            $table->string('name', 255);
            $table->string('phone', 30);
            $table->string('email', 255)->nullable();

            // Requirement
            $table->text('description');

            $table->string('work_type', 100)
                ->nullable();

            // Budget
            $table->decimal('budget_min', 12, 2)
                ->nullable();

            $table->decimal('budget_max', 12, 2)
                ->nullable();

            // Area
            $table->decimal('area', 12, 2)
                ->nullable();

            // Area Unit
            $table->unsignedInteger('area_unit_id')
                ->nullable();

            // Work Location
            $table->text('address')
                ->nullable();

            $table->string('city', 100)
                ->nullable();

            $table->string('state', 100)
                ->nullable();

            $table->string('pincode', 20)
                ->nullable();

            // Preferred Schedule
            $table->date('preferred_date')
                ->nullable();

            $table->time('preferred_time')
                ->nullable();

            // Enquiry Status
            $table->string('status', 50)
                ->default('pending');

            // Timestamps
            $table->timestamps();

            // Foreign Key - Service
            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->nullOnDelete();

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

            // Foreign Key - Area Unit
            $table->foreign('area_unit_id')
                ->references('id')
                ->on('units')
                ->nullOnDelete();

            // Indexes
            $table->index('service_id');
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('area_unit_id');
            $table->index('phone');
            $table->index('pincode');
            $table->index('status');
            $table->index('preferred_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};