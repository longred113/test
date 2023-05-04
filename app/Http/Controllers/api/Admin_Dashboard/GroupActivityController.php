<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\GroupActivities;
use App\Models\MatchedActivities;
use App\Models\TblGroups;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupActivityController extends Controller
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
        $groupMatchActivities = GroupActivities::all();
        return $this->successGroupActivityRequest($groupMatchActivities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store($groupActivityParams)
    {
        try {
            foreach ($groupActivityParams['matchActivityIds'] as $matchActivityId) {
                $groupActivityId = IdGenerator::generate(['table' => 'group_activities', 'trow' => 'groupActivityId', 'length' => 7, 'prefix' => 'GA']);
                $matchedActivityName = MatchedActivities::where('matchedActivityId', $matchActivityId)->first()->name;
                $params = [
                    'groupActivityId' => $groupActivityId,
                    'groupId' => $groupActivityParams['groupId'],
                    'groupName' => $groupActivityParams['groupName'],
                    'matchedActivityId' => $matchActivityId,
                    'matchedActivityName' => $matchedActivityName,
                ];

                $new [] = GroupActivities::create($params);
                
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $new;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($groupId)
    {
        // $groupMatchActivity = GroupActivities::join('matched_activities', 'group_activities.matchedActivityId', '=', 'matched_activities.matchedActivityId')
        // ->where('groupId', $groupId)->get();
        // return $this->successGroupActivityRequest($groupMatchActivity);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($groupId)
    {
        $groupActivity = GroupActivities::where('groupId', $groupId)->get();
        $groupActivity->delete();
        return $this->successGroupActivityRequest($groupActivity);
    }
}
