<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_bank_details', function (Blueprint $table) {

            // Primary Key
            $table->increments('id');

            // Vendor
            $table->unsignedInteger('vendor_id');

            // Bank Details
            $table->string('bank_name', 255);
            $table->string('account_number', 100);
            $table->string('account_holder_name', 255);
            $table->string('ifsc_code', 20);
            $table->string('upi_id', 255)->nullable();
            $table->string('qr_code_image', 255)->nullable();

            // Timestamps
            $table->timestamps();

            // Foreign Key - Vendor
            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
                ->cascadeOnDelete();

            // Indexes
            $table->index('vendor_id');
            $table->index('ifsc_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_bank_details');
    }
};