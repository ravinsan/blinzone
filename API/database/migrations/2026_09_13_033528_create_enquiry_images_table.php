<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiry_images', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            // Enquiry
            $table->unsignedInteger('enquiry_id');

            // Image
            $table->string('image', 255);

            // Timestamps
            $table->timestamps();

            // Foreign Key - Enquiry
            $table->foreign('enquiry_id')
                ->references('id')
                ->on('enquiries')
                ->cascadeOnDelete();

            // Index
            $table->index('enquiry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiry_images');
    }
};