<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            // Country
            $table->unsignedInteger('country_id');

            // Foreign Key - Countries
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->cascadeOnDelete();

            // State Details
            $table->string('name', 100);
            $table->string('code', 20)->nullable();

            // Status
            $table->boolean('status')
                ->default(true);
            $table->softDeletes();
            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['country_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};