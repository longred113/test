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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->string('enrollmentId')->primary();
            $table->string('studentId')->nullable();
            $table->string('talkSamId')->nullable();
            $table->string('campusId')->nullable();
            $table->string('level')->nullable();
            $table->string('subject')->nullable();
            $table->string('status')->nullable();
            $table->date('submittedDate')->nullable();
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
        Schema::dropIfExists('enrollments');
    }
}
