<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\GroupActivities;
use App\Models\Groups;
use App\Models\MatchedActivities;
use App\Models\TblGroups;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
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
        $groups = TblGroups::all();
        return $this->successGroupRequest($groups); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = Validator::make($this->request->all(),[
            'name' => 'string|required',
            'matchActivities' => 'required|array',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try{
            foreach($this->request->matchActivities as $matchActivity){
                $newMatchActivity[] = MatchedActivityController::store($matchActivity);
            }
            foreach($newMatchActivity as $matchActivity){
                $matchActivityIds[] = $matchActivity['matchedActivityId'];
            }
            $groupId = IdGenerator::generate(['table' => 'tbl_groups', 'trow' => 'groupId', 'length' => 7, 'prefix' => 'GR']);
            $params = [
                'groupId' => $groupId,
                'name' => $this->request->name,
            ];
            $group = TblGroups::create($params);
            
            $groupActivityParams = [
                'groupId' => $groupId,
                'groupName' => $this->request->name,
                'matchActivityIds' => $matchActivityIds,
            ];
            $new = GroupActivityController::store($groupActivityParams);
        }catch(Exception $e){
            return $e->getMessage();
        }

        return $this->successGroupRequest($new);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($groupId)
    {
        $group = TblGroups::where('groupId', $groupId)->first();
        $group->matchActivity = GroupActivities::join('matched_activities', 'group_activities.matchedActivityId', '=', 'matched_activities.matchedActivityId')
            ->where('group_activities.groupId', $groupId)
            ->select(
                'group_activities.matchedActivityId',
                'group_activities.matchedActivityName',
                'matched_activities.time as duration',
                'matched_activities.type',
            )
            ->get();
        if (empty($group)) {
            return $this->errorBadRequest('Group not found');
        }
        return $this->successGroupRequest($group);
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
        $validator = Validator::make($this->request->all(),[
            'groupId' => 'string|required',
            'groupName' => 'string',
            'matchedActivityId' => 'string',
            'matchedActivityName' => 'string',
            'type' => 'string',
            'time' => 'integer',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try{
            if(!empty($this->request->groupId)){
                if(!empty($this->request->groupName)){
                    $params['name'] = $this->request->groupName;
                    $group = TblGroups::where('groupId', $this->request->groupId)->update($params);
                }
    
                if(!empty($this->request->matchedActivityId)){
                    if(!empty($this->request->matchedActivityName)){
                        $activityParams['name'] = $this->request->matchedActivityName;
                    }
                    if(!empty($this->request->type)){
                        $activityParams['type'] = $this->request->type;
                    }
                    if(!empty($this->request->time)){
                        $activityParams['time'] = $this->request->time;
                    }
                    MatchedActivities::where('matchedActivityId', $this->request->matchedActivityId)->update($activityParams);
                    $groupActivityParams = [
                        'groupName' => $this->request->groupName,
                        'matchedActivityName' => $this->request->matchedActivityName,
                    ];
                    GroupActivities::where('groupId', $this->request->groupId)
                        ->where('matchedActivityId', $this->request->matchedActivityId)
                        ->update($groupActivityParams);
                }
            }
        }catch(Exception $e){
            return $e->getMessage();
        }

        return $this->successGroupRequest('updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($groupId)
    {
        $group = TblGroups::where('groupId', $groupId)->first();
        if (empty($group)) {
            return $this->errorBadRequest('Group not found');
        }
        $group->delete();
        return $this->successGroupRequest($group);
    }
}
