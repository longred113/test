<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchedActivityResource;
use App\Models\MatchedActivities;
use App\Models\ProductMatchedActivities;
use App\Models\StudentMatchedActivities;
use Exception;
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
            'time' => 'integer|required',
            // 'unitId' => 'string|required',
            'type' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $matchedActivityId = IdGenerator::generate(['table' => 'matched_activities', 'trow' => 'matchedActivityId', 'length' => 7, 'prefix' => 'MA']);
        $params = [
            'matchedActivityId' => $matchedActivityId,
            'productId' => $this->request['productId'],
            'name' => $this->request['name'],
            'time' => $this->request['time'],
            'unitId' => $this->request['unitId'],
            'type' => $this->request['type'],
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

    public function getMatchActivityFromProduct($productId)
    {
        $matchedActivity = MatchedActivities::where('productId', $productId)->get();
        return $matchedActivity;
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
        if (empty($this->request['productId'])) {
            $this->request['productId'] = $matchedActivity['productId'];
        }
        if (empty($this->request['name'])) {
            $this->request['name'] = $matchedActivity['name'];
        }
        if (empty($this->request['time'])) {
            $this->request['time'] = $matchedActivity['time'];
        }
        if (empty($this->request['unitId'])) {
            $this->request['unitId'] = $matchedActivity['unitId'];
        }
        if (empty($this->request['type'])) {
            $this->request['type'] = $matchedActivity['type'];
        }
        $validator = Validator::make($this->request->all(), [
            // 'productId' => 'string',
            'name' => 'string|required',
            'time' => 'integer|required',
            // 'unitId' => 'string',
            'type'  => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $matchedActivity['name'] = $this->request['name'],
            $matchedActivity['time'] = $this->request['time'],
            $matchedActivity['unitId'] = $this->request['unitId'],
            $matchedActivity['type'] = $this->request['type'],
        ];

        try{
            $newInfoMatchedActivity = $matchedActivity->update($params);
            ProductMatchedActivities::where('matchedActivityId', $matchedActivityId)->update(['matchedActivityName' => $this->request['name']]);
            StudentMatchedActivities::where('matchedActivityId', $matchedActivityId)->update(['name' => $this->request['name']]);
        } catch(Exception $e){
            $e->getMessage();
        }
        return $this->successMatchedActivityRequest();
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
