<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
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
        $studentsData = StudentResource::collection(Students::all());
        return $this->successStudentRequest($studentsData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = Validator::make($this->request->all(), [
            'studentId' => 'string|required|unique:students',
            'name' => 'string|required',
            'email' => 'string|required|unique:students',
            // 'gender' => 'string|required',
            // 'dateOfBirth' => 'date|required',
            // 'country' => 'string|required',
            // 'timeZone' => 'string|required',
            // 'status' => 'string|required',
            // 'joinedDate' => 'date|required',
            // 'withDrawal' => 'date|required',
            // 'introduction' => 'string|required',
            // 'talkSamId' => 'string|required',
            // 'basicPoint' => 'integer|required',
            'campusId' => 'string|required',
            'type' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            'studentId' => $this->request['studentId'],
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'gender' => $this->request['gender'],
            'dateOfBirth' => $this->request['dateOfBirth'],
            'country' => $this->request['country'],
            'timeZone' => $this->request['timeZone'],
            'status' => $this->request['status'],
            'joinedDate' => $this->request['joinedDate'],
            'withDrawal' => $this->request['withDrawal'],
            'introduction' => $this->request['introduction'],
            'talkSamId' => $this->request['talkSamId'],
            'basicPoint' => $this->request['basicPoint'],
            'campusId' => $this->request['campusId'],
            'type' => $this->request['type'],
        ];
        $newStudentData = new StudentResource(Students::create($params));
        return $this->successStudentRequest($newStudentData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function show($studentId)
    {
        $student = Students::find($studentId);
        $studentData = new StudentResource($student);
        return $this->successStudentRequest($studentData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function update($studentId)
    {
        $student = Students::find($studentId);
        $validator = Validator::make($this->request->all(), [
            'name' => 'string|required',
            'email' => 'string|required',
            // 'gender' => 'string|required',
            // 'dateOfBirth' => 'date|required',
            // 'country' => 'string|required',
            // 'timeZone' => 'string|required',
            // 'status' => 'string|required',
            // 'joinedDate' => 'date|required',
            // 'withDrawal' => 'date|required',
            // 'introduction' => 'string|required',
            // 'talkSamId' => 'string|required',
            // 'basicPoint' => 'integer|required',
            'campusId' => 'string|required',
            'type' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            $student['name'] = $this->request['name'],
            $student['email'] = $this->request['email'],
            $student['gender'] = $this->request['gender'],
            $student['dateOfBirth'] = $this->request['dateOfBirth'],
            $student['country'] = $this->request['country'],
            $student['timeZone'] = $this->request['timeZone'],
            $student['status'] = $this->request['status'],
            $student['joinedDate'] = $this->request['joinedDate'],
            $student['withDrawal'] = $this->request['withDrawal'],
            $student['introduction'] = $this->request['introduction'],
            $student['talkSamId'] = $this->request['talkSamId'],
            $student['basicPoint'] = $this->request['basicPoint'],
            $student['campusId'] = $this->request['campusId'],
            $student['type'] = $this->request['type'],
        ];

        $newStudentInfoData = $student->update($params);
        return $this->successStudentRequest($newStudentInfoData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function destroy($studentId)
    {
        $student = Students::find($studentId);
        $deleteStudent = $student->delete();
        return $this->successStudentRequest($deleteStudent);
    }
}
