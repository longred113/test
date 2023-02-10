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
        Schema::create('match_activities', function (Blueprint $table) {
            $table->string('matchActivityId')->primary();
            $table->string('productId')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->integer('time')->nullable();
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
        Schema::dropIfExists('match_activities');
    }
}
