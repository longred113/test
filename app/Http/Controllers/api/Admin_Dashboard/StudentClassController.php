<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentClassResource;
use App\Models\StudentClasses;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
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
        if($validator->fails()){
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $studentClassId = IdGenerator::generate(['table'=>'student_classes', 'trow' => 'studentClassId', 'length' => 7, 'prefix' => 'SC']);
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $validator = Validator::make($this->request->all(), [
            'studentId' => 'string|required',
            'classId' => 'string|required',
        ]);
        if($validator->fails()){
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $studentId = $this->request['studentId'];
        $classId = $this->request['classId'];
        $params = [
            'studentId' => $studentId,
            'classId' => $classId,
            'point' => $this->request['point'],
        ];
        $studentClass = StudentClasses::where('classId', $classId)
        ->where('studentId', $studentId)->update($params);
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
}
