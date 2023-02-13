<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollment', function (Blueprint $table) {
            $table->string('studentName')->nullable();
            $table->string('studentId')->nullable();
            $table->string('talkSamId')->nullable();
            $table->string('campusName')->nullable();
            $table->string('level')->nullable();
            $table->string('subject')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enrollment');
    }
}
