<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Roles;
use App\Models\Students;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Validation\Rule;

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

    public function studentWithdrawalList() 
    {
        $studentWithdrawal = StudentResource::collection(Students::where('type', 'online-break')->orWhere('type', 'offline-break')->get());
        return $this->successStudentRequest($studentWithdrawal);
    }

    public function studentJoinedList()
    {
        $studentId = Users::get('studentId');
        $joinedStudent = Students::whereIn('studentId', $studentId)->where('type', 'online')->get();
        return $this->successStudentRequest($joinedStudent);
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
            'name' => 'string|required',
            'email' => 'string|required|unique:students',
            'password' => 'string|required|min:8',
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
            'type' => [Rule::in(['online', 'offline', 'online-break', 'offline-break', 'reserve', 'wait-for-approval'])],
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $studentId = IdGenerator::generate(['table'=>'students', 'trow' => 'studentId', 'length' => 7, 'prefix' => 'ST']);
        $studentParams = [
            'studentId' => $studentId,
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

        $userParams = [
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'password' => $this->request['password'],
            'studentId' => $studentId,
        ];
        $newStudentData = new StudentResource(Students::create($studentParams));
        UserController::store($userParams);
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
        if (empty($this->request['name'])) {
            $this->request['name'] = $student['name'];
        }
        if (empty($this->request['email'])) {
            $this->request['email'] = $student['email'];
        }
        if (empty($this->request['gender'])) {
            $this->request['gender'] = $student['gender'];
        }
        if (empty($this->request['dateOfBirth'])) {
            $this->request['dateOfBirth'] = $student['dateOfBirth'];
        }
        if (empty($this->request['country'])) {
            $this->request['country'] = $student['country'];
        }
        if (empty($this->request['timeZone'])) {
            $this->request['timeZone'] = $student['timeZone'];
        }
        if (empty($this->request['status'])) {
            $this->request['status'] = $student['status'];
        }
        if (empty($this->request['joinedDate'])) {
            $this->request['joinedDate'] = $student['joinedDate'];
        }
        if (empty($this->request['withDrawal'])) {
            $this->request['withDrawal'] = $student['withDrawal'];
        }
        if (empty($this->request['introduction'])) {
            $this->request['introduction'] = $student['introduction'];
        }
        if (empty($this->request['talkSamId'])) {
            $this->request['talkSamId'] = $student['talkSamId'];
        }
        if (empty($this->request['basicPoint'])) {
            $this->request['basicPoint'] = $student['basicPoint'];
        }
        if (empty($this->request['campusId'])) {
            $this->request['campusId'] = $student['campusId'];
        }
        if (empty($this->request['type'])) {
            $this->request['type'] = $student['type'];
        }
        $validator = Validator::make($this->request->all(), [
            'name' => 'string',
            'email' => 'string',
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
            'type' => 'string',
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
