<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->string('classId')->primary();
            $table->string('productId')->nullable();
            $table->string('name')->nullable();
            $table->integer('numberOfStudent')->nullable();
            $table->string('subject')->nullable();
            $table->string('onlineTeacher')->nullable();
            $table->string('classday')->nullable();
            $table->string('classTimeSlot')->nullable();
            $table->date('classStartDate')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->foreign('productId')->references('productId')->on('products')->onDelete('cascade');
            $table->foreign('onlineTeacher')->references('teacherId')->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classes');
    }
}
