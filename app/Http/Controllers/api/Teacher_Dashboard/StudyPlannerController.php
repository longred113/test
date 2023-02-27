<?php

namespace App\Http\Controllers\api\Teacher_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
use App\Models\MatchedActivities;
use App\Models\Products;
use App\Models\StudentClasses;
use App\Models\StudentMatchedActivities;
use App\Models\Students;
use App\Models\Teachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        return $this->successStudentRequest($students);
    }

    public function getToDoListOfStudent($studentId)
    {
        // $studentStudyPlanner = classes::join('student_classes', 'classes.classId', '=', 'student_classes.classId')
        // ->join('class_match_activities', 'classes.classId', '=', 'class_match_activities.classId')->where('studentId', $studentId)->get();
        
        // $matchedActivityIds = $studentStudyPlanner->pluck('matchedActivityId')->toArray();
        // $studyPlanner = MatchedActivities::whereIn('matchedActivityId', $matchedActivityIds)->get();
        
        // foreach($studyPlanner as $todoList) {
        //     if(!empty($todoList['type'] == 'todo')) {
        //         $todoStudyPlanner = $todoList;
        //     }
        //     if(!empty($todoList['type'] == 'done')) {
        //         $doneStudyPlanner = $todoList;
        //     }
        //     if(!empty($todoList['type'] == 'incomplete')) {
        //         $incompleteStudyPlanner = $todoList;
        //     }
        // }
        // return $studyPlanner;

        $majoinCP = Students::join('student_matched_activities','students.studentId', '=', 'student_matched_activities.studentId')->get();
        return $majoinCP;
    }

    public function updateStatusOfStudyPlanner($matchedActivityId)
    {
        $matchedActivity = MatchedActivities::find($matchedActivityId);
        $validator = Validator::make($this->request->all, [
            'type' => 'string',
        ]);
        if($validator->fails()){
            return $validator->errors();
        }

        $params = [
            $matchedActivity['type'] = $this->request['type'],
        ];
        $newType = $matchedActivity->update($params);
        return $this->successStudyPlannerRequest($newType);
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
