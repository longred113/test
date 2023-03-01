<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentClassMatchedActivityResource;
use App\Models\StudentClassMatchedActivities;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentClassMatchedActivityController extends Controller
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
    public function index()
    {
        $studentMatchedActivitiesData = StudentClassMatchedActivityResource::collection(StudentClassMatchedActivities::all());
        return $this->successStudentMatchedActivityRequest($studentMatchedActivitiesData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = Validator::make($this->request->all(), [
            'studentId' => 'string|required',
            'matchedActivityId' => 'string|required',
            'classId' => 'string|required',
        ]);
        if($validator->fails()){
            return $validator->errors();
        }

        $studentMatchedActivityId = IdGenerator::generate(['table'=>'student_class_matched_activities', 'trow' => 'studentClMaActivityId', 'length' => 9, 'prefix' => 'SCMA']);
        $params = [
            'studentClMaActivityId' => $studentMatchedActivityId,
            'studentId' => $this->request['studentId'],
            'matchedActivityId' => $this->request['matchedActivityId'],
            'classId' => $this->request['classId'],
            'status' => 'to-do',
        ];

        $newStudentMatchedActivity = new StudentClassMatchedActivityResource(StudentClassMatchedActivities::create($params));
        return $this->successStudentMatchedActivityRequest($newStudentMatchedActivity);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($studentMatchedActivityId)
    {
        $studentMatchedActivity = StudentClassMatchedActivities::find($studentMatchedActivityId);
        $studentMatchedActivityData = new StudentClassMatchedActivityResource($studentMatchedActivity);
        return $this->successStudentMatchedActivityRequest($studentMatchedActivityData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($studentMatchedActivityId)
    {
        $studentMatchedActivity = StudentClassMatchedActivities::find($studentMatchedActivityId);
        if(empty($this->request['studentId'])){
            $this->request['studentId'] = $studentMatchedActivity['studentId'];
        }
        if(empty($this->request['matchedActivity'])){
            $this->request['matchedActivity'] = $studentMatchedActivity['matchedActivity'];
        }
        $validator = Validator::make($this->request->all(), [
            'studentId' => 'string|required',
            'matchedActivityId' => 'string|required',
            'status' => [Rule::in(['to-do', 'incomplete', 'done'])],
        ]);
        if($validator->fails()){
            return $validator->errors();
        }

        $params = [
            $studentMatchedActivity['studentId'] = $this->request['studentId'],
            $studentMatchedActivity['matchedActivityId'] = $this->request['matchedActivityId'],
            $studentMatchedActivity['classId'] = $this->request['classId'],
            $studentMatchedActivity['status'] = $this->request['status'],
        ];
        $newInfStudentMatchedActivity = $studentMatchedActivity->update($params);
        return $this->successStudentMatchedActivityRequest($newInfStudentMatchedActivity);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($studentMatchedActivityId)
    {
        $studentMatchedActivity = StudentClassMatchedActivities::find($studentMatchedActivityId);
        $deleteStudentMatchedActivity = $studentMatchedActivity->delete();
        return $this->successStudentMatchedActivityRequest($deleteStudentMatchedActivity);
    }
}
