<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ClassTimes;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

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
        $classEndDate = $classTimeParams['classEndDate'];
        try{
            foreach($classTimes as $classTime) {
                $classTimeId = IdGenerator::generate(['table' => 'class_times', 'trow' => 'classTimeId', 'length' => 7, 'prefix' => 'CT']);
                $classTimeResult = (explode('-', $classTime));
                $params = [
                    'classTimeId' => $classTimeId,
                    'classId' => $classId,
                    'day' => $classTimeResult[0],
                    'classTimeSlot' => $classTimeResult[1],
                    'classStartDate' => $classTimeParams['classStartDate'],
                ];
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
    public static function update($classTimeParams)
    {
        $classId = $classTimeParams['classId'];
        $classTimes = $classTimeParams['classTime'];
        $classStartDate = $classTimeParams['classStartDate'];
        $classEndDate = $classTimeParams['classEndDate'];
        $classHaveTime = ClassTimes::where('classId', $classId)->get();
        try{
            if(!$classHaveTime->isEmpty()){
                ClassTimes::where('classId', $classId)->delete();
                foreach($classTimes as $classTime) {
                    $classTimeId = IdGenerator::generate(['table' => 'class_times', 'trow' => 'classTimeId', 'length' => 7, 'prefix' => 'CT']);
                    $classTimeResult = (explode('-', $classTime));
                    $params = [
                        'classTimeId' => $classTimeId,
                        'classId' => $classId,
                        'day' => $classTimeResult[0],
                        'classTimeSlot' => $classTimeResult[1],
                        'classStartDate' => $classStartDate,
                    ];
                    $params['classEndDate'] = $classEndDate;
    
                    ClassTimes::create($params);
                }
            }
            if($classHaveTime->isEmpty()){
                foreach($classTimes as $classTime) {
                    $classTimeId = IdGenerator::generate(['table' => 'class_times', 'trow' => 'classTimeId', 'length' => 7, 'prefix' => 'CT']);
                    $classTimeResult = (explode('-', $classTime));
                    $params = [
                        'classTimeId' => $classTimeId,
                        'classId' => $classId,
                        'day' => $classTimeResult[0],
                        'classTimeSlot' => $classTimeResult[1],
                        'classStartDate' => $classStartDate,
                    ];
                    $params['classEndDate'] = $classEndDate;
    
                    ClassTimes::create($params);
                }
            }
        } catch(Exception $e){
            return $e->getMessage();
        }
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
