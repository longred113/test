<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
use App\Models\Teachers;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
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
        $classesData = ClassResource::collection(Classes::all());
        return $this->successClassRequest($classesData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $onlineTeacher = Teachers::where('type', 'online')->get();
        $validator = Validator::make($this->request->all(), [
            'name' => 'string|required',
            // 'numberOfStudent' => 'integer|required',
            // 'subject' => 'string|required',
            // 'onlineTeacher' => 'string|required',
            // 'classday' => 'string|required',
            // 'classTimeSlot' => 'string|required',
            // 'classStartDate' => 'date|required',
            // 'status' => 'string|required',
            'typeOfClass' => 'string',
            'initialTextbook' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $classId = IdGenerator::generate(['table'=>'classes', 'trow' => 'classId', 'length' => 7, 'prefix' => 'CL']);
        $params = [
            'classId' => $classId,
            'name' => $this->request['name'],
            'numberOfStudent' => $this->request['numberOfStudent'],
            'subject' => $this->request['subject'],
            'onlineTeacher' => $this->request['onlineTeacher'],
            'classday' => $this->request['classday'],
            'classTimeSlot' => $this->request['classTimeSlot'],
            'classStartDate' => $this->request['classStartDate'],
            'status' => $this->request['status'],
            'typeOfClass' => $this->request['typeOfClass'],
            'initialTextbook' => $this->request['initialTextbook'],
        ];
        $newClass = new ClassResource(Classes::create($params));
        return $this->successClassRequest($newClass);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($classId)
    {
        $class = Classes::find($classId);
        $classData = new ClassResource($class);
        return $this->successClassRequest($classData);
    }

    public function getClassFromTeacher($onlineTeacher)
    {
        $class = Classes::where('onlineTeacher', $onlineTeacher)->get();
        return $this->successClassRequest($class);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($classId)
    {
        $class = Classes::find($classId);
        if(empty($this->request['name'])) {
            $this->request['name'] = $class['name'];
        }
        if(empty($this->request['numberOfStudent'])) {
            $this->request['numberOfStudent'] = $class['numberOfStudent'];
        }
        if(empty($this->request['subject'])) {
            $this->request['subject'] = $class['subject'];
        }
        if(empty($this->request['onlineTeacher'])) {
            $this->request['onlineTeacher'] = $class['onlineTeacher'];
        }
        if(empty($this->request['classday'])) {
            $this->request['classday'] = $class['classday'];
        }
        if(empty($this->request['classTimeSlot'])) {
            $this->request['classTimeSlot'] = $class['classTimeSlot'];
        }
        if(empty($this->request['classStartDate'])) {
            $this->request['classStartDate'] = $class['classStartDate'];
        }
        if(empty($this->request['status'])) {
            $this->request['status'] = $class['status'];
        }
        if(empty($this->request['typeOfClass'])) {
            $this->request['typeOfClass'] = $class['typeOfClass'];
        }
        if(empty($this->request['initialTextbook'])) {
            $this->request['initialTextbook'] = $class['initialTextbook'];
        }
        $validator = Validator::make($this->request->all(), [
            'name' => 'string|required',
            'numberOfStudent' => 'integer|required',
            // 'subject' => 'string|required',
            // 'onlineTeacher' => 'string|required',
            // 'classday' => 'string|required',
            // 'classTimeSlot' => 'string|required',
            // 'classStartDate' => 'date|required',
            // 'status' => 'string|required',
            'typeOfClass' => 'string',
            'initialTextbook' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $class['name'] = $this->request['name'],
            $class['numberOfStudent'] = $this->request['numberOfStudent'],
            $class['subject'] = $this->request['subject'],
            $class['onlineTeacher'] = $this->request['onlineTeacher'],
            $class['classday'] = $this->request['classday'],
            $class['classTimeSlot'] = $this->request['classTimeSlot'],
            $class['classStartDate'] = $this->request['classStartDate'],
            $class['status'] = $this->request['status'],
            $class['typeOfClass'] = $this->request['typeOfClass'],
            $class['initialTextbook'] = $this->request['initialTextbook'],
        ];
        $newInfoClass = $class->update($params);
        return $this->successClassRequest($newInfoClass);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($classId)
    {
        $class = Classes::find($classId);
        $deleteClass = $class->delete();
        return $this->successClassRequest($deleteClass);
    }
}
