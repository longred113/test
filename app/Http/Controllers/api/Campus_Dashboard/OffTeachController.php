<?php

namespace App\Http\Controllers\api\Campus_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Teachers;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Resources\TeacherResource;
use Exception;

class OffTeachController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Teachers::join('campuses', 'teachers.campusId', '=', 'campuses.campusId')
            ->select(
                'teachers.teacherId',
                'teachers.campusId',
                'campuses.name as campusName',
                'teachers.name as teacherName',
                'teachers.email as email',
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
            ->where('type', 'off')->get();
        return $this->successTeacherRequest($data);
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
            // 'teacherId' => 'required|unique:teachers',
            'name' => 'required',
            // 'email' => 'required',
            // 'gender' => 'required',
            // 'dateOfBirth' => 'required',
            // 'status' => 'required',
            // 'activate' => 'required',
            // 'country' => 'required',
            // 'timeZone' => 'required',
            // 'startDate' => 'required',
            // 'resignation' => 'required',
            // 'resume' => 'required',
            // 'certificate' => 'required',
            // 'contract' => 'required',
            // 'basicPoint' => 'required',
            // 'campusId' => 'required',
            // 'type' => 'required',
            // 'talkSamId' => 'required',
            // 'role' => 'required',
            // 'memo' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $teacherId = IdGenerator::generate(['table' => 'teachers', 'trow' => 'teacherId', 'length' => 7, 'prefix' => 'TC']);
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
            'role' => request('role'),
            'memo' => request('memo'),
        ];
        $newTeachers = new TeacherResource(Teachers::create($params));
        return $newTeachers;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($teacherId)
    {
        $Teachers = Teachers::find($teacherId);
        $TeachersData = new TeacherResource($Teachers);
        return $this->successTeacherRequest($TeachersData);
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
        $teachers = Teachers::find($teacherId);
        if (empty($request->name)) {
            $request['name'] = $teachers['name'];
        }
        if (empty($request->email)) {
            $request['email'] = $teachers['email'];
        }
        if (empty($request->gender)) {
            $request['gender'] = $teachers['gender'];
        }
        if (empty($request->dateOfBirth)) {
            $request['dateOfBirth'] = $teachers['dateOfBirth'];
        }
        if (empty($request->status)) {
            $request['status'] = $teachers['status'];
        }
        if (empty($request->activate)) {
            $request['activate'] = $teachers['activate'];
        }
        if (empty($request->country)) {
            $request['country'] = $teachers['country'];
        }
        if (empty($request->timeZone)) {
            $request['timeZone'] = $teachers['timeZone'];
        }
        if (empty($request->startDate)) {
            $request['startDate'] = $teachers['startDate'];
        }
        if (empty($request->resignation)) {
            $request['resignation'] = $teachers['resignation'];
        }
        if (empty($request->resume)) {
            $request['resume'] = $teachers['resume'];
        }
        if (empty($request->certificate)) {
            $request['certificate'] = $teachers['certificate'];
        }
        if (empty($request->contract)) {
            $request['contract'] = $teachers['contract'];
        }
        if (empty($request->basicPoint)) {
            $request['basicPoint'] = $teachers['basicPoint'];
        }
        if (empty($request->campusId)) {
            $request['campusId'] = $teachers['campusId'];
        }
        if (empty($request->type)) {
            $request['type'] = $teachers['type'];
        }
        if (empty($request->talkSamId)) {
            $request['talkSamId'] = $teachers['talkSamId'];
        }
        if (empty($request->role)) {
            $request['role'] = $teachers['role'];
        }
        if (empty($request->memo)) {
            $request['memo'] = $teachers['memo'];
        }
        $validator = validator::make($request->all(), [
            'name' => 'required|string',
            // 'email' => 'required|string',
            // 'gender' => 'required',
            // 'dateOfBirth' => 'required',
            // 'status' => 'required',
            // 'activate' => 'required',
            // 'country' => 'required',
            // 'timeZone' => 'required',
            // 'startDate' => 'required',
            // 'resignation' => 'required',
            // 'resume' => 'required',
            // 'certificate' => 'required',
            // 'contract' => 'required',
            // 'basicPoint' => 'required',
            // 'campusId' => 'required',
            // 'type' => 'required',
            // 'talkSamId' => 'required',
            // 'role' => 'required',
            // 'memo' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $teachers['teacherId'] = $request['teacherId'],
            $teachers['name'] = $request['name'],
            $teachers['email'] = $request['email'],
            $teachers['gender'] = $request['gender'],
            $teachers['dateOfBirth'] = $request['dateOfBirth'],
            $teachers['status'] = $request['status'],
            $teachers['activate'] = $request['activate'],
            $teachers['country'] = $request['country'],
            $teachers['timeZone'] = $request['timeZone'],
            $teachers['startDate'] = $request['startDate'],
            $teachers['resignation'] = $request['resignation'],
            $teachers['resume'] = $request['resume'],
            $teachers['certificate'] = $request['certificate'],
            $teachers['contract'] = $request['contract'],
            $teachers['basicPoint'] = $request['basicPoint'],
            $teachers['campusId'] = $request['campusId'],
            $teachers['type'] = $request['type'],
            $teachers['talkSamId'] = $request['talkSamId'],
            $teachers['role'] = $request['role'],
            $teachers['memo'] = $request['memo'],
        ];
        $newInfoTeachers = $teachers->update($params);
        return $this->successTeacherRequest($newInfoTeachers);
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
        $deleteTeachers = $teacher->delete();
        return $this->successTeacherRequest($deleteTeachers);
    }
}
