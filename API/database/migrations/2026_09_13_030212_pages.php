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
        Schema::create('pages', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            // Page Details
            $table->string('title');
            $table->string('slug')
                ->nullable()
                ->index();

            $table->string('image')
                ->nullable();

            $table->longText('description')
                ->nullable();

            $table->longText('short_description')
                ->nullable();

            $table->longText('long_description')
                ->nullable();

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

            // Soft Deletes
            $table->softDeletes();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};