<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('productId')->primary();
            $table->string('name')->nullable();
            $table->string('level')->nullable();
            $table->string('startLevel')->nullable();
            $table->string('endLevel')->nullable();
            $table->string('details')->nullable();
            $table->string('image')->nullable();
            $table->boolean('activate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
