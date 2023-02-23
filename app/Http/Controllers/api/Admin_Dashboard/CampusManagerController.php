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
        $data = CampusManagerResource::collection(CampusManager::all());
        return $this->successRequest($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = validator::make($this->request->all(), [
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

        $campusManagerId = IdGenerator::generate(['table'=>'campus_mangers', 'trow' => 'campusManagerId', 'length' => 8, 'prefix' => 'CPM']);
        $params = [
            'campusManagerId' => $campusManagerId,
            'name' => $this->request('name'),
            'email' => $this->request('email'),
            'gender' => $this->request('gender'),
            'dateOfBirth' => $this->request('dateOfBirth'),
            'country' => $this->request('country'),
            'timeZone' => $this->request('timeZone'),
            'startDate' => $this->request('startDate'),
            'resignation' => $this->request('resignation'),
            'campusId' => $this->request('campusId'),
            'memo' => $this->request('memo'),
            'offlineStudentId' => $this->request('offlineStudentId'),
            'offlineTeacherId' => $this->request('offlineTeacherId')
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
    public function update($campusManagerId)
    {
        $campusManager = CampusManager::find($campusManagerId);
        if(empty($this->request['name'])) {
            $this->request['name'] = $campusManager['name'];
        }
        // if(empty($this->request['email'])) {
        //     $this->request['email'] = $campusManager['email'];
        // }
        // if(empty($this->request['gender'])) {
        //     $this->request['gender'] = $campusManager['gender'];
        // }
        // if(empty($this->request['dateOfBirth'])) {
        //     $this->request['dateOfBirth'] = $campusManager['dateOfBirth'];
        // }
        // if(empty($this->request['country'])) {
        //     $this->request['country'] = $campusManager['country'];
        // }
        // if(empty($this->request['timeZone'])) {
        //     $this->request['timeZone'] = $campusManager['timeZone'];
        // }
        // if(empty($this->request['startDate'])) {
        //     $this->request['startDate'] = $campusManager['startDate'];
        // }
        // if(empty($this->request['resignation'])) {
        //     $this->request['resignation'] = $campusManager['resignation'];
        // }
        //  if(empty($this->request['campusId'])) {
        //     $this->request['campusId'] = $campusManager['campusId'];
        // }
        // if(empty($this->request['memo'])) {
        //     $this->request['memo'] = $campusManager['memo'];
        // }
        // if(empty($this->request['offlineStudentId'])) {
        //     $this->request['offlineStudentId'] = $campusManager['offlineStudentId'];
        // }
        // if(empty($this->request['offlineTeacherId'])) {
        //     $this->request['offlineTeacherId'] = $campusManager['offlineTeacherId'];
        // }
        $validator = validator::make($this->request->all(), [
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
            $campusManager['name'] = $this->request['name'],
            $campusManager['email'] = $this->request['email'],
            $campusManager['gender'] = $this->request['gender'],
            $campusManager['dateOfBirth'] = $this->request['dateOfBirth'],
            $campusManager['country'] = $this->request['country'],
            $campusManager['timeZone'] = $this->request['timeZone'],
            $campusManager['startDate'] = $this->request['startDate'],
            $campusManager['resignation'] = $this->request['resignation'],
            $campusManager['campusId'] = $this->request['campusId'],
            $campusManager['memo'] = $this->request['memo'],
            $campusManager['offlineStudentId'] = $this->request['offlineStudentId'],
            $campusManager['offlineTeacherId'] = $this->request['offlineTeacherId'],
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