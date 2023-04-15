<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Enrollment;
use App\Http\Resources\Enrollment as EnrollmentResource;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\Products;
use App\Http\Resources\Products as ProductsResource;
use Exception;

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
        $datas = EnrollmentResource::collection(Enrollment::all());
        return $this->successEnrollmentRequest($datas);
    }

    public function getEnrollmentWhereCheckIs0()
    {
        try{
            $enrollment = Enrollment::leftJoin('student_enrollments', 'enrollments.enrollmentId', '=', 'student_enrollments.enrollmentId')
            ->leftJoin('campuses', 'enrollments.campusId', '=', 'campuses.campusId')
            ->leftJoin('product_enrollments', 'enrollments.enrollmentId', '=', 'product_enrollments.enrollmentId')
            ->leftJoin('products', 'products.productId', '=', 'product_enrollments.productId')
            ->leftJoin('students', 'student_enrollments.studentId', '=', 'students.studentId')
            ->selectRaw(
                'enrollments.enrollmentId,
                GROUP_CONCAT(DISTINCT CONCAT_WS(":",student_enrollments.studentId, students.name)) as students,
                campuses.campusId,
                campuses.name as campusName,
                GROUP_CONCAT(DISTINCT CONCAT_WS(":",product_enrollments.productId, products.name)) as Products,
                MAX(products.level) as level,
                enrollments.submittedDate,
                enrollments.status',
            )
            ->where('student_enrollments.check', 0)
            ->groupBy('enrollments.enrollmentId')
            ->get();
        }catch(Exception $e){
            return $e->getMessage();
        }

        return $this->successEnrollmentRequest($enrollment);
    }

    public function enrollmentHistory()
    {
        try{
            $enrollment = Enrollment::join('student_enrollments', 'enrollments.enrollmentId', '=', 'student_enrollments.enrollmentId')
            ->join('campuses', 'enrollments.campusId', '=', 'campuses.campusId')
            ->join('product_enrollments', 'enrollments.enrollmentId', '=', 'product_enrollments.enrollmentId')
            ->join('products', 'products.productId', '=', 'product_enrollments.productId')
            ->join('students', 'student_enrollments.studentId', '=', 'students.studentId')
            ->selectRaw(
                'enrollments.enrollmentId,
                GROUP_CONCAT(DISTINCT CONCAT_WS(":",student_enrollments.studentId, students.name)) as students,
                campuses.campusId,
                campuses.name as campusName,
                GROUP_CONCAT(DISTINCT CONCAT_WS(":",product_enrollments.productId, products.name)) as Products,
                MAX(products.level) as level,
                DATE_FORMAT(enrollments.submittedDate,"%Y-%m-%d") as submittedDate,
                enrollments.status',
            )
            ->where('student_enrollments.check', 1)
            ->groupBy('enrollments.enrollmentId')
            ->get();
        }catch(Exception $e){
            return $e->getMessage();
        }
        return $this->successEnrollmentRequest($enrollment);
    }

    public function getEnrollmentHaveProductAndStudent()
    {
        try{
            $enrollment = Enrollment::leftJoin('student_enrollments', 'enrollments.enrollmentId', '=', 'student_enrollments.enrollmentId')
            ->leftJoin('campuses', 'enrollments.campusId', '=', 'campuses.campusId')
            ->leftJoin('product_enrollments', 'enrollments.enrollmentId', '=', 'product_enrollments.enrollmentId')
            ->leftJoin('products', 'products.productId', '=', 'product_enrollments.productId')
            ->leftJoin('students', 'student_enrollments.studentId', '=', 'students.studentId')
            ->selectRaw(
                'enrollments.enrollmentId,
                GROUP_CONCAT(DISTINCT CONCAT_WS(":",student_enrollments.studentId, students.name)) as students,
                campuses.campusId,
                campuses.name as campusName,
                GROUP_CONCAT(DISTINCT CONCAT_WS(":",product_enrollments.productId, products.name)) as Products,
                MAX(products.level) as level,
                enrollments.submittedDate,
                enrollments.status',
            )
            ->where('student_enrollments.check', 1)
            ->groupBy('enrollments.enrollmentId')
            ->get();
        } catch(Exception $e){
            return $e->getMessage();
        }
        
        return $this->successEnrollmentRequest($enrollment);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function Enrollmentshow($level)
    {
        $data = ProductsResource::collection(Products::where('level', $level)->get());
        return $this->successEnrollmentRequest($data);
    }

    public function showErollmentByPro($level, $product)
    {
        $data = ProductsResource::collection(Products::where('product', $product)->where('product', $product)->get());
        return $this->successEnrollmentRequest($data);
    }

    public function getEnrollment($campusId) 
    {
        $enrollment = Enrollment::where('campusId', $campusId)->get();
        return $this->successEnrollmentRequest($enrollment);
    }
    public function store()
    {
        $validator = validator::make($this->request->all(), [
            // 'enrollmentId' => 'required|unique:enrollments',
            // 'studentName' => 'required',
            // 'talkSamId' => 'required',
            'campusId' => 'required',
            // 'activate' => 'required',
            // 'level' => 'required',
            // 'subject' => 'required',
            // 'status' => 'required',
            // 'submittedDate' => 'date|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $enrollmentId = IdGenerator::generate(['table' => 'enrollments', 'trow' => 'enrollmentId', 'length' => 7, 'prefix' => 'ER']);
        $params = [
            'enrollmentId' => $enrollmentId,
            'talkSamId' => $this->request['talkSamId'],
            'campusId' => $this->request['campusId'],
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
        if (empty($this->request['talkSamId'])) {
            $this->request['talkSamId'] = $enrollment['talkSamId'];
        }
        if (empty($this->request['campusId'])) {
            $this->request['campusId'] = $enrollment['campusId'];
        }
        if (empty($this->request['level'])) {
            $this->request['level'] = $enrollment['level'];
        }
        if (empty($this->request['subject'])) {
            $this->request['subject'] = $enrollment['subject'];
        }
        if (empty($this->request['status'])) {
            $this->request['status'] = $enrollment['status'];
        }
        if (empty($this->request['submittedDate'])) {
            $this->request['submittedDate'] = $enrollment['submittedDate'];
        }
        $validator = validator::make($this->request->all(), [
            'campusId' => 'string',
            'status' => 'string',
            'submittedDate' => 'date',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $params = [
            $enrollment['talkSamId'] = $this->request['talkSamId'],
            $enrollment['campusId'] = $this->request['campusId'],
            $enrollment['level'] = $this->request['level'],
            $enrollment['subject'] = $this->request['subject'],
            $enrollment['status'] = $this->request['status'],
            $enrollment['submittedDate'] = $this->request['submittedDate'],
        ];
        $newInfoEnrollment = $enrollment->update($params);
        return $this->successEnrollmentRequest($newInfoEnrollment);
    }

    public function updateEnrollmentHistory($enrollmentId)
    {
        $enrollment = Enrollment::find($enrollmentId);
        if (empty($this->request['talkSamId'])) {
            $this->request['talkSamId'] = $enrollment['talkSamId'];
        }
        if (empty($this->request['campusId'])) {
            $this->request['campusId'] = $enrollment['campusId'];
        }
        if (empty($this->request['level'])) {
            $this->request['level'] = $enrollment['level'];
        }
        if (empty($this->request['subject'])) {
            $this->request['subject'] = $enrollment['subject'];
        }
        if (empty($this->request['status'])) {
            $this->request['status'] = $enrollment['status'];
        }
        if (empty($this->request['submittedDate'])) {
            $this->request['submittedDate'] = $enrollment['submittedDate'];
        }
        $validator = validator::make($this->request->all(), [
            'campusId' => 'string|required',
            'status' => 'string|required',
            'submittedDate' => 'date|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $params = [
            $enrollment['talkSamId'] = $this->request['talkSamId'],
            $enrollment['campusId'] = $this->request['campusId'],
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
    public function destroy($enrollmentId)
    {
        $enrollment = Enrollment::find($enrollmentId);
        $deleteEnrollment = $enrollment->delete();
        return $this->successEnrollmentRequest($deleteEnrollment);
    }
}
