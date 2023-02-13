<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->string('unitId')->primary();
            $table->string('matchedActivityId')->nullable();
            $table->string('productId')->nullable();
            $table->string('name')->nullable();
            $table->date('startDate')->nullable();
            $table->date('startDate')->nullable();
            $table->timestamps();
            $table->foreign('productId')->references('productId')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
    }
}
