<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            // Primary Key
            $table->increments('id');

            // Slider Details
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->string('image')->nullable();
            $table->string('image_mobile')->nullable();

            // Slider Type
            $table->string('slider_type')
                ->default(1)
                ->comment('1 - Top, 2 - Middle, 3 - Bottom');

            // Status
            $table->string('status')
                ->default(0)
                ->comment('1 - Active, 0 - Inactive');

            // Created By - users.id
            $table->unsignedInteger('created_by')->nullable();

            // Updated By - users.id
            $table->unsignedInteger('updated_by')->nullable();

            // Soft Delete
            $table->softDeletes();

            // Timestamps
            $table->timestamps();

            // Foreign Keys
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // Indexes
            $table->index('slider_type');
            $table->index('status');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};