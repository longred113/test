<?php

namespace App\Http\Controllers\api\Admin_Dashboard;
use App\Http\Controllers\Controller;
use App\Http\Resources\CampusResource;
use App\Http\Resources\TeacherResource;
use App\Models\Campus;
use App\Models\Teachers;
use Error;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TypeError;

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
        $teachersData = TeacherResource::collection(Teachers::all());
        return $this->successTeacherRequest($teachersData);
    }

    public function showOnlineTeacher()
    {
        $teacherData = TeacherResource::collection(Teachers::where('type', 'online')->get());
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
            'password' => 'required|string|min:8',
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
            return $validator->errors();
        }

        $teacherId = IdGenerator::generate(['table'=>'teachers', 'trow' => 'teacherId', 'length' => 7, 'prefix' => 'TC']);
        $params = [
            'teacherId' => $teacherId,
            'name' => $this->request['name'],
            'email' => $this->request['email'],
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

        $userParams = [
            'teacherId' => $teacherId,
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'password' => $this->request['password'],
        ];
        $newTeacherData = new TeacherResource(Teachers::create($params));
        UserController::store($userParams);
        return $this->successTeacherRequest($newTeacherData);
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
        $joinData = Teachers::join('campuses','teachers.campusId', '=', 'campuses.campusId')->where('teacherId', $teacherId)->get();
        foreach($joinData as $join) {
            $data = $join['name'];
        }
        $teacherData = [];
        $teacherData = $teacher;
        $teacherData['campusName'] = $data; 
        return $teacherData;
        return $this->successTeacherRequest($teacherData);
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
            return $validator->errors();
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
            return $validator->errors();
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