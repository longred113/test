<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ClassReports;
use App\Http\Resources\ClassReportsResource;
use Carbon\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class ClassReportController extends Controller
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
        $classReportData = ClassReports::join('teachers', 'class_reports.teacherId', '=', 'teachers.teacherId')
        ->join('classes', 'class_reports.classId', '=', 'classes.classId')
        ->join('students', 'class_reports.studentId', '=', 'students.studentId')
        ->join('campuses', 'class_reports.campusId', '=', 'campuses.campusId')
        ->select(
            'class_reports.classReportId',
            'teachers.teacherId', 
            'teachers.name as teacherName', 
            'classes.classId', 
            'classes.name as className', 
            'students.studentId', 
            'students.name as studentName', 
            'campuses.campusId',
            'campuses.name as campusName', 
            'class_reports.date',
            'class_reports.comment',
            'class_reports.attendance',
            'class_reports.preparation',
            'class_reports.attitude',
            'class_reports.participation',
            )
        ->get();
        return $this->successClassReport($classReportData);
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
            'teacherId' => 'string|required',
            // 'classId' => 'string|required',
            // 'studentId' => 'string|required',
            // 'campusId' => 'string|required',
            // 'status' => 'integer|required',
            // 'date' => 'string|required',
            // 'preparation' => 'string|required',
            // 'attitude' => 'string|required',
            // 'participation' => 'string|required',
            // 'comment' => 'string|required',
            'attendance' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $classReportId = IdGenerator::generate(['table' => 'class_reports', 'trow' => 'classReportId', 'length' => 8, 'prefix' => 'CRB']);
        $params = [
            'classReportId' => $classReportId,
            'teacherId' => $this->request['teacherId'],
            'classId' => $this->request['classId'],
            'studentId' => $this->request['studentId'],
            'campusId' => $this->request['campusId'],
            'status' => $this->request['status'],
            'date' => Carbon::now(),
            'preparation' => $this->request['preparation'],
            'attitude' => $this->request['attitude'],
            'participation' => $this->request['participation'],
            'comment' => $this->request['comment'],
            'attendance' => $this->request['attendance'],
        ];
        $newClassReport = ClassReports::create($params);
        // $paramssss = ClassReports::where('classReportId', $params['classReportId'])
        // ->update(['date' => $newClassReport['create_at']]);
        return $this->successClassRequest($newClassReport);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($classReportId)
    {

        $classReport = ClassReports::join('teachers', 'class_reports.teacherId', '=', 'teachers.teacherId')
        ->join('classes', 'class_reports.classId', '=', 'classes.classId')
        ->join('students', 'class_reports.studentId', '=', 'students.studentId')
        ->join('campuses', 'class_reports.campusId', '=', 'campuses.campusId')
        ->join('student_products','class_reports.studentId', '=', 'student_products.studentId')
        ->join('products', 'student_products.productId', '=', 'products.productId')
        ->select(
            'class_reports.classReportId',
            'teachers.teacherId', 
            'teachers.name as teacherName', 
            'classes.classId', 
            'classes.name as className', 
            'students.studentId', 
            'students.name as studentName', 
            'campuses.campusId',
            'campuses.name as campusName', 
            'class_reports.date',
            'class_reports.comment',
            'class_reports.preparation',
            'class_reports.attitude',
            'class_reports.participation',
            'products.productId',
            'products.name as productName',
            )
        ->where('classReportId', $classReportId)->get();
        return $this->successClassReport($classReport);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($classReportId)
    {
        $classReport = ClassReports::find($classReportId);
        if (empty($this->request['teacherId'])) {
            $this->request['teacherId'] = $classReport['teacherId'];
        }
        if (empty($this->request['classId'])) {
            $this->request['classId'] = $classReport['classId'];
        }
        if (empty($this->request['studentId'])) {
            $this->request['studentId'] = $classReport['studentId'];
        }
        if (empty($this->request['campusId'])) {
            $this->request['campusId'] = $classReport['campusId'];
        }
        if (empty($this->request['status'])) {
            $this->request['status'] = $classReport['status'];
        }
        if (empty($this->request['date'])) {
            $this->request['date'] = $classReport['date'];
        }
        if (empty($this->request['preparation'])) {
            $this->request['preparation'] = $classReport['preparation'];
        }
        if (empty($this->request['attitude'])) {
            $this->request['attitude'] = $classReport['attitude'];
        }
        if (empty($this->request['participation'])) {
            $this->request['participation'] = $classReport['participation'];
        }
        if (empty($this->request['comment'])) {
            $this->request['comment'] = $classReport['comment'];
        }
        $validator = Validator::make($this->request->all(), [
            // 'teacherId' => 'string|required',
            // 'classId' => 'string|required',
            // 'studentId' => 'string|required',
            // 'campusId' => 'string|required',
            // 'status' => 'string|required',
            // 'date' => 'string|required',
            // 'preparation' => 'string|required',
            // 'attitude' => 'string|required',
            // 'participation' => 'string|required',
            'comment' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $ClassReport['teacherId'] = $this->request['teacherId'],
            $ClassReport['classId'] = $this->request['classId'],
            $ClassReport['studentId'] = $this->request['studentId'],
            $ClassReport['campusId'] = $this->request['campusId'],
            $ClassReport['status'] = $this->request['status'],
            $ClassReport['date'] = $this->request['date'],
            $ClassReport['preparation'] = $this->request['preparation'],
            $ClassReport['attitude'] = $this->request['attitude'],
            $ClassReport['participation'] = $this->request['participation'],
            $ClassReport['comment'] = $this->request['comment'],
        ];

        $newClassReportsData = $classReport->update($params);
        return $this->successClassReport($newClassReportsData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($classReportId)
    {
        $ClassReport = ClassReports::find($classReportId);
        $deleteClassReport = $ClassReport->delete();
        return $this->successClassReport($deleteClassReport);
    }
}
