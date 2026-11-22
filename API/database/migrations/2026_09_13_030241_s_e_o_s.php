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
        Schema::create('s_e_o_s', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('page_id');

            $table->smallInteger('page_type')
                ->comment('0 - Content page, 1 - News Menu, 2 - News');

            $table->string('meta_title')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->string('meta_description')->nullable();

            $table->timestamps();

            $table->index(['page_id', 'page_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_e_o_s');
    }
};