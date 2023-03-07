<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentMatchedActivityResource;
use App\Models\StudentMatchedActivities;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentMatchedActivityController extends Controller
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
        $studentMatchedActivitiesData = StudentMatchedActivityResource::collection(StudentMatchedActivities::all());
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
            'name' => 'string',
        ]);
        if($validator->fails()){
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $studentMatchedActivityId = IdGenerator::generate(['table'=>'student_matched_activities', 'trow' => 'studentMatchedActivityId', 'length' => 8, 'prefix' => 'SMA']);
        $params = [
            'studentMatchedActivityId' => $studentMatchedActivityId,
            'studentId' => $this->request['studentId'],
            'matchedActivityId' => $this->request['matchedActivityId'],
            'name' => $this->request['name'],
            'status' => 'to-do',
        ];

        $newStudentMatchedActivity = new StudentMatchedActivityResource(StudentMatchedActivities::create($params));
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
        $studentMatchedActivity = StudentMatchedActivities::find($studentMatchedActivityId);
        $studentMatchedActivityData = new StudentMatchedActivityResource($studentMatchedActivity);
        return $this->successStudentMatchedActivityRequest($studentMatchedActivityData);
    }

    public function getMatchedActivityFromStudent($studentId) {
        $studentMatchedActivity = StudentMatchedActivities::where('studentId', $studentId)->get();
        return $this->successStudentMatchedActivityRequest($studentMatchedActivity);
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
        $studentMatchedActivity = StudentMatchedActivities::find($studentMatchedActivityId);
        if(empty($this->request['studentId'])){
            $this->request['studentId'] = $studentMatchedActivity['studentId'];
        }
        if(empty($this->request['matchedActivity'])){
            $this->request['matchedActivity'] = $studentMatchedActivity['matchedActivity'];
        }
        if(empty($this->request['name'])){
            $this->request['name'] = $studentMatchedActivity['name'];
        }
        $validator = Validator::make($this->request->all(), [
            'studentId' => 'string|required',
            'matchedActivityId' => 'string|required',
            'name' => 'string',
            'status' => [Rule::in(['to-do', 'incomplete', 'done'])],
        ]);
        if($validator->fails()){
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $studentMatchedActivity['studentId'] = $this->request['studentId'],
            $studentMatchedActivity['matchedActivityId'] = $this->request['matchedActivityId'],
            $studentMatchedActivity['name'] = $this->request['name'],
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
        $studentMatchedActivity = StudentMatchedActivities::find($studentMatchedActivityId);
        $deleteStudentMatchedActivity = $studentMatchedActivity->delete();
        return $this->successStudentMatchedActivityRequest($deleteStudentMatchedActivity);
    }
}
