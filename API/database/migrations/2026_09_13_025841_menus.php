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
        Schema::create('menus', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            // Menu Details
            $table->string('name')->nullable();
            $table->string('url')->nullable();

            // Parent Menu
            // 0 = Top Level Menu
            $table->unsignedInteger('parent_id')
                ->default(0);

            // Display Order
            $table->unsignedInteger('order_id')
                ->default(0);

            // Status
            $table->smallInteger('status')
                ->default(1)
                ->comment('1 - Active, 0 - Inactive');

            // Created By
            $table->unsignedInteger('created_by')
                ->nullable();

            // Updated By
            $table->unsignedInteger('updated_by')
                ->nullable();

            // Foreign Keys
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // Soft Deletes
            $table->softDeletes();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('parent_id');
            $table->index('order_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};