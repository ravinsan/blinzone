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
        Schema::create('role_menus', function (Blueprint $table) {
            $table->increments('id');;

            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('menu_id')->nullable();

            $table->smallInteger('is_parent')
                ->default(1)
                ->comment('1-Active, 0-Inactive');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_menus');
    }
};