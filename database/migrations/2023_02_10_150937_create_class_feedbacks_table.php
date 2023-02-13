<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_feedbacks', function (Blueprint $table) {
            $table->string('teacherId')->nullable();
            $table->string('classId')->nullable();
            $table->string('studentId')->nullable();
            $table->string('campusId')->nullable();
            $table->date('date')->nullable();
            $table->integer('satisfaction')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
            $table->foreign('teacherId')->references('teacherId')->on('teachers')->onDelete('cascade');
            $table->foreign('classId')->references('classId')->on('classes')->onDelete('cascade');
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
            $table->foreign('campusId')->references('campusId')->on('campuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_feedbacks');
    }
}
