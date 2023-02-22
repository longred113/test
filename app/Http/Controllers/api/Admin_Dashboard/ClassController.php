<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
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
        $validator = Validator::make($this->request->all(), [
            'productId' => 'string|required',
            'name' => 'string|required',
            // 'numberOfStudent' => 'integer|required',
            // 'subject' => 'string|required',
            // 'onlineTeacher' => 'string|required',
            // 'classday' => 'string|required',
            // 'classTimeSlot' => 'string|required',
            // 'classStartDate' => 'date|required',
            // 'status' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $classId = IdGenerator::generate(['table'=>'classes', 'trow' => 'classId', 'length' => 8, 'prefix' => 'CL-']);
        $params = [
            'classId' => $classId,
            'productId' => $this->request['productId'],
            'name' => $this->request['name'],
            'numberOfStudent' => $this->request['numberOfStudent'],
            'subject' => $this->request['subject'],
            'onlineTeacher' => $this->request['onlineTeacher'],
            'classday' => $this->request['classday'],
            'classTimeSlot' => $this->request['classTimeSlot'],
            'classStartDate' => $this->request['classStartDate'],
            'status' => $this->request['status'],
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
        $validator = Validator::make($this->request->all(), [
            'productId' => 'string|required',
            'name' => 'string|required',
            'numberOfStudent' => 'integer|required',
            // 'subject' => 'string|required',
            // 'onlineTeacher' => 'string|required',
            // 'classday' => 'string|required',
            // 'classTimeSlot' => 'string|required',
            // 'classStartDate' => 'date|required',
            // 'status' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            $class['productId'] = $this->request['productId'],
            $class['name'] = $this->request['name'],
            $class['numberOfStudent'] = $this->request['numberOfStudent'],
            $class['subject'] = $this->request['subject'],
            $class['onlineTeacher'] = $this->request['onlineTeacher'],
            $class['classday'] = $this->request['classday'],
            $class['classTimeSlot'] = $this->request['classTimeSlot'],
            $class['classStartDate'] = $this->request['classStartDate'],
            $class['status'] = $this->request['status'],
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
