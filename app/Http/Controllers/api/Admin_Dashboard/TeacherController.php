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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $campusIds = Campus::select('campusId')->get();
        foreach ($campusIds as $campusId) {
            $campusName = Campus::whereIn('campusId', $campusId)->get('name');
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
        ]);
        if ($validator->failed()) {
            return $validator->errors();
        }

        $teacherId = IdGenerator::generate(['table'=>'teachers', 'trow' => 'teacherId', 'length' => 8, 'prefix' => 'TC-']);
        $params = [
            'teacherId' => $teacherId,
            'name' => request('name'),
            'email' => request('email'),
            'gender' => request('gender'),
            'dateOfBirth' => request('dateOfBirth'),
            'status' => request('status'),
            'activate' => request('activate'),
            'country' => request('country'),
            'timeZone' => request('timeZone'),
            'startDate' => request('startDate'),
            'resignation' => request('resignation'),
            'resume' => request('resume'),
            'certificate' => request('certificate'),
            'contract' => request('contract'),
            'basicPoint' => request('basicPoint'),
            'campusId' => request('campusId'),
            'type' => request('type'),
            'talkSamId' => request('talkSamId'),
        ];
        $newTeacherData = new TeacherResource(Teachers::create($params));
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
        $teacherData = new TeacherResource($teacher);
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
        ]);
        if ($validator->failed()) {
            return $validator->errors();
        }

        $params = [
            $teacher['name'] = $request['name'],
            $teacher['email'] = $request['email'],
            $teacher['dateOfBirth'] = $request['dateOfBirth'],
            $teacher['status'] = $request['status'],
            $teacher['activate'] = $request['activate'],
            $teacher['country'] = $request['country'],
            $teacher['timeZone'] = $request['timeZone'],
            $teacher['startDate'] = $request['startDate'],
            $teacher['resignation'] = $request['resignation'],
            $teacher['resume'] = $request['resume'],
            $teacher['certificate'] = $request['certificate'],
            $teacher['contract'] = $request['contract'],
            $teacher['basicPoint'] = $request['basicPoint'],
            $teacher['type'] = $request['type'],
            $teacher['talkSamId'] = $request['talkSamId'],
            $teacher['campusId'] = $request['campusId'],
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