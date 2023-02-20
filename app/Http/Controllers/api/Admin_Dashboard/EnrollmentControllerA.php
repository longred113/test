<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Enrollment;
use App\Http\Resources\Enrollment as EnrollmentResource;

class EnrollmentControllerA extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = EnrollmentResource::collection(Enrollment::all());
        return $this->successStudentRequest($data);
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
            'studentId' => 'required|unique:enrollments',
            'studentName' => 'required',
            // 'talkSamId' => 'required',
            // 'campusName' => 'required',
            // 'activate' => 'required',
            // 'level' => 'required',
            // 'subject' => 'required',
            // 'status' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $params = [
            'studentId' => request('studentId'),
            'studentName' => request('studentName'),
            'talkSamId' => request('talkSamId'),
            'campusName' => request('campusName'),
            'activate' => request('activate'),
            'level' => request('level'),
            'subject' => request('subject'),
            'status' => request('status'),
        ];
        $newEnrollment = new EnrollmentResource(Enrollment::create($params));
        return $newEnrollment;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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