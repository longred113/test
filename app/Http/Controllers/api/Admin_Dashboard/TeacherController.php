<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampusResource;
use App\Http\Resources\TeacherResource;
use App\Models\Campus;
use App\Models\CampusManager;
use App\Models\Teachers;
use App\Models\Users;
use Error;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use TypeError;

use function App\Helpers\trans;
use function PHPUnit\Framework\returnSelf;

class TeacherController extends Controller
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
        $teachersData = Teachers::join('users', 'teachers.teacherId', '=', 'users.teacherId')
            ->join('campuses', 'teachers.campusId', '=', 'campuses.campusId')
            ->select(
                'teachers.teacherId',
                'teachers.name as name',
                'teachers.email as email',
                'users.password as password',
                'teachers.campusId',
                'campuses.name as campusName',
                'teachers.role',
                'teachers.activate',
                'teachers.type'
            )
            ->where('teachers.type', 'online')->get();
        return $this->successTeacherRequest($teachersData);
    }

    public function getTeacherDataBeforeUpdate()
    {
        $teachersData = Teachers::join('users', 'teachers.teacherId', '=', 'users.teacherId')
            ->join('campuses', 'teachers.campusId', '=', 'campuses.campusId')
            ->select(
                'teachers.teacherId',
                'teachers.campusId',
                'campuses.name as campusName',
                'teachers.name as teacherName',
                'teachers.email as email',
                'users.password as password',
                'teachers.role',
                'teachers.activate',
                'teachers.type',
                'teachers.gender',
                'teachers.dateOfBirth',
                'teachers.status',
                'teachers.country',
                'teachers.timeZone',
                'teachers.talkSamId',
                'teachers.memo',
            )
            ->where('teachers.type', 'online')->get();
            $campusManagerData = CampusManager::leftJoin('users', 'campus_managers.campusManagerId', '=', 'users.campusManagerId')
            ->leftJoin('campuses', 'campus_managers.campusId', '=', 'campuses.campusId')
            ->select(
                'campus_managers.campusManagerId as teacherId',
                'campus_managers.name as teacherName',
                'campus_managers.email',
                'users.password',
                'campus_managers.campusId',
                'campuses.name as campusName',
                'campus_managers.role',
                'campus_managers.activate',
                'campus_managers.gender',
                'campus_managers.dateOfBirth',
                'campus_managers.country',
                'campus_managers.timeZone',
                'campus_managers.talkSamId',
            )
            ->get();
        $mergedData = $teachersData->concat($campusManagerData);
        return $this->successTeacherRequest($mergedData);
    }

    public function showOnlineTeacher()
    {
        $teacherData = Teachers::join('users', 'teachers.teacherId', '=', 'users.teacherId')
            ->join('campuses', 'teachers.campusId', '=', 'campuses.campusId')
            ->select(
                'teachers.teacherId',
                'teachers.name as teacherName',
                'teachers.email as userName',
                'users.password as password',
                'teachers.campusId',
                'campuses.name as campusName',
                'teachers.role',
                'teachers.activate',
                'teachers.type'
            )
            ->where('teachers.type', 'online')->get();
        $campusManagerData = CampusManager::join('users', 'campus_managers.campusManagerId', '=', 'users.campusManagerId')
            ->join('campuses', 'campus_managers.campusId', '=', 'campuses.campusId')
            ->select(
                'campus_managers.campusManagerId as teacherId',
                'campus_managers.name as teacherName',
                'campus_managers.email as userName',
                'users.password',
                'campus_managers.campusId',
                'campuses.name as campusName',
                'campus_managers.role',
                'campus_managers.activate',
                // 'campus_managers.type'
            )
            ->get();
        $mergedData = $teacherData->concat($campusManagerData);
        return $this->successTeacherRequest($mergedData);
    }

    public function getAllOnlineTeacher()
    {
        $teachersData = Teachers::where('type', 'online')->select('teacherId', 'name as teacherName')->get();
        return $this->successTeacherRequest($teachersData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $campusIds = Campus::select('campusId')->get();
        foreach ($campusIds as $campusId) {
            $campusName = Campus::whereIn('campusId', $campusId)->get('name');
        }
        $validator = validator::make($this->request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:teachers',
            'password' => 'required|string',
            // 'gender' => 'required|string',
            // 'dateOfBirth' => 'required|date',
            // 'status' => 'required|string',
            // 'activate' => 'required',
            // 'country' => 'required|string',
            // 'timeZone' => 'required|string',
            // 'startDate' => 'required|string',
            // 'resignation' => 'required',
            // 'resume' => 'required|string',
            // 'certificate' => 'required|string',
            // 'contract' => 'required|string',
            // 'basicPoint' => 'required|integer',
            // 'type' => 'required|string',
            // 'talkSamId' => 'require|string',
            'campusId' => 'required|string',
            // 'role' => 'string',
            // 'memo' => 'string',
        ]);
        if ($validator->failed()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $teacherId = IdGenerator::generate(['table' => 'teachers', 'trow' => 'teacherId', 'length' => 7, 'prefix' => 'TC']);
        $params = [
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'password' => $this->request['password'],
            'gender' => $this->request['gender'],
            'dateOfBirth' => $this->request['dateOfBirth'],
            'status' => $this->request['status'],
            'activate' => $this->request['activate'],
            'country' => $this->request['country'],
            'timeZone' => $this->request['timeZone'],
            'startDate' => $this->request['startDate'],
            'resignation' => $this->request['resignation'],
            'resume' => $this->request['resume'],
            'certificate' => $this->request['certificate'],
            'contract' => $this->request['contract'],
            'basicPoint' => $this->request['basicPoint'],
            'campusId' => $this->request['campusId'],
            'type' => $this->request['type'],
            'talkSamId' => $this->request['talkSamId'],
            'role' => $this->request['role'],
            'memo' => $this->request['memo'],
        ];
        if ($this->request['role'] == 'Campus Manager') {
            $existCampusId = CampusManager::where('campusId', $this->request['campusId'])->get();
            if ($existCampusId->isNotEmpty()) {
                $errorMessage = Lang::get('teacher.existed_campus_manager', [], 'vi');
                return response()->json(['error' => $errorMessage], 400);
            }
            $newCampusManager = CampusManagerController::store($params);
            return $this->successCampusManagerRequest($newCampusManager);
        } else {
            $params['teacherId'] = $teacherId;
            $newTeacherData = new TeacherResource(Teachers::create($params));
            $userParams = [
                'teacherId' => $teacherId,
                'name' => $this->request['name'],
                'email' => $this->request['email'],
                'password' => $this->request['password'],
            ];
            UserController::store($userParams);
            return $this->successTeacherRequest($newTeacherData);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($teacherId)
    {
        $teacher = Teachers::find($teacherId);
        $joinData = Teachers::join('campuses', 'teachers.campusId', '=', 'campuses.campusId')->where('teacherId', $teacherId)->get();
        foreach ($joinData as $join) {
            $data = $join['name'];
        }
        $showTeacherData = [];
        $showTeacherData = $teacher;
        $showTeacherData['campusName'] = $data;
        return $this->successTeacherRequest($showTeacherData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($teacherId)
    {
        $teacher = Teachers::where('teacherId', $teacherId)->first();
        if (!empty($teacher)) {
            if (empty($this->request['name'])) {
                $this->request['name'] = $teacher['name'];
            }
            if (empty($this->request['email'])) {
                $this->request['email'] = $teacher['email'];
            }
            if (empty($this->request['gender'])) {
                $this->request['gender'] = $teacher['gender'];
            }
            if (empty($this->request['dateOfBirth'])) {
                $this->request['dateOfBirth'] = $teacher['dateOfBirth'];
            }
            if (empty($this->request['status'])) {
                $this->request['status'] = $teacher['status'];
            }
            if (empty($this->request['activate'])) {
                $this->request['activate'] = $teacher['activate'];
            }
            if (empty($this->request['country'])) {
                $this->request['country'] = $teacher['country'];
            }
            if (empty($this->request['timeZone'])) {
                $this->request['timeZone'] = $teacher['timeZone'];
            }
            if (empty($this->request['startDate'])) {
                $this->request['startDate'] = $teacher['startDate'];
            }
            if (empty($this->request['resignation'])) {
                $this->request['resignation'] = $teacher['resignation'];
            }
            if (empty($this->request['resume'])) {
                $this->request['resume'] = $teacher['resume'];
            }
            if (empty($this->request['certificate'])) {
                $this->request['certificate'] = $teacher['certificate'];
            }
            if (empty($this->request['contract'])) {
                $this->request['contract'] = $teacher['contract'];
            }
            if (empty($this->request['basicPoint'])) {
                $this->request['basicPoint'] = $teacher['basicPoint'];
            }
            if (empty($this->request['type'])) {
                $this->request['type'] = $teacher['type'];
            }
            if (empty($this->request['talkSamId'])) {
                $this->request['talkSamId'] = $teacher['talkSamId'];
            }
            if (empty($this->request['campusId'])) {
                $this->request['campusId'] = $teacher['campusId'];
            }
            if (empty($this->request['role'])) {
                $this->request['role'] = $teacher['role'];
            }
            if (empty($this->request['memo'])) {
                $this->request['memo'] = $teacher['memo'];
            }
            $validator = validator::make($this->request->all(), [
                'name' => 'required|string',
                // 'email' => 'required|string|unique:teachers',
                // 'gender' => 'required|string',
                // 'dateOfBirth' => 'required|date',
                // 'status' => 'required|string',
                // 'activate' => 'required',
                // 'country' => 'required|string',
                // 'timeZone' => 'required|string',
                // 'startDate' => 'required|string',
                // 'resignation' => 'required',
                // 'resume' => 'required|string',
                // 'certificate' => 'required|string',
                // 'contract' => 'required|string',
                // 'basicPoint' => 'required|integer',
                // 'type' => 'required|string',
                // 'talkSamId' => 'require|string',
                'campusId' => 'required|string',
                'role' => 'string',
                'memo' => 'string',
            ]);
            if ($validator->failed()) {
                return $this->errorBadRequest($validator->getMessageBag()->toArray());
            }

            $params = [
                $teacher['name'] = $this->request['name'],
                $teacher['email'] = $this->request['email'],
                $teacher['gender'] = $this->request['gender'],
                $teacher['dateOfBirth'] = $this->request['dateOfBirth'],
                $teacher['status'] = $this->request['status'],
                $teacher['activate'] = $this->request['activate'],
                $teacher['country'] = $this->request['country'],
                $teacher['timeZone'] = $this->request['timeZone'],
                $teacher['startDate'] = $this->request['startDate'],
                $teacher['resignation'] = $this->request['resignation'],
                $teacher['resume'] = $this->request['resume'],
                $teacher['certificate'] = $this->request['certificate'],
                $teacher['contract'] = $this->request['contract'],
                $teacher['basicPoint'] = $this->request['basicPoint'],
                $teacher['type'] = $this->request['type'],
                $teacher['talkSamId'] = $this->request['talkSamId'],
                $teacher['campusId'] = $this->request['campusId'],
                $teacher['role'] = $this->request['role'],
                $teacher['memo'] = $this->request['memo'],
            ];
            $newInfoTeacher = $teacher->update($params);
            $user = Users::where('teacherId', $teacherId)->first();
            if(empty($this->request['userName'])){
                $this->request['userName'] = $user['userName'];
            }
            if(empty($this->request['password'])){
                $this->request['password'] = $user['password'];
            }
            $userParams = [
                'teacherId' => $teacherId,
                'name' => $this->request['name'],
                'email' => $this->request['email'],
                'password' => $this->request['password'],
            ];
            try {
                if (!empty($user)) {
                    UserController::update($userParams);
                }
            } catch (Exception $e) {
                return $e->getMessage();
            }
            return $this->successTeacherRequest($newInfoTeacher);
        }
        $campusManager = CampusManager::where('campusManagerId', $teacherId)->first();
        if (!empty($campusManager)) {
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
                'activate' => 'integer',
            ]);
            if ($validator->fails()) {
                return $this->errorBadRequest($validator->getMessageBag()->toArray());
            }
            $campusManagerParams = [
                $campusManager['name'] = $this->request['name'],
                $campusManager['email'] = $this->request['email'],
                $campusManager['gender'] = $this->request['gender'],
                $campusManager['dateOfBirth'] = $this->request['dateOfBirth'],
                $campusManager['talkSamId'] = $this->request['talkSamId'],
                $campusManager['country'] = $this->request['country'],
                $campusManager['timeZone'] = $this->request['timeZone'],
                $campusManager['startDate'] = $this->request['startDate'],
                $campusManager['resignation'] = $this->request['resignation'],
                $campusManager['campusId'] = $this->request['campusId'],
                $campusManager['memo'] = $this->request['memo'],
                $campusManager['offlineStudentId'] = $this->request['offlineStudentId'],
                $campusManager['offlineTeacherId'] = $this->request['offlineTeacherId'],
                $campusManager['role'] = 'campusManger',
                $campusManager['activate'] = $this->request['activate'],
            ];
            try{
                $newInfoCampusManager = $campusManager->update($campusManagerParams);
                $user = Users::where('campusManagerId', $teacherId)->first();
                if(empty($this->request['userName'])){
                    $this->request['userName'] = $user['userName'];
                }
                if(empty($this->request['password'])){
                    $this->request['password'] = $user['password'];
                }
                $userParams = [
                    'campusManagerId' => $teacherId,
                    'name' => $this->request['name'],
                    'email' => $this->request['email'],
                    'password' => $this->request['password'],
                ];
                if(!empty($user)){
                    UserController::update($userParams);
                }
            }catch(Exception $e){
                return $e->getMessage();
            }
            return $this->successCampusManagerRequest($newInfoCampusManager);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($teacherId)
    {
        $teacher = Teachers::find($teacherId);
        $deleteTeacher = $teacher->delete();
        return $this->successTeacherRequest($deleteTeacher);
    }

    public function multiDeleteTeacher()
    {
        $validator = validator::make($this->request->all(), [
            'teacherId' => 'string|required_without:teacherIds',
            'teacherIds' => 'array|required_without:teacherId'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        if (!empty($this->request->get('teacherId'))) {
            $ids[] = $this->request->get('teacherId');
        } else {
            $ids = $this->request->get('teacherIds');
        }
        dd($ids);
        foreach ($ids as $id) {
            $deleteTeacher = Teachers::where('teacherId', $id)->delete();
        }
        return $this->successTeacherRequest($deleteTeacher);
    }
}
