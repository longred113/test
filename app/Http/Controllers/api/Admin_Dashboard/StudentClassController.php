<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Student;
use App\Http\Resources\StudentClassResource;
use App\Models\Classes;
use App\Models\StudentClasses;
use App\Models\Students;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentClassController extends Controller
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
        $studentClassesData = StudentClassResource::collection(StudentClasses::all());
        return $this->successStudentClassRequest($studentClassesData);
    }

    public function getClassByStudent($studentId)
    {
        $studentClassesData = StudentClasses::join('classes', 'student_classes.classId', '=', 'classes.classId')
            ->select(
                'classes.classId',
                'classes.name',
            )
            ->where('studentId', $studentId)
            ->distinct()
            ->get();
        return $this->successStudentClassRequest($studentClassesData);
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
            'studentId' => 'string|required',
            'classId' => 'string|required',
            'point' => 'integer',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $studentClassId = IdGenerator::generate(['table' => 'student_classes', 'trow' => 'studentClassId', 'length' => 7, 'prefix' => 'SC']);
        $params = [
            'studentClassId' => $studentClassId,
            'studentId' => $this->request['studentId'],
            'classId' => $this->request['classId'],
            'point' => $this->request['point'],
        ];
        $newStudentClassData = new StudentClassResource(StudentClasses::create($params));
        return $this->successStudentClassRequest($newStudentClassData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($studentClassId)
    {
        $studentClass = StudentClasses::find($studentClassId);
        $studentClassData = new StudentClassResource($studentClass);
        return $this->successStudentClassRequest($studentClassData);
    }

    public function getStudentFromClass($classId)
    {
        $studentClass = StudentClasses::where('classId', $classId)->get();
        return $this->successStudentClassRequest($studentClass);
    }

    public function getClassFromStudent()
    {
        $validator = Validator::make($this->request->all(), [
            'studentIds' => 'array|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $studentIds = $this->request['studentIds'];
        try {
            $studentsData = Students::whereIn('students.studentId', $studentIds)
                ->join('student_classes', 'students.studentId', '=', 'student_classes.studentId')
                ->join('classes', 'student_classes.classId', '=', 'classes.classId')
                ->join('teachers', 'classes.onlineTeacher', '=', 'teachers.teacherId')
                ->selectRaw(
                    'teachers.name as teacherName, 
                    teachers.teacherId, 
                    GROUP_CONCAT(DISTINCT(classes.name)) as classNames, 
                    GROUP_CONCAT(DISTINCT(students.name)) as studentNames'
                )
                ->groupBy('teachers.name', 'teachers.teacherId')
                ->get();
            foreach ($studentsData as $student) {
                $classNamesArray = explode(',', $student->classNames);
                $student->classNames = $classNamesArray;
            }
            foreach ($studentsData as $student) {
                $studentNamesArray = explode(',', $student->studentNames);
                $student->studentNames = $studentNamesArray;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successStudentRequest($studentsData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($studentClassId)
    {
        $studentClass = StudentClasses::find($studentClassId);
        $validator = Validator::make($this->request->all(), [
            'studentId' => 'string|required',
            'classId' => 'string|required',
            'point' => 'integer',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $studentId = $this->request['studentId'];
        $classId = $this->request['classId'];
        $params = [
            'studentId' => $studentId,
            'classId' => $classId,
            'point' => $this->request['point'],
        ];
        $studentClass = $studentClass->update($params);
        return $this->successStudentClassRequest($studentClass);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($studentClassId)
    {
        $studentClass = StudentClasses::find($studentClassId);
        $deleteStudentClass = $studentClass->delete();
        return $this->successStudentClassRequest($deleteStudentClass);
    }

    public static function createStudentClassByAdmin($studentClassParams)
    {
        $studentId = $studentClassParams['studentId'];
        $classId = $studentClassParams['classId'];
        // $point = $studentClassParams['point'];
        $studentClassId = IdGenerator::generate(['table' => 'student_classes', 'trow' => 'studentClassId', 'length' => 7, 'prefix' => 'SC']);
        $params = [
            'studentClassId' => $studentClassId,
            'studentId' => $studentId,
            'classId' => $classId,
            // 'point' => $point,
        ];
        $newStudentClassData = StudentClasses::create($params);
    }
}
