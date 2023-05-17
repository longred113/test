<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ClassHolidays;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;

class ClassHolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
public static function store($classHolidayParams)
    {
        $classId = $classHolidayParams['classId'];
        $holidayIds = $classHolidayParams['holidayIds'];
        $classHolidays = ClassHolidays::where('classId', $classId)->get();
        try{
            if(!$classHolidays->isEmpty()){
                ClassHolidays::where('classId', $classId)->delete();
                foreach($holidayIds as $holidayId) {
                    $classHolidayId = IdGenerator::generate(['table' => 'class_holidays', 'trow' => 'classHolidayId', 'length' => 7, 'prefix' => 'CH']);
                    $params = [
                        'classHolidayId' => $classHolidayId,
                        'classId' => $classId,
                        'holidayId' => $holidayId,
                    ];
                    ClassHolidays::create($params);
                }
            }
            if($classHolidays->isEmpty()){
                foreach($holidayIds as $holidayId) {
                    $classHolidayId = IdGenerator::generate(['table' => 'class_holidays', 'trow' => 'classHolidayId', 'length' => 7, 'prefix' => 'CH']);
                    $params = [
                        'classHolidayId' => $classHolidayId,
                        'classId' => $classId,
                        'holidayId' => $holidayId,
                    ];
                    ClassHolidays::create($params);
                }
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function update($holidayParams)
    {
        $holidayIds = $holidayParams['holidayIds'];
        $classId = $holidayParams['classId'];
        try{
            $classHolidays = ClassHolidays::where('classId', $classId)->get();
            if(!$classHolidays->isEmpty()){
                ClassHolidays::where('classId', $classId)->delete();
                foreach($holidayIds as $holidayId) {
                    $classHolidayId = IdGenerator::generate(['table' => 'class_holidays', 'trow' => 'classHolidayId', 'length' => 7, 'prefix' => 'CH']);
                    $params = [
                        'classHolidayId' => $classHolidayId,
                        'classId' => $classId,
                        'holidayId' => $holidayId,
                    ];
                    ClassHolidays::create($params);
                }
            }
            if($classHolidays->isEmpty()){
                foreach($holidayIds as $holidayId) {
                    $classHolidayId = IdGenerator::generate(['table' => 'class_holidays', 'trow' => 'classHolidayId', 'length' => 7, 'prefix' => 'CH']);
                    $params = [
                        'classHolidayId' => $classHolidayId,
                        'classId' => $classId,
                        'holidayId' => $holidayId,
                    ];
                    ClassHolidays::create($params);
                }
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
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
