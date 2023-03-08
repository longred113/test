<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matched_activities', function (Blueprint $table) {
            $table->string('matchedActivityId')->primary();
            $table->string('productId')->nullable();
            $table->string('name')->nullable();
            $table->integer('time')->nullable();
            $table->string('unitId')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('matched_activities');
    }
}
