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
    ) {
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
            ->where('studentId', $studentId)->get();
        return ($studentClass);
    }
    public function showStudyPlanner($studentId)
    {
        $studentStudyPlanner = classes::join('student_classes', 'classes.classId', '=', 'student_classes.classId')
            ->join('class_match_activities', 'classes.classId', '=', 'class_match_activities.classId')->where('studentId', $studentId)->get();

        $matchedActivityIds = $studentStudyPlanner->pluck('matchedActivityId')->toArray();
        $studyPlanner = MatchedActivities::whereIn('matchedActivityId', $matchedActivityIds)->get();

        foreach ($studyPlanner as $todoList) {
            if (!empty($todoList['type'] == 'todo')) {
                $todoStudyPlanner = $todoList;
            }
            if (!empty($todoList['type'] == 'done')) {
                $doneStudyPlanner = $todoList;
            }
            if (!empty($todoList['type'] == 'incomplete')) {
                $incompleteStudyPlanner = $todoList;
            }
        }
        return $studyPlanner;
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
