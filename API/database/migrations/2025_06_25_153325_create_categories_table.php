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
        Schema::create('categories', function (Blueprint $table) {
            // Primary Key
            $table->increments('id');

            // Category Details
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('image')->nullable();
            $table->longText('description')->nullable();

            // Parent Category
            $table->unsignedInteger('parent_id')->nullable();

            // Status
            $table->smallInteger('status')
                ->default(1)
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
            $table->index('parent_id');
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
        Schema::dropIfExists('categories');
    }
};