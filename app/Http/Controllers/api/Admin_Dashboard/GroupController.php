<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\GroupActivities;
use App\Models\Groups;
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
            'matchActivityIds' => 'required|array',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try{
            $groupId = IdGenerator::generate(['table' => 'tbl_groups', 'trow' => 'groupId', 'length' => 7, 'prefix' => 'GR']);
            $params = [
                'groupId' => $groupId,
                'name' => $this->request->name,
            ];
            $group = TblGroups::create($params);
            
            $groupActivityParams = [
                'groupId' => $groupId,
                'groupName' => $this->request->name,
                'matchActivityIds' => $this->request->matchActivityIds,
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
    public function update($groupId)
    {
        $group = TblGroups::where('groupId', $groupId)->first();
        if (empty($group)) {
            return $this->errorBadRequest('Group not found');
        }
        $validator = Validator::make($this->request->all(),[
            'name' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            'name' => $this->request->name,
        ];
        $group->update($params);

        return $this->successGroupRequest($group);
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
