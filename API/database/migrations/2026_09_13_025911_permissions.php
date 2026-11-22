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
        Schema::create('permissions', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            // Menu
            $table->unsignedInteger('menu_id')
                ->nullable();

            $table->unsignedInteger('sub_menu_id')
                ->nullable();

            // Permission Details
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->string('guard_name')
                ->default('web');

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

            // Foreign Keys - Users
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // Timestamps
            $table->timestamps();

            // Soft Deletes
            $table->softDeletes();

            // Indexes
            $table->index('menu_id');
            $table->index('sub_menu_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};