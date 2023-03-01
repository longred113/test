<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkForTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->foreign('campusId')->references('campusId')->on('campuses')->onDelete('cascade');
            $table->foreign('enrollmentId')->references('enrollmentId')->on('enrollments')->onDelete('cascade');
        });
        Schema::table('campus_managers', function (Blueprint $table) {
            $table->foreign('campusId')->references('campusId')->on('campuses')->onDelete('cascade');
            $table->foreign('offlineStudentId')->references('studentId')->on('students')->onDelete('cascade');
            $table->foreign('offlineTeacherId')->references('teacherId')->on('teachers')->onDelete('cascade');
        });
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreign('campusId')->references('campusId')->on('campuses')->onDelete('cascade');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('roleId')->references('roleId')->on('roles')->onDelete('cascade');
            $table->foreign('parentId')->references('parentId')->on('parents')->onDelete('cascade');
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
            $table->foreign('campusManagerId')->references('campusManagerId')->on('campus_managers')->onDelete('cascade');
            $table->foreign('teacherId')->references('teacherId')->on('teachers')->onDelete('cascade');
        });
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreign('campusId')->references('campusId')->on('campuses')->onDelete('cascade');
        });
        Schema::table('product_packages', function (Blueprint $table) {
            $table->foreign('packageId')->references('packageId')->on('packages')->onDelete('cascade');
            $table->foreign('productId')->references('productId')->on('products')->onDelete('cascade');
        });
        Schema::table('class_products', function (Blueprint $table) {
            $table->foreign('classId')->references('classId')->on('classes')->onDelete('cascade');
            $table->foreign('productId')->references('productId')->on('products')->onDelete('cascade');
        });
        Schema::table('student_products', function (Blueprint $table) {
            $table->foreign('productId')->references('productId')->on('products')->onDelete('cascade');
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
        });
        Schema::table('matched_activities', function (Blueprint $table) {
            $table->foreign('productId')->references('productId')->on('products')->onDelete('cascade');
            $table->foreign('unitId')->references('unitId')->on('units')->onDelete('cascade');
        });
        Schema::table('class_match_activities', function (Blueprint $table) {
            $table->foreign('classId')->references('classId')->on('classes')->onDelete('cascade');
            $table->foreign('matchedActivityId')->references('matchedActivityId')->on('matched_activities')->onDelete('cascade');
        });
        Schema::table('student_class_matched_activities', function (Blueprint $table) {
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
            $table->foreign('matchedActivityId')->references('matchedActivityId')->on('matched_activities')->onDelete('cascade');
            $table->foreign('classId')->references('classId')->on('classes')->onDelete('cascade');
        });
        Schema::table('student_classes', function (Blueprint $table) {
            $table->foreign('classId')->references('classId')->on('classes')->onDelete('cascade');
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
        });
        Schema::table('classes', function (Blueprint $table) {
            $table->foreign('onlineTeacher')->references('teacherId')->on('teachers')->onDelete('cascade');
        });
        Schema::table('class_feedbacks', function (Blueprint $table) {
            $table->foreign('teacherId')->references('teacherId')->on('teachers')->onDelete('cascade');
            $table->foreign('classId')->references('classId')->on('classes')->onDelete('cascade');
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
            $table->foreign('campusId')->references('campusId')->on('campuses')->onDelete('cascade');
        });
        Schema::table('units', function (Blueprint $table) {
            $table->foreign('productId')->references('productId')->on('products')->onDelete('cascade');
        });
        Schema::table('class_reports', function (Blueprint $table) {
            $table->foreign('teacherId')->references('teacherId')->on('teachers')->onDelete('cascade');
            $table->foreign('classId')->references('classId')->on('classes')->onDelete('cascade');
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
            $table->foreign('campusId')->references('campusId')->on('campuses')->onDelete('cascade');
        });
        Schema::table('class_boards', function (Blueprint $table) {
            $table->foreign('class')->references('classId')->on('classes')->onDelete('cascade');
        });
        Schema::table('class_materials', function (Blueprint $table) {
            $table->foreign('class')->references('classId')->on('classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
