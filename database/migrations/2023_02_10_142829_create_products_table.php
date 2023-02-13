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
            $table->integer('packageId')->nullable();
            $table->string('name')->nullable();
            $table->string('startLevel')->nullable();
            $table->string('endLevel')->nullable();
            $table->string('details')->nullable();
            $table->string('image')->nullable();
            $table->boolean('activate')->nullable();
            $table->timestamps();
            $table->foreign('packageId')->references('packageId')->on('pakages')->onDelete('cascade');
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
