<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CampusManager;
use App\Http\Resources\CampusManager as CampusManagerResource;
use App\Models\Campus;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Str;

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
        return $this->successCampusManagerRequest($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store($teacherParams)
    {
        // $validator = validator::make($this->request->all(), [
        //     'name' => 'required',
        //     // 'email' => 'required',
        //     // 'gender' => 'required',
        //     // 'dateOfBirth' => 'required',
        //     // 'country' => 'required',
        //     // 'timeZone' => 'required',
        //     // 'startDate' => 'required',
        //     // 'resignation' => 'required',
        //     'campusId' => 'required',
        //     // 'memo' => 'required',
        //     // 'offlineStudentId' => 'required',
        //     // 'offlineTeacherId' => 'required'
        // ]);
        // if ($validator->fails()) {
        //     return $this->errorBadRequest($validator->getMessageBag()->toArray());
        // }

        $campusManagerId = IdGenerator::generate(['table' => 'campus_managers', 'trow' => 'campusManagerId', 'length' => 8, 'prefix' => 'CPM']);
        // $email = Campus::where('campusId', $teacherParams['campusId'])->pluck('name')->map(function ($name) {
        //     return $name . '@gmail.com';
        // })->first();
        $userPassword = Str::random(8);
        $params = [
            'campusManagerId' => $campusManagerId,
            'name' => $teacherParams['name'],
            'email' => $teacherParams['email'],
            'gender' => $teacherParams['gender'],
            'dateOfBirth' => $teacherParams['dateOfBirth'],
            'country' => $teacherParams['country'],
            'timeZone' => $teacherParams['timeZone'],
            'startDate' => $teacherParams['startDate'],
            'resignation' => $teacherParams['resignation'],
            'campusId' => $teacherParams['campusId'],
            'memo' => $teacherParams['memo'],
        ];
        $userParams = [
            'campusId' => $teacherParams['campusId'],
            'name' => $params['name'],
            'email' => $params['email'],
            'password' => $userPassword,
        ];
        $newCampusManager = new CampusManagerResource(CampusManager::create($params));
        UserController::store($userParams);
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
        return $this->successCampusManagerRequest($CampusManagerData);
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
        if (empty($this->request['name'])) {
            $this->request['name'] = $campusManager['name'];
        }
        if (empty($this->request['email'])) {
            $this->request['email'] = $campusManager['email'];
        }
        if (empty($this->request['gender'])) {
            $this->request['gender'] = $campusManager['gender'];
        }
        if (empty($this->request['dateOfBirth'])) {
            $this->request['dateOfBirth'] = $campusManager['dateOfBirth'];
        }
        if (empty($this->request['country'])) {
            $this->request['country'] = $campusManager['country'];
        }
        if (empty($this->request['timeZone'])) {
            $this->request['timeZone'] = $campusManager['timeZone'];
        }
        if (empty($this->request['startDate'])) {
            $this->request['startDate'] = $campusManager['startDate'];
        }
        if (empty($this->request['resignation'])) {
            $this->request['resignation'] = $campusManager['resignation'];
        }
        if (empty($this->request['campusId'])) {
            $this->request['campusId'] = $campusManager['campusId'];
        }
        if (empty($this->request['memo'])) {
            $this->request['memo'] = $campusManager['memo'];
        }
        if (empty($this->request['offlineStudentId'])) {
            $this->request['offlineStudentId'] = $campusManager['offlineStudentId'];
        }
        if (empty($this->request['offlineTeacherId'])) {
            $this->request['offlineTeacherId'] = $campusManager['offlineTeacherId'];
        }
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
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
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
        return $this->successCampusManagerRequest($newInfoCampusManager);
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
        return $this->successCampusManagerRequest($deleteCampusManager);
    }
}
