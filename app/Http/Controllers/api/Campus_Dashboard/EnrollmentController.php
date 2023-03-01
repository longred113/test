<?php

namespace App\Http\Controllers\api\Campus_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Enrollment;
use App\Http\Resources\Products as ProductsResource;
use App\Http\Resources\Student as StudentResource;
use App\Http\Resources\Enrollment as EnrollmentResource;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($campusId)
    {
        $data = Students::where('campusId', $campusId)->get();
        return $data;
        // return $this->successEnrollmentRequest($data);
    }

    public function showErollment($level)
    {
        $data = ProductsResource::collection(Products::where('level', $level)->get());
        return $this->successEnrollmentRequest($data);
    }
    public function showErollmentByPro($level, $productId)
    {
        $data = ProductsResource::collection(Products::where('level', $level)->where('productId', $productId)->get());
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
            'campusId' => 'required|unique:campusId',
          
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $enrollmentId = IdGenerator::generate(['table'=>'enrollments', 'trow' => 'enrollmentId', 'length' => 7, 'prefix' => 'ER']);
        $params = [
            'enrollmentId' => $enrollmentId,
            'talkSamId' => request('talkSamId'),
            'campusId' => request('campusId'),
            'level' => request('level'),
            'subject' => request('subject'),
            'status' => request('status'),
            'submittedDate' => request('submittedDate'),
         
            
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
    public function show($Enrollment)
    {
        $Enrollments = Enrollment::find($Enrollment);
        $EnrollmentsData = new Enrollment($Enrollments);
        return $this->successEnrollmentRequest($EnrollmentsData);
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
    public function update(Request $request, $enrollmentId)
    {
        $enrollments = Students::find($enrollmentId);
        if(empty($request->talkSamId)) {
            $request['talkSamId'] = $enrollments['talkSamId'];
        }
        if(empty($request->campusId)) {
            $request['campusId'] = $enrollments['campusId'];
        }
        if(empty($request->level)) {
            $request['level'] = $enrollments['level'];
        }
        if(empty($request->subject)) {
            $request['subject'] = $enrollments['subject'];
        }
        if(empty($request->status)) {
            $request['status'] = $enrollments['status'];
        }
        if(empty($request->submittedDate)) {
            $request['submittedDate'] = $enrollments['submittedDate'];
        }
       
        
        $validator = validator::make($request->all(), [
            'campusId' => 'required|string',
           
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        
        $params = [
            $enrollments['talkSamId'] = $request['talkSamId'],
            $enrollments['campusId'] = $request['campusId'],
            $enrollments['level'] = $request['level'],
            $enrollments['subject'] = $request['subject'],
            $enrollments['status'] = $request['status'],
            $enrollments['submittedDate'] = $request['submittedDate'],
            
        ];
        $newInfoEnrollment = $enrollments->update($params);
        return $this->successEnrollmentRequest($newInfoEnrollment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($enrollmentId)
    {
        $enrollment = Enrollment::find($enrollmentId);
        $deleteEnrollment = $enrollment->delete();
        return $this->successEnrollmentRequest($deleteEnrollment);
    }
}