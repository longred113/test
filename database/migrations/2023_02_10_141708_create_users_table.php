<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('userId')->primary();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('roleId')->nullable();
            $table->timestamps();
            $table->foreign('roleId')->references('roleId')->on('roles')->onDelete('cascade'); 
            $table->foreign('userId')->references('studentId')->on('students')->onDelete('cascade'); 
            $table->foreign('userId')->references('teacherId')->on('teachers')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
