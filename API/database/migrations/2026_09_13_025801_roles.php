<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {

            $table->increments('id');

            // Role Details
            $table->string('name')->nullable();
            $table->string('guard_name')->nullable();
            $table->string('role_level')->nullable();

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
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};