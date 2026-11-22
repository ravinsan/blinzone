<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            // State
            $table->unsignedInteger('state_id');

            // Foreign Key - States
            $table->foreign('state_id')
                ->references('id')
                ->on('states')
                ->cascadeOnDelete();

            // City Details
            $table->string('name', 100);

            // Status
            $table->boolean('status')
                ->default(true);
            $table->softDeletes();
            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['state_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};