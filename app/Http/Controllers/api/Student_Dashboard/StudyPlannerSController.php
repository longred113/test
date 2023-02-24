<?php

namespace App\Http\Controllers\api\Student_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
use App\Models\MatchedActivities;
use App\Models\StudentClasses;
use App\Models\Students;
use App\Models\Teachers;
use Illuminate\Http\Request;

class StudyPlannerSController extends Controller
{
    protected Request $request;

    public function __construct(
        Request $request
        )       
    {
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showStuClass($studentId)
    {
        $studentClass = StudentClasses::join('classes', 'student_classes.classId', "=", 'classes.classId')
        ->where('studentId',$studentId)->get();
        return($studentClass);
    }
    public function showStudyPlanner()
    {
    
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}