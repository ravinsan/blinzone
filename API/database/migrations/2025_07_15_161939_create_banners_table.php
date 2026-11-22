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
        Schema::create('banners', function (Blueprint $table) {
            // Primary Key
            $table->increments('id');

            // Banner Details
            $table->string('position')->nullable();
            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->string('thumb')->nullable();
            $table->longText('description')->nullable();

            // Display Order
            $table->string('ord')->default(1);

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
            $table->index('position');
            $table->index('status');
            $table->index('ord');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};