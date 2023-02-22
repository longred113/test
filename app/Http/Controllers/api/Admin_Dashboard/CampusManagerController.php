<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CampusManager;
use App\Http\Resources\CampusManager as CampusManagerResource;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class CampusManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = CampusManagerResource::collection(CampusManager::all());
        return $this->successRequest($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = validator::make($request->all(), [
            'name' => 'required',
            // 'email' => 'required',
            // 'gender' => 'required',
            // 'dateOfBirth' => 'required',
            // 'country' => 'required',
            // 'timeZone' => 'required',
            // 'startDate' => 'required',
            // 'resignation' => 'required',
            // 'campusId' => 'required',
            // 'memo' => 'required',
            // 'offlineStudentId' => 'required',
            // 'offlineTeacherId' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $campusManagerId = IdGenerator::generate(['table'=>'campus_mangers', 'trow' => 'campusManagerId', 'length' => 9, 'prefix' => 'CPM-']);
        $params = [
            'campusManagerId' => $campusManagerId,
            'name' => request('name'),
            'email' => request('email'),
            'gender' => request('gender'),
            'dateOfBirth' => request('dateOfBirth'),
            'country' => request('country'),
            'timeZone' => request('timeZone'),
            'startDate' => request('startDate'),
            'resignation' => request('resignation'),
            'campusId' => request('campusId'),
            'memo' => request('memo'),
            'offlineStudentId' => request('offlineStudentId'),
            'offlineTeacherId' => request('offlineTeacherId')
        ];
        $newCampusManager = new CampusManagerResource(CampusManager::create($params));
        return $newCampusManager;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($campusManagerId)
    {
        $CampusManager = CampusManager::find($campusManagerId);
        $CampusManagerData = new CampusManagerResource($CampusManager);
        return $this->successRequest($CampusManagerData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $campusManagerId)
    {
        $campusManager = CampusManager::find($campusManagerId);
        if(empty($request->name)) {
            $request['name'] = $campusManager['name'];
        }
        // if(empty($request->email)) {
        //     $request['email'] = $campusManager['email'];
        // }
        // if(empty($request->gender)) {
        //     $request['gender'] = $campusManager['gender'];
        // }
        // if(empty($request->dateOfBirth)) {
        //     $request['dateOfBirth'] = $campusManager['dateOfBirth'];
        // }
        // if(empty($request->country)) {
        //     $request['country'] = $campusManager['country'];
        // }
        // if(empty($request->timeZone)) {
        //     $request['timeZone'] = $campusManager['timeZone'];
        // }
        // if(empty($request->startDate)) {
        //     $request['startDate'] = $campusManager['startDate'];
        // }
        // if(empty($request->resignation)) {
        //     $request['resignation'] = $campusManager['resignation'];
        // }
        //  if(empty($request->campusId)) {
        //     $request['campusId'] = $campusManager['campusId'];
        // }
        // if(empty($request->memo)) {
        //     $request['memo'] = $campusManager['memo'];
        // }
        // if(empty($request->offlineStudentId)) {
        //     $request['offlineStudentId'] = $campusManager['offlineStudentId'];
        // }
        // if(empty($request->offlineTeacherId)) {
        //     $request['offlineTeacherId'] = $campusManager['offlineTeacherId'];
        // }
        $validator = validator::make($request->all(), [
            'name' => 'required',
            // 'email' => 'required',
            // 'gender' => 'required',
            // 'dateOfBirth' => 'required',
            // 'country' => 'required',
            // 'timeZone' => 'required',
            // 'startDate' => 'required',
            // 'resignation' => 'required',
            // 'campusId' => 'required',
            // 'memo' => 'required',
            // 'offlineStudentId' => 'required',
            // 'offlineTeacherId' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $params = [
            $campusManager['name'] = $request['name'],
            $campusManager['email'] = $request['email'],
            $campusManager['gender'] = $request['gender'],
            $campusManager['dateOfBirth'] = $request['dateOfBirth'],
            $campusManager['country'] = $request['country'],
            $campusManager['timeZone'] = $request['timeZone'],
            $campusManager['startDate'] = $request['startDate'],
            $campusManager['resignation'] = $request['resignation'],
            $campusManager['campusId'] = $request['campusId'],
            $campusManager['memo'] = $request['memo'],
            $campusManager['offlineStudentId'] = $request['offlineStudentId'],
            $campusManager['offlineTeacherId'] = $request['offlineTeacherId'],
        ];
        $newInfoCampusManager = $campusManager->update($params);
        return $this->successRequest($newInfoCampusManager);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($campusManagerId)
    {
        $campusManager = CampusManager::find($campusManagerId);
        $deleteCampusManager = $campusManager->delete();
        return $this->successRequest($deleteCampusManager);
    }
}