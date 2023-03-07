<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassMatchActivityResource;
use App\Models\ClassMatchActivities;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassMatchActivityController extends Controller
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
        $classMatchedActivities = ClassMatchActivityResource::collection(ClassMatchActivities::all());
        return $this->successClassMatchActivityRequest($classMatchedActivities);
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
            'classId' => 'string|required',
            'matchedActivityId' => 'string|required',
            'status' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $classMatchActivityId = IdGenerator::generate(['table' => 'class_match_activities', 'trow' => 'classMatchActivityId', 'length' => 8, 'prefix' => 'CMA']);
        $params = [
            'classMatchActivityId' => $classMatchActivityId,
            'classId' => $this->request['classId'],
            'matchedActivityId' => $this->request['matchedActivityId'],
            'status' => $this->request['status'],
        ];

        $newClassMatchedActivity = new ClassMatchActivityResource(ClassMatchActivities::create($params));
        return $this->successClassMatchActivityRequest($newClassMatchedActivity);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($classMatchActivityId)
    {
        $classMatchActivity = ClassMatchActivities::find($classMatchActivityId);
        $classMatchActivityData = new ClassMatchActivityResource($classMatchActivity);
        return $this->successClassMatchActivityRequest($classMatchActivityData);
    }

    public function displayByClass($classId)
    {
        $classMatchedActivity = ClassMatchActivities::where('classId', $classId)->get();
        return $this->successClassMatchActivityRequest($classMatchedActivity);
    }

    public function displayByMatchActivity($matchedActivity)
    {
        $classMatchedActivity = ClassMatchActivities::where('matchedActivityId', $matchedActivity)->get();
        return $this->successClassMatchActivityRequest($classMatchedActivity);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($classMatchActivityId)
    {
        $classMatchActivity = ClassMatchActivities::find($classMatchActivityId);
        if (empty($this->request['classId'])) {
            $this->request['classId'] = $classMatchActivity['classId'];
        }
        if (empty($this->request['matchedActivityId'])) {
            $this->request['matchedActivityId'] = $classMatchActivity['matchedActivityId'];
        }
        if (empty($this->request['status'])) {
            $this->request['status'] = $classMatchActivity['status'];
        }
        $validator = Validator::make($this->request->all(), [
            'classId' => 'string|required',
            'matchedActivityId' => 'string|required',
            'status' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $params = [
            'classId' => $this->request['classId'],
            'matchedActivityId' => $this->request['matchedActivityId'],
            'status' => $this->request['status'],
        ];

        $newClassMatchedActivityData = $classMatchActivity->update($params);
        return $this->successClassMatchActivityRequest($newClassMatchedActivityData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($classMatchActivityId)
    {
        $classMatchActivity = ClassMatchActivities::find($classMatchActivityId);
        $deleteClassMatchedActivity = $classMatchActivity->delete();
        return $this->successClassMatchActivityRequest($deleteClassMatchedActivity);
    }
}
