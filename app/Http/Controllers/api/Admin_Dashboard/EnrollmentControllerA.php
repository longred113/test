<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Enrollment;
use App\Http\Resources\Enrollment as EnrollmentResource;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class EnrollmentControllerA extends Controller
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
        $data = EnrollmentResource::collection(Enrollment::all());
        return $this->successEnrollmentRequest($data);
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
            'studentId' => 'required|unique:enrollments',
            'studentName' => 'required',
            // 'talkSamId' => 'required',
            // 'campusName' => 'required',
            // 'activate' => 'required',
            // 'level' => 'required',
            // 'subject' => 'required',
            // 'status' => 'required',
            'submittedDate' => 'date|required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $enrollmentId = IdGenerator::generate(['table'=>'enrollments', 'trow' => 'enrollmentId', 'length' => 7, 'prefix' => 'ER']);
        $params = [
            'studentId' => $enrollmentId,
            'studentName' => $this->request['studentName'],
            'talkSamId' => $this->request['talkSamId'],
            'campusName' => $this->request['campusName'],
            'level' => $this->request['level'],
            'subject' => $this->request['subject'],
            'status' => $this->request['status'],
            'submitted' => $this->request['submitted'],
        ];
    
        $newEnrollment = new EnrollmentResource(Enrollment::create($params));
        return $this->successEnrollmentRequest($newEnrollment);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($enrollmentId)
    {
        $Enrollment = Enrollment::find($enrollmentId);
        $EnrollmentData = new EnrollmentResource($Enrollment);
        return $this->successEnrollmentRequest($EnrollmentData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($enrollmentId)
    {
        $enrollment = Enrollment::find($enrollmentId);
        if(empty($this->request['studentId'])) {
            $this->request['studentId'] = $enrollment['studentId'];
        }
        if(empty($this->request['studentName'])) {
            $this->request['studentName'] = $enrollment['studentName'];
        }
        if(empty($this->request['talkSamId'])) {
            $this->request['talkSamId'] = $enrollment['talkSamId'];
        }
        if(empty($this->request['campusName'])) {
            $this->request['campusName'] = $enrollment['campusName'];
        }
        if(empty($this->request['level'])) {
            $this->request['level'] = $enrollment['level'];
        }
        if(empty($this->request['subject'])) {
            $this->request['subject'] = $enrollment['subject'];
        }
        if(empty($this->request['status'])) {
            $this->request['status'] = $enrollment['status'];
        }
        if(empty($this->request['submittedDate'])) {
            $this->request['submittedDate'] = $enrollment['submittedDate'];
        }
        $validator = validator::make($this->request->all(), [
            'studentId' => 'required',
            'studentName' => 'required',
            'talkSamId' => 'required',
            'campusName' => 'required',
            'level' => 'required',
            'subject' => 'required',
            'status' => 'required',
            'submittedDate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $params = [
            $enrollment['studentId'] = $this->request['studentId'],
            $enrollment['studentName'] = $this->request['studentName'],
            $enrollment['talkSamId'] = $this->request['talkSamId'],
            $enrollment['campusName'] = $this->request['campusName'],
            $enrollment['level'] = $this->request['level'],
            $enrollment['subject'] = $this->request['subject'],
            $enrollment['status'] = $this->request['status'],
            $enrollment['submittedDate'] = $this->request['submittedDate'],
        ];
        $newInfoEnrollment = $enrollment->update($params);
        return $this->successEnrollmentRequest($newInfoEnrollment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}