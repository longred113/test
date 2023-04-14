<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentMatchedActivityResource;
use App\Models\MatchedActivities;
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
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $studentMatchedActivityId = IdGenerator::generate(['table' => 'student_matched_activities', 'trow' => 'studentMatchedActivityId', 'length' => 8, 'prefix' => 'SMA']);
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

    public function getMatchedActivityFromStudent($studentId)
    {
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
        if (empty($this->request['studentId'])) {
            $this->request['studentId'] = $studentMatchedActivity['studentId'];
        }
        if (empty($this->request['matchedActivity'])) {
            $this->request['matchedActivity'] = $studentMatchedActivity['matchedActivity'];
        }
        if (empty($this->request['name'])) {
            $this->request['name'] = $studentMatchedActivity['name'];
        }
        if (empty($this->request['status'])) {
            $this->request['status'] = $studentMatchedActivity['status'];
        }
        $validator = Validator::make($this->request->all(), [
            'studentId' => 'string|required',
            'matchedActivityId' => 'string|required',
            'name' => 'string',
            'status' => [Rule::in(['to-do', 'incomplete', 'done'])],
        ]);
        if ($validator->fails()) {
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

    public function updateActivate()
    {
        $validator = Validator::make($this->request->all(), [
            'studentId' => 'string|required',
            'matchedActivityId' => 'string|required',
            'activate' => 'integer',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $studentMatchedActivity = StudentMatchedActivities::where('studentId', $this->request['studentId'])
            ->where('matchedActivityId', $this->request['matchedActivityId'])
            ->update(['activate' => $this->request['activate']]);
        return $studentMatchedActivity;
        return $this->successStudentMatchedActivityRequest($studentMatchedActivity);
    }

    public function updateMatchActivityOfStudent()
    {
        $validator = Validator::make($this->request->all(), [
            'studentId' => 'string|required',
            'matchedActivityIds' => 'array|required',
            'status' => [Rule::in(['to-do', 'incomplete', 'done'])],
            'activate' => 'integer',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        
        $studentId = $this->request['studentId'];
        $matchedActivityIds = $this->request['matchedActivityIds'];
        $students = StudentMatchedActivities::where('studentId', $studentId)->delete();
        foreach($matchedActivityIds as $matchedActivityId){
            $studentMatchedActivityId = IdGenerator::generate(['table' => 'student_matched_activities', 'trow' => 'studentMatchedActivityId', 'length' => 8, 'prefix' => 'SMA']);
            $params = [
                'studentMatchedActivityId' => $studentMatchedActivityId,
                'studentId' => $studentId,
                'matchedActivityId' => $matchedActivityId,
                'status' => 'to-do',
            ];
            $matchedActivityName = MatchedActivities::where('matchedActivityId', $matchedActivityId)->pluck('name')->toArray();
            $params['name'] = implode(', ', $matchedActivityName);
            StudentMatchedActivities::create($params);
        }
        return $this->successStudentMatchedActivityRequest();
    }

    public static function updateMultipleMatchedActivity($studentMatchActivityParams)
    {
        $studentId = $studentMatchActivityParams['studentId'];
        $matchedActivityIds = $studentMatchActivityParams['matchedActivityIds'];

        StudentMatchedActivities::where('studentId', $studentId)->delete();
        foreach($matchedActivityIds as $matchedActivityId){
            $studentMatchedActivityId = IdGenerator::generate(['table' => 'student_matched_activities', 'trow' => 'studentMatchedActivityId', 'length' => 8, 'prefix' => 'SMA']);
            $params = [
                'studentMatchedActivityId' => $studentMatchedActivityId,
                'studentId' => $studentId,
                'matchedActivityId' => $matchedActivityId,
                'status' => 'to-do',
                'activate' => 1,
            ];
            $matchedActivityName = MatchedActivities::where('matchedActivityId', $matchedActivityId)->pluck('name')->toArray();
            $params['name'] = implode(', ', $matchedActivityName);
            StudentMatchedActivities::create($params);
        }
    }

    // public static function updateMultipleStudentWithMultipleMatchedActivity($studentMatchActivityParams)
    // {
    //     $studentIds = $studentMatchActivityParams['studentIds'];
    //     $matchedActivityIds = $studentMatchActivityParams['matchedActivityIds'];

    //     foreach($studentIds as $studentId){
    //         $students = StudentMatchedActivities::where('studentId', $studentId)->delete();
    //         foreach($matchedActivityIds as $matchedActivityId){
    //             $studentMatchedActivityId = IdGenerator::generate(['table' => 'student_matched_activities', 'trow' => 'studentMatchedActivityId', 'length' => 8, 'prefix' => 'SMA']);
    //             $params = [
    //                 'studentMatchedActivityId' => $studentMatchedActivityId,
    //                 'studentId' => $studentId,
    //                 'matchedActivityId' => $matchedActivityId,
    //                 'status' => 'to-do',
    //                 'activate' => 1,
    //             ];
    //             $matchedActivityName = MatchedActivities::where('matchedActivityId', $matchedActivityId)->pluck('name')->toArray();
    //             $params['name'] = implode(', ', $matchedActivityName);
    //             StudentMatchedActivities::create($params);
    //         }
    //     }
    // }
}
