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
            $table->string('name')->nullable();
            $table->integer('numberOfStudent')->nullable();
            $table->string('subject')->nullable();
            $table->string('onlineTeacher')->nullable();
            $table->string('classday')->nullable();
            $table->string('classTimeSlot')->nullable();
            $table->date('classStartDate')->nullable();
            $table->string('status')->nullable();
            $table->string('typeOfClass')->nullable();
            $table->string('initialTextbook')->nullable();
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
        Schema::dropIfExists('classes');
    }
}
