<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Exports\ClassFeedbackExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ClassFeedbacks;
use App\Http\Resources\ClassFeedbackResource;
use App\Models\Students;
use App\Models\Teachers;
use Carbon\Carbon;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Maatwebsite\Excel\Facades\Excel;

class ClassFeedbackController extends Controller
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
        try {
            $classFeedbackData = ClassFeedbacks::join('teachers', 'class_feedbacks.teacherId', '=', 'teachers.teacherId')
                ->join('classes', 'class_feedbacks.classId', '=', 'classes.classId')
                ->join('students', 'class_feedbacks.studentId', '=', 'students.studentId')
                ->join('campuses', 'class_feedbacks.campusId', '=', 'campuses.campusId')
                ->select(
                    'class_feedbacks.classFeedbackId',
                    'teachers.teacherId',
                    'teachers.name as teacherName',
                    'classes.classId',
                    'classes.name as className',
                    'students.studentId',
                    'students.name as studentName',
                    'campuses.campusId',
                    'campuses.name as campusName',
                    'class_feedbacks.satisfaction',
                    'class_feedbacks.date',
                    'class_feedbacks.comment'
                )
                ->get();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successClassFeedback($classFeedbackData);
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
            // 'date' => 'string|required',
            'satisfaction' => 'integer',
            // 'comment' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $classFeedbackId = IdGenerator::generate(['table' => 'class_feedbacks', 'trow' => 'classFeedbackId', 'length' => 8, 'prefix' => 'CFB']);
        $params = [
            'classFeedbackId' => $classFeedbackId,
            'teacherId' => $this->request['teacherId'],
            'classId' => $this->request['classId'],
            'studentId' => $this->request['studentId'],
            'campusId' => $this->request['campusId'],
            'date' => Carbon::now(),
            'satisfaction' => $this->request['satisfaction'],
            'comment' => $this->request['comment'],
        ];
        $newClassFeedback = new ClassFeedbackResource(ClassFeedbacks::create($params));
        return $this->successClassFeedback($newClassFeedback);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($classFeedbackId)
    {
        $classFeedbackData = ClassFeedbacks::join('teachers', 'class_feedbacks.teacherId', '=', 'teachers.teacherId')
            ->join('classes', 'class_feedbacks.classId', '=', 'classes.classId')
            ->join('students', 'class_feedbacks.studentId', '=', 'students.studentId')
            ->join('campuses', 'class_feedbacks.campusId', '=', 'campuses.campusId')
            ->join('student_products', 'class_feedbacks.studentId', '=', 'student_products.studentId')
            ->join('products', 'student_products.productId', '=', 'products.productId')
            ->select(
                'class_feedbacks.classFeedbackId',
                'teachers.teacherId',
                'teachers.name as teacherName',
                'classes.classId',
                'classes.name as className',
                'students.studentId',
                'students.name as studentName',
                'campuses.campusId',
                'campuses.name as campusName',
                'class_feedbacks.satisfaction',
                'class_feedbacks.date',
                'class_feedbacks.comment',
                'products.productId',
                'products.name as productName',
            )
            ->where('classFeedbackId', $classFeedbackId)->get();
        return $this->successClassFeedback($classFeedbackData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($classFeedbackId)
    {
        $classFeedback = ClassFeedbacks::find($classFeedbackId);
        if (empty($this->request['teacherId'])) {
            $this->request['teacherId'] = $classFeedback['teacherId'];
        }
        if (empty($this->request['classId'])) {
            $this->request['classId'] = $classFeedback['classId'];
        }
        if (empty($this->request['studentId'])) {
            $this->request['studentId'] = $classFeedback['studentId'];
        }
        if (empty($this->request['campusId'])) {
            $this->request['campusId'] = $classFeedback['campusId'];
        }
        if (empty($this->request['date'])) {
            $this->request['date'] = $classFeedback['date'];
        }
        if (empty($this->request['satisfaction'])) {
            $this->request['satisfaction'] = $classFeedback['satisfaction'];
        }
        if (empty($this->request['comment'])) {
            $this->request['comment'] = $classFeedback['comment'];
        }
        $validator = Validator::make($this->request->all(), [
            // 'teacherId' => 'string|required',
            // 'classId' => 'string|required',
            // 'studentId' => 'string|required',
            // 'campusId' => 'string|required',
            // 'date' => 'string|required',
            // 'satisfaction' => 'string|required',
            'comment' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $classFeedback['teacherId'] = $this->request['teacherId'],
            $classFeedback['classId'] = $this->request['classId'],
            $classFeedback['studentId'] = $this->request['studentId'],
            $classFeedback['campusId'] = $this->request['campusId'],
            $classFeedback['date'] = $this->request['date'],
            $classFeedback['satisfaction'] = $this->request['satisfaction'],
            $classFeedback['comment'] = $this->request['comment'],
        ];

        $newClassFeedbacksData = $classFeedback->update($params);
        return $this->successClassFeedback($newClassFeedbacksData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($classFeedbackId)
    {
        $ClassFeedback = ClassFeedbacks::find($classFeedbackId);
        $deleteClassFeedback = $ClassFeedback->delete();
        return $this->successClassFeedback($deleteClassFeedback);
    }

    //create function for show class feedback of one teacher and avage satisfaction and group by class, student, campus

    public function showClassFeedbackOfOneTeacher($teacherId)
    {
        try {
            $classFeedback = ClassFeedbacks::leftJoin('teachers', 'class_feedbacks.teacherId', '=', 'teachers.teacherId')
                ->leftJoin('classes', 'class_feedbacks.classId', '=', 'classes.classId')
                ->leftJoin('students', 'class_feedbacks.studentId', '=', 'students.studentId')
                ->leftJoin('campuses', 'class_feedbacks.campusId', '=', 'campuses.campusId')
                ->leftJoin('student_products', 'class_feedbacks.studentId', '=', 'student_products.studentId')
                ->leftJoin('products', 'student_products.productId', '=', 'products.productId')
                ->selectRaw(
                    'class_feedbacks.classFeedbackId,
                    teachers.teacherId,
                    teachers.name as teacherName,
                    classes.classId,
                    classes.name as className,
                    students.studentId,
                    students.name as studentName,
                    campuses.campusId,
                    campuses.name as campusName,
                    class_feedbacks.satisfaction,
                    class_feedbacks.date,
                    class_feedbacks.comment,
                    GROUP_CONCAT(DISTINCT CONCAT_WS(":", products.productId, products.name)) as productData'
                )
                ->where('teachers.teacherId', $teacherId)
                ->groupBy('class_feedbacks.classFeedbackId','students.studentId')
                ->get();
            $classFeedbackData = $classFeedback->groupBy('studentId');   
            $teacher = Teachers::where('teacherId', $teacherId)->select('teacherId', 'name')->first();
            $groupClassFeedback = [
                'teacherData' => $teacher,
                'studentData' => $classFeedbackData,
            ];
            $averageSatisfaction = ClassFeedbacks::where('teacherId', $teacherId)->avg('satisfaction');
            $data = [
                'classFeedbackData' => $groupClassFeedback,
                'averageSatisfaction' => round($averageSatisfaction),
            ];
        } catch (\Exception $e) {
            return $this->errorBadRequest($e->getMessage());
        }
        return $this->successClassFeedback($data);
    }

    public function exportToExcelFile($teacherId)
    {
        $classFeedbackData = ClassFeedbacks::join('teachers', 'class_feedbacks.teacherId', '=', 'teachers.teacherId')
            ->join('classes', 'class_feedbacks.classId', '=', 'classes.classId')
            ->join('students', 'class_feedbacks.studentId', '=', 'students.studentId')
            ->join('campuses', 'class_feedbacks.campusId', '=', 'campuses.campusId')
            ->join('student_products', 'class_feedbacks.studentId', '=', 'student_products.studentId')
            ->join('products', 'student_products.productId', '=', 'products.productId')
            ->select(
                'class_feedbacks.classFeedbackId',
                'teachers.teacherId',
                'teachers.name as teacherName',
                'classes.classId',
                'classes.name as className',
                'students.studentId',
                'students.name as studentName',
                'campuses.campusId',
                'campuses.name as campusName',
                'class_feedbacks.satisfaction',
                'class_feedbacks.date',
                'class_feedbacks.comment',
                'products.productId',
                'products.name as productName',
            )
            ->where('teachers.teacherId', $teacherId)
            ->get();
        $averageSatisfaction[] = ClassFeedbacks::where('teacherId', $teacherId)->avg('satisfaction');
        $export = new ClassFeedbackExport($classFeedbackData, $averageSatisfaction);
        return Excel::download($export, 'classFeedback.xlsx');
    }
}
