<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->string('teacherId')->primary();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('gender')->nullable();
            $table->date('dateOfBirth')->nullable();
            $table->string('status')->nullable();
            $table->boolean('activate')->nullable();
            $table->string('country')->nullable();
            $table->string('timeZone')->nullable();
            $table->string('startDate')->nullable();
            $table->boolean('resignation')->nullable();
            $table->string('resume')->nullable();
            $table->string('certificate')->nullable();
            $table->string('contract')->nullable();
            $table->integer('basicPoint')->nullable();
            $table->string('campusId')->nullable();
            $table->string('type')->nullable();
            $table->string('talkSamId')->nullable();
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
        Schema::dropIfExists('teachers');
    }
}
