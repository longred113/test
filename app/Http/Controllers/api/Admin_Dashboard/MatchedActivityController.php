<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchedActivityResource;
use App\Models\MatchedActivities;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MatchedActivityController extends Controller
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
        $matchedActivityData = MatchedActivityResource::collection(MatchedActivities::all());
        return $this->successMatchedActivityRequest($matchedActivityData);
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
            // 'productId' => 'required',
            'name' => 'string|required',
            'type' => 'string|required',
            'time' => 'integer|required',
            // 'unitId' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $matchedActivityId = IdGenerator::generate(['table'=>'matched_activities', 'trow' => 'matchedActivityId', 'length' => 7, 'prefix' => 'MA']);
        $params = [
            'matchedActivityId' => $matchedActivityId,
            'productId' => $this->request['productId'],
            'name' => $this->request['name'],
            'type' => $this->request['type'],
            'time' => $this->request['time'],
            'unitId' => $this->request['unitId'],
        ];
        $newMatchedActivityData = new MatchedActivityResource(MatchedActivities::create($params));
        return $this->successMatchedActivityRequest($newMatchedActivityData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $matchedActivityId
     * @return \Illuminate\Http\Response
     */
    public function show($matchedActivityId)
    {
        $matchedActivity = MatchedActivities::find($matchedActivityId);
        $matchedActivityData = new MatchedActivityResource($matchedActivity);
        return $this->successMatchedActivityRequest($matchedActivityData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $matchedActivityId
     * @return \Illuminate\Http\Response
     */
    public function update($matchedActivityId)
    {
        $matchedActivity = MatchedActivities::find($matchedActivityId);
        $validator = Validator::make($this->request->all(), [
            'productId' => 'required',
            'name' => 'string|required',
            'type' => 'string|required',
            'time' => 'integer|required',
            'unitId' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            $matchedActivity['productId'] = $this->request['productId'],
            $matchedActivity['name'] = $this->request['name'],
            $matchedActivity['type'] = $this->request['type'],
            $matchedActivity['time'] = $this->request['time'],
            $matchedActivity['unitId'] = $this->request['unitId'],
        ];

        $newInfoMatchedActivity = $matchedActivity->update($params);
        return $this->successMatchedActivityRequest($newInfoMatchedActivity);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $matchedActivityId
     * @return \Illuminate\Http\Response
     */
    public function destroy($matchedActivityId)
    {
        $matchedActivity = MatchedActivities::find($matchedActivityId);
        $deleteMatchedActivity = $matchedActivity->delete();
        return $this->successMatchedActivityRequest($deleteMatchedActivity);
    }
}
