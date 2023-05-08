<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ClassTimes;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;

class ClassTimeController extends Controller
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
        $classTime = ClassTimes::all()->groupBy('classId'); 
        return $this->successClassTimeRequest($classTime);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store($classTimeParams)
    {
        $classId = $classTimeParams['classId'];
        $classTimes = $classTimeParams['classTimes'];
        try{
            foreach($classTimes as $classTime) {
                $classTimeId = IdGenerator::generate(['table' => 'class_times', 'trow' => 'classTimeId', 'length' => 7, 'prefix' => 'CT']);
                $classTime = (explode('-', $classTime));
                $params = [
                    'classTimeId' => $classTimeId,
                    'classId' => $classId,
                    'day' => $classTime[0],
                    'classTimeSlot' => $classTime[1],
                    'classStartDate' => $classTimeParams['classStartDate'],
                ];
                $classEndDate = date('Y-m-d', strtotime($classTimeParams['classStartDate'] . ' + 2 months'));
                $params['classEndDate'] = $classEndDate;

                ClassTimes::create($params);
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($classId)
    {
        $classTime = ClassTimes::where('classId', $classId)->get();
        return $this->successClassTimeRequest($classTime);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($classTimeId)
    {
        $classTime = ClassTimes::where('classTimeId', $classTimeId)->first();
        if(!empty($this->request['day'])) {
            $params['day'] = $this->request['day'];
        }
        if(!empty($this->request['classTimeSlot'])) {
            $params['classTimeSlot'] = $this->request['classTimeSlot'];
        }
        if(!empty($this->request['classStartDate'])) {
            $params['classStartDate'] = $this->request['classStartDate'];
        }
        if(!empty($this->request['classEndDate'])) {
            $params['classEndDate'] = $this->request['classEndDate'];
        }
        $classTime->update($params);
        return $this->successClassTimeRequest($classTime);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($classTimeId)
    {
        $classTime = ClassTimes::where('classTimeId', $classTimeId)->delete();
        return $this->successClassTimeRequest($classTime);
    }
}
