<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampusManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campus_mangers', function (Blueprint $table) {
            $table->string('campusManagerId')->primary();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('gender')->nullable();
            $table->date('dateOfBirth')->nullable();
            $table->string('country')->nullable();
            $table->string('timeZone')->nullable();
            $table->string('startDate')->nullable();
            $table->boolean('resignation')->nullable();
            $table->string('campusId')->nullable();
            $table->string('memo');
            $table->string('offlineStudentId');
            $table->string('offlineTeacherId');
            $table->timestamps();
            $table->foreign('campusManagerId')->references('userId')->on('users')->onDelete('cascade');
            $table->foreign('campusId')->references('campusId')->on('campuses')->onDelete('cascade');
            $table->foreign('offlineStudentId')->references('studentId')->on('students')->onDelete('cascade');
            $table->foreign('offlineTeacherId')->references('teacherId')->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campus_managers');
    }
}
