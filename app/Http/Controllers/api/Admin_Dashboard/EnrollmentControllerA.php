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

        $enrollmentId = IdGenerator::generate(['table'=>'enrollments', 'trow' => 'enrollmentId', 'length' => 8, 'prefix' => 'ER-']);
        $params = [
            'studentId' => $enrollmentId,
            'studentName' => $this->request['studentName'],
            'talkSamId' => $this->request['talkSamId'],
            'campusName' => $this->request['campusName'],
            'activate' => $this->request['activate'],
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