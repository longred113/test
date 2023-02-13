<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassMatchActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_match_activities', function (Blueprint $table) {
            $table->string('classId')->nullable();
            $table->string('matchedActivityId')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->foreign('classId')->references('classId')->on('classes')->onDelete('cascade');
            $table->foreign('matchedActivityId')->references('matchedActivityId')->on('matchedActivities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_match_activities');
    }
}
