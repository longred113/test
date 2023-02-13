<?php

use Faker\Guesser\Name;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->string('studentId')->primary();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('gender')->nullable();
            $table->string('dateOfBirth')->nullable();
            $table->string('country')->nullable();
            $table->string('timeZone')->nullable();
            $table->date('joinedDate')->nullable();
            $table->date('withDrawal')->nullable();
            $table->string('introduction')->nullable();
            $table->string('talkSamId')->nullable();
            $table->integer('basicPoint')->nullable();
            $table->string('campusId')->nullable();
            $table->boolean('type')->nullable();
            $table->string('teacherId')->nullable();
            $table->string('classId')->nullable();
            $table->timestamps();
            $table->foreign('studentId')->references('userId')->on('users')->onDelete('cascade');
            $table->foreign('campusId')->references('campusId')->on('campuses')->onDelete('cascade');
            $table->foreign('teacherId')->references('teacherId')->on('teachers')->onDelete('cascade');
            $table->foreign('classId')->references('classId')->on('classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
