<?php

namespace App\Http\Controllers\api\Teacher_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
use App\Models\MatchedActivities;
use App\Models\StudentClasses;
use App\Models\Students;
use App\Models\Teachers;
use Illuminate\Http\Request;

class StudyPlannerController extends Controller
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
    public function showClasses($teacherId)
    {
        $classes = Classes::where('onlineTeacher', $teacherId)->get();
        return $this->successClassRequest($classes);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showStudentInClass($classId)
    {
        $studentIds = StudentClasses::where('classId', $classId)->get('studentId');
        $students = Students::whereIn('studentId', $studentIds)->get();

        $studentStudyPlanner = classes::join('student_classes', 'classes.classId', '=', 'student_classes.classId')
        ->join('class_match_activities', 'classes.classId', '=', 'class_match_activities.classId')->get();
        foreach($studentStudyPlanner as $student) {
            $studyPlanner = MatchedActivities::where('matchedActivityId', $student['matchedActivityId'])->get();
        }
        return $studyPlanner;
        return $this->successStudentRequest($students);
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
