<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
use App\Models\ClassTimes;
use App\Models\Teachers;
use DateTime;
use DateTimeZone;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function getClass()
    {
        $classesData = Classes::all();
        foreach($classesData as $class){
            $classId = $class['classId'];
            $class['classTime'] = Classes::join('class_times', 'classes.classId', '=', 'class_times.classId')
            ->join('teachers', 'classes.onlineTeacher', '=', 'teachers.teacherId')
            ->leftJoin('class_time_slots', 'class_times.classTimeSlot', '=', 'class_time_slots.name')
            ->select(
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classId,classes.name,class_times.day,teachers.name)) as Class'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_times.day)) as day'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.onlineTeacher,teachers.name)) as teacher'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_time_slots.classStart)) as startTime'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_time_slots.classEnd)) as endTime'),
                'class_times.classTimeSlot',
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classStartDate, class_times.classEndDate)) as Date'),
            )
            ->where('classes.classId', $classId)
            // ->where('classes.typeOfClass', 'online')
            ->where('classes.expired', 0)
            ->groupBy('class_times.classTimeSlot')
            ->get();
        }

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
            'numberOfStudent' => 'integer|required',
            'onlineTeacher' => 'string|required',
            'productIds' => 'array|required',
            // 'classday' => 'string',
            // 'classTimeSlot' => 'string|required',
            'classTime' => 'array|required',
            'classStartDate' => 'date|required',
            'status' => 'string',
            'typeOfClass' => 'string|required',
            'initialTextbook' => 'string',
            'level' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $classId = IdGenerator::generate(['table' => 'classes', 'trow' => 'classId', 'length' => 7, 'prefix' => 'CL']);
        $classTimes = $this->request['classTime'];
        $params = [
            'classId' => $classId,
            'name' => $this->request['name'],
            'level' => $this->request['level'],
            'numberOfStudent' => $this->request['numberOfStudent'],
            // 'subject' => $this->request['subject'],
            'onlineTeacher' => $this->request['onlineTeacher'],
            // 'classday' => $this->request['classday'],
            // 'classTimeSlot' => $this->request['classTimeSlot'],
            // 'classTime' => $classTime,
            'classStartDate' => $this->request['classStartDate'],
            'status' => $this->request['status'],
            'typeOfClass' => $this->request['typeOfClass'],
            'initialTextbook' => $this->request['initialTextbook'],
            'expired' => 0,
        ];

        $productNumber = count($this->request['productIds']);
        if (!empty($this->request['duration'])) {
            $params['duration'] = $this->request['duration'];
        }
        if (!empty($this->request['classStartDate'])) {
            $classEndDate = date('Y-m-d', strtotime($this->request['classStartDate'] . ' + ' . $productNumber * 2 . ' months'));
            $params['classEndDate'] = $classEndDate;
        }
        $newClass = new ClassResource(Classes::create($params));

        foreach ($classTimes as $classTime) {
            $classTimeSlot = $classTime['classTimeSlot'];
            $days = $classTime['day'];

            foreach ($days as $day) {
                $formatted = $day . "-" . $classTimeSlot;
                $classTimeResults[] = $formatted;
            }
        }
        $newClass['classTime'] = $classTimeResults;
        $classTimeParams = [
            'classId' => $classId,
            'classTimes' => $classTimeResults,
            'classStartDate' => $this->request['classStartDate'],
            'classEndDate' => $classEndDate,
        ];
        ClassTimeController::store($classTimeParams);
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
        $validator = Validator::make($this->request->all(), [
            'name' => 'string',
            'numberOfStudent' => 'integer',
            // 'subject' => 'string|required',
            'onlineTeacher' => 'string',
            // 'classday' => 'string|required',
            // 'classTimeSlot' => 'string|required',
            'classStartDate' => 'date',
            'status' => 'string',
            'typeOfClass' => 'string',
            'initialTextbook' => 'string',
            'expired' => 'integer',
            'classTime' => 'array',
            'productIds' => 'array',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $class = Classes::where('classId', $classId)->first();
        if(!empty($this->request['name'])){
            $params['name'] = $this->request['name'];
        }
        if(!empty($this->request['level'])){
            $params['level'] = $this->request['level'];
        }
        if(!empty($this->request['numberOfStudent'])){
            $params['numberOfStudent'] = $this->request['numberOfStudent'];
        }
        if(!empty($this->request['onlineTeacher'])){
            $params['onlineTeacher'] = $this->request['onlineTeacher'];
        }
        if(!empty($this->request['classStartDate'])){
            $params['classStartDate'] = $this->request['classStartDate'];
            $classTimeParams['classStartDate'] = $this->request['classStartDate'];
            if(!empty($this->request['productIds'])){
                $productNumber = count($this->request['productIds']);
                $classEndDate = date('Y-m-d', strtotime($this->request['classStartDate'] . ' + ' . $productNumber * 2 . ' months'));
                $params['classEndDate'] = $classEndDate;
                $classTimeParams['classEndDate'] = $classEndDate;
            }
        }
        if(!empty($this->request['status'])){
            $params['status'] = $this->request['status'];
        }
        if(!empty($this->request['typeOfClass'])){
            $params['typeOfClass'] = $this->request['typeOfClass'];
        }
        if(!empty($this->request['initialTextbook'])){
            $params['initialTextbook'] = $this->request['initialTextbook'];
        }
        if(!empty($this->request['expired'])){
            $params['expired'] = $this->request['expired'];
        }
        $newInfoClass = $class->update($params);
        if(!empty($this->request['classTime'])){
            foreach ($this->request['classTime'] as $classTime) {
                $classTimeSlot = $classTime['classTimeSlot'];
                $days = $classTime['day'];
    
                foreach ($days as $day) {
                    $formatted = $day . "-" . $classTimeSlot;
                    $classTimeResults[] = $formatted;
                }
            }
            $classTimeParams['classTime'] = $classTimeResults;
        }
        $classTimeParams['classId'] = $classId;
        ClassTimeController::update($classTimeParams);
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

    // function calculateEndDate($class)
}
