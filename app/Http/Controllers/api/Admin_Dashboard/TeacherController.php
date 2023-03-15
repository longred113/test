<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampusResource;
use App\Http\Resources\TeacherResource;
use App\Models\Campus;
use App\Models\CampusManager;
use App\Models\Teachers;
use Error;
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
            'teachers.type')
        ->where('teachers.type', 'online')->get();
        return $this->successTeacherRequest($teachersData);
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
            'teachers.type')
        ->where('teachers.type', 'online')->get();
        return $this->successTeacherRequest($teacherData);
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
            $campusManager = CampusManagerController::store($params);
            return $this->successCampusManagerRequest($campusManager);
        }
        else {
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
    public function update(Request $request, $teacherId)
    {
        $teacher = Teachers::find($teacherId);
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
        $validator = validator::make($request->all(), [
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
        return $this->successTeacherRequest($newInfoTeacher);
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
