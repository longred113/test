<?php

namespace App\Http\Controllers\api\Campus_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CampusManager;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Enrollment;
use App\Models\Students;
use App\Http\Resources\Products as ProductsResource;
use App\Http\Resources\Enrollment as EnrollmentResource;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($studentId)
    {
        $data = Student::collection(Students::where('status', 'Approved')->orWhere('status', 'Applied')->get());
        return $this->successStudentRequest($data);

        // $enrollment = Enrollment::join('enrollments', 'students.studentId', "=", 'enrollments.studentId')
        // ->where('studentId',$studentId)->get();
        // return($enrollment);
    }

    public function showErollment($level)
    {
        $data = ProductsResource::collection(Products::where('level', $level)->get());
        return $this->successEnrollmentRequest($data);
    }
    public function showErollmentByPro($level, $product)
    {
        $data = ProductsResource::collection(Products::where('level', $level)->where('product', $product)->get());
        return $this->successEnrollmentRequest($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'studentId' => 'required|unique:students',
            'name' => 'required',
            // 'email' => 'required',
            // 'gender' => 'required',
            // 'dateOfBirth' => 'required',
            // 'country' => 'required',
            // 'timeZone' => 'required',
            // 'joinedDate' => 'required',
            // 'withDrawal' => 'required',
            // 'introduction' => 'required',
            // 'talkSamId' => 'required',
            // 'basicPoint' => 'required',
            // 'campusId' => 'required',
            // 'type' => 'required',
            // 'status' => 'required',           
            
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
                $enrollmentId = IdGenerator::generate(['table'=>'enrollments', 'trow' => 'enrollmentId', 'length' => 7, 'prefix' => 'ER']);
        $params = [
            'studentId' => request('studentId'),
            'name' => request('name'),
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
            'status' => request('status'),
            'status' => request('status'),
         
            
        ];
        $newEnrollment = new Student(Students::create($params));
        return $newEnrollment;
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
        $StudentsData = new Student($Students);
        return $this->successStudentRequest($StudentsData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        if(empty($request->name)) {
            $request['name'] = $students['name'];
        }
        if(empty($request->email)) {
            $request['email'] = $students['email'];
        }
        if(empty($request->gender)) {
            $request['gender'] = $students['gender'];
        }
        if(empty($request->dateOfBirth)) {
            $request['dateOfBirth'] = $students['dateOfBirth'];
        }
        if(empty($request->country)) {
            $request['country'] = $students['country'];
        }
        if(empty($request->timeZone)) {
            $request['timeZone'] = $students['timeZone'];
        }
        if (empty($request->joinedDate)) {
            $request['joinedDate'] = $students['joinedDate'];
        }
        if(empty($request->withDrawal)) {
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
        if(empty($request->campusId)) {
            $request['campusId'] = $students['campusId'];
        }
        if(empty($request->type)) {
            $request['type'] = $students['type'];
        }
        if(empty($request->classId)) {
            $request['classId'] = $students['classId'];
        }
        
        $validator = validator::make($request->all(), [
            'name' => 'required|string',
            // 'email' => 'required',
            // 'gender' => 'required',
            // 'dateOfBirth' => 'required',
            // 'country' => 'required',
            // 'timeZone' => 'required',
            // 'joinedDate' => 'required',
            // 'withDrawal' => 'required',
            // 'introduction' => 'required',
            // 'talkSamId' => 'required',
            // 'basicPoint' => 'required',
            // 'campusId' => 'required',
            // 'type' => 'required',
            // 'status' => 'required',  
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        
        $params = [
            $students['studentId'] = $request['studentId'],
            $students['name'] = $request['name'],
            $students['email'] = $request['email'],
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
            $students['status'] = $request['status'],
            
            
            
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