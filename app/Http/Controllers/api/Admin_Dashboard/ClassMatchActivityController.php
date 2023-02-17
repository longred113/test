<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassMatchActivityResource;
use App\Models\ClassMatchActivities;
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
            return $validator->errors();
        }

        $params = [
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
    public function show($classId,$matchedActivityId)
    {
        $classMatchedActivity = ClassMatchActivities::find($classId, $matchedActivityId);
        $classMatchedActivityData = new ClassMatchActivityResource($classMatchedActivity);
        return $this->successClassMatchActivityRequest($classMatchedActivityData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($classId, $matchedActivityId)
    {
        $classMatchedActivity = ClassMatchActivities::find($classId, $matchedActivityId);
        $validator = Validator::make($this->request->all(), [
            'classId' => 'string|required',
            'matchedActivityId' => 'string|required',
            'status' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            $classMatchedActivity['classId'] = $this->request['classId'],
            $classMatchedActivity['matchedActivityId'] = $this->request['matchedActivityId'],
            $classMatchedActivity['status'] = $this->request['status'],
        ];

        $newClassMatchedActivityData = $classMatchedActivity->update($params);
        return $this->successClassMatchActivityRequest($newClassMatchedActivityData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($classId, $matchedActivityId)
    {
        $classMatchedActivity = ClassMatchActivities::find($classId, $matchedActivityId);
        $deleteClassMatchedActivity = $classMatchedActivity->delete();
        return $this->successClassMatchActivityRequest($deleteClassMatchedActivity);
    }
}
