<?php

namespace App\Http\Controllers\api\Campus_Dashboard;

use App\Events\EnrollmnetMessage;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Enrollment;
use App\Http\Resources\Products as ProductsResource;
use App\Http\Resources\Student as StudentResource;
use App\Http\Resources\Enrollment as EnrollmentResource;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllEnrollment()
    {
        $data = EnrollmentResource::collection(Enrollment::all());
        return $this->successEnrollmentRequest($data);
    }

    public function showEnrollment($level)
    {
        $data = ProductsResource::collection(Products::where('level', $level)->get());
        return $this->successEnrollmentRequest($data);
    }
    public function index($campusId, $productId)
    {
        $products = Students::join('student_products', 'students.studentId', '=', 'student_products.studentId')
            ->where('campusId', $campusId)->where('productId', $productId)->get();
        return $products;
        // return $this->successEnrollmentRequest($data);
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
            'campusId' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $enrollmentId = IdGenerator::generate(['table' => 'enrollments', 'trow' => 'enrollmentId', 'length' => 7, 'prefix' => 'ER']);
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
        event(new EnrollmnetMessage('New enrollment has been created'));
        return $newEnrollment;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($campusId)
    {
        $enrollments = Enrollment::where('campusId',$campusId)->get();
        return $this->successEnrollmentRequest($enrollments);
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
        $enrollments = Enrollment::find($enrollmentId);
        if (empty($request->talkSamId)) {
            $request['talkSamId'] = $enrollments['talkSamId'];
        }
        if (empty($request->campusId)) {
            $request['campusId'] = $enrollments['campusId'];
        }
        if (empty($request->level)) {
            $request['level'] = $enrollments['level'];
        }
        if (empty($request->subject)) {
            $request['subject'] = $enrollments['subject'];
        }
        if (empty($request->status)) {
            $request['status'] = $enrollments['status'];
        }
        if (empty($request->submittedDate)) {
            $request['submittedDate'] = $enrollments['submittedDate'];
        }


        $validator = validator::make($request->all(), [
            'campusId' => 'required|string',

        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
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
