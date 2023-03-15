<?php

namespace App\Http\Controllers\api\Campus_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Students;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Resources\Student as StudentsResource;
use App\Http\Resources\StudentResource;

class OffStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Students::select('studentId', 'name', 'joinedDate', 'withDrawal', 'campusId', 'type')->where('type', 'off')->get();
        return $this->successStudentRequest($data);
    }

    public function showStudentWithCampus($campusId)
    {
        $studentData = Students::where('campusId', $campusId)->where('type', 'off')->get();
        $getStData = StudentResource::collection($studentData);
        return $this->successStudentRequest($getStData);
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
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $studentId = IdGenerator::generate(['table' => 'students', 'trow' => 'studentId', 'length' => 7, 'prefix' => 'ST']);
        $params = [
            'studentId' => $studentId,
            'name' => request('name'),
            'enrollmentId' => request('enrollmentId'),
            'enrollmentCount' => 0,
            'email' => request('email'),
            'gender' => request('gender'),
            'dateOfBirth' => request('dateOfBirth'),
            'country' => request('country'),
            'timeZone' => request('timeZone'),
            'joinedDate' => request('joinedDate'),
            'withDrawal' => request('withDrawal'),
            'introduction' => request('introduction'),
            'talkSamId' => request('talkSamId'),
            'basicPoint' => request('basicPoint'),
            'campusId' => request('campusId'),
            'type' => request('type'),



        ];
        $newStudents = new StudentsResource(Students::create($params));
        return $newStudents;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($studentId)
    {
        $Students = Students::find($studentId);
        $StudentsData = new StudentsResource($Students);
        return $this->successStudentRequest($StudentsData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $studentId)
    {
        $students = Students::find($studentId);
        if (empty($request->name)) {
            $request['name'] = $students['name'];
        }
        if (empty($request->email)) {
            $request['email'] = $students['email'];
        }
        if (empty($request->enrollmentId)) {
            $request['enrollmentId'] = $students['enrollmentId'];
        }
        if (empty($request->gender)) {
            $request['gender'] = $students['gender'];
        }
        if (empty($request->dateOfBirth)) {
            $request['dateOfBirth'] = $students['dateOfBirth'];
        }
        if (empty($request->country)) {
            $request['country'] = $students['country'];
        }
        if (empty($request->timeZone)) {
            $request['timeZone'] = $students['timeZone'];
        }
        if (empty($request->joinedDate)) {
            $request['joinedDate'] = $students['joinedDate'];
        }
        if (empty($request->withDrawal)) {
            $request['withDrawal'] = $students['withDrawal'];
        }
        if (empty($request->introduction)) {
            $request['introduction'] = $students['introduction'];
        }
        if (empty($request->talkSamId)) {
            $request['talkSamId'] = $students['talkSamId'];
        }
        if (empty($request->basicPoint)) {
            $request['basicPoint'] = $students['basicPoint'];
        }
        if (empty($request->campusId)) {
            $request['campusId'] = $students['campusId'];
        }
        if (empty($request->type)) {
            $request['type'] = $students['type'];
        }

        $validator = validator::make($request->all(), [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $students['studentId'] = $request['studentId'],
            $students['name'] = $request['name'],
            $students['email'] = $request['email'],
            $students['enrollmentId'] = $request['enrollmentId'],
            $students['gender'] = $request['gender'],
            $students['dateOfBirth'] = $request['dateOfBirth'],
            $students['country'] = $request['country'],
            $students['timeZone'] = $request['timeZone'],
            $students['joinedDate'] = $request['joinedDate'],
            $students['withDrawal'] = $request['withDrawal'],
            $students['introduction'] = $request['introduction'],
            $students['talkSamId'] = $request['talkSamId'],
            $students['basicPoint'] = $request['basicPoint'],
            $students['campusId'] = $request['campusId'],
            $students['type'] = $request['type'],
        ];
        $newInfoStudents = $students->update($params);
        return $this->successStudentRequest($newInfoStudents);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($studentId)
    {
        $student = Students::find($studentId);
        $deleteStudents = $student->delete();
        return $this->successStudentRequest($deleteStudents);
    }
}
