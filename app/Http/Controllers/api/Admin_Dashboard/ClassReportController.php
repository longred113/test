<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ClassReports;
use App\Http\Resources\ClassReportsResource;
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
        $data = ClassReportsResource::collection(ClassReports::all());
        return $this->successClassRequest($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $validator = Validator::make($this->request->all(), [
            'teacherId' => 'string|required',
            // 'classId' => 'string|required',
            // 'studentId' => 'string|required',
            // 'campusId' => 'string|required',
            // 'status' => 'string|required',
            // 'date' => 'string|required',
            // 'preparation' => 'string|required',
            // 'attitude' => 'string|required',
            // 'participation' => 'string|required',
            // 'comment' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $classReportId = IdGenerator::generate(['table'=>'class_reports', 'trow' => 'classReportId', 'length' => 9, 'prefix' => 'CRB-']);
        $params = [
            'classReportId' => $classReportId,
            'teacherId' => $this->request['teacherId'],
            'classId' => $this->request['classId'],
            'studentId' => $this->request['studentId'],
            'campusId' => $this->request['campusId'],
            'status' => $this->request['status'],
            'date' => $this->request['date'],
            'preparation' => $this->request['preparation'],
            'attitude' => $this->request['attitude'],
            'participation' => $this->request['participation'],
            'comment' => $this->request['comment'],
        ];
       
        $newClassReport = new ClassReportsResource(ClassReports::create($params));
        return $this->successClassRequest($newClassReport);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ClassReport)
    {
        $ClassReports = ClassReports::find($ClassReport);
        $ClassReportsData = new ClassReportsResource($ClassReports);
        return $this->successClassReport($ClassReportsData);
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