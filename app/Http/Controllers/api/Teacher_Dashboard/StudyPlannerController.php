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
use Illuminate\Validation\Rule;


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

    public function getToDoListOfStudent($classId, $studentId)
    {
        $studentStudyPlanner = MatchedActivities::join('student_class_matched_activities','matched_activities.matchedActivityId', '=', 'student_class_matched_activities.matchedActivityId')
        ->where('studentId', $studentId)->where('classId', $classId)->get();

        $todoList = $studentStudyPlanner->where('status', 'to-do');
        $doneList = $studentStudyPlanner->where('status', 'done');
        $incomplete = $studentStudyPlanner->where('status', 'incomplete');
        return $this->successStudyPlannerRequest($studentStudyPlanner);
    }

    public function updateStatusOfStudyPlanner($studentClMaActivityId)
    {
        $matchedActivity = StudentMatchedActivities::find($studentClMaActivityId);
        $validator = Validator::make($this->request->all, [
            'type' => [Rule::in(['to-do', 'incomplete', 'done'])],
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
