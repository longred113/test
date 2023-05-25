<?php

namespace App\Http\Controllers\api\Campus_Dashboard;

use App\Http\Controllers\api\Admin_Dashboard\ClassHolidayController;
use App\Http\Controllers\api\Admin_Dashboard\ClassProductController;
use App\Http\Controllers\api\Admin_Dashboard\ClassTimeController;
use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\ClassProducts;
use App\Models\Holidays;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OffClassController extends Controller
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
    public function index($campusId)
    {
        $offlineClass = Classes::where('category', 'offline')->where('campusId', $campusId)->get();
        return $this->successClassRequest($offlineClass);
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
            'name' => 'string|required',
            'numberOfStudent' => 'integer|required',
            'onlineTeacher' => 'string|required',
            'productIds' => 'array|required',
            'classTime' => 'array|required',
            'classStartDate' => 'date|required',
            'status' => 'string',
            'typeOfClass' => 'string|required',
            'initialTextbook' => 'string',
            'level' => 'string|required',
            'holidayIds' => 'array',
            'availableNumStudent' => 'integer|required',
            'category' => 'string|required',
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
            'category' => $this->request['category'],
        ];

        if (!empty($this->request['availableNumStudent'])) {
            if ($this->request['availableNumStudent'] > $this->request['numberOfStudent']) {
                return $this->errorBadRequest('Available number of student must be less than or equal to number of student');
            }
            $params['availableNumStudent'] = $this->request['availableNumStudent'];
        }

        $productNumber = count($this->request['productIds']);
        if (!is_null($this->request['holidayIds']) && !empty($this->request['classStartDate'])) {
            $holidayIds = $this->request['holidayIds'];
            $holidays = Holidays::whereIn('holidayId', $holidayIds)->get('duration');
            $offDates = 0;
            foreach ($holidays as $holiday) {
                $offDates = $offDates + $holiday->duration;
            }
            $classEndDate = date('Y-m-d', strtotime($this->request['classStartDate'] . ' + ' . $productNumber * 2 . ' months'));
            $classEndDate = date('Y-m-d', strtotime($classEndDate . ' + ' . $offDates . ' days'));
            $params['classEndDate'] = $classEndDate;
        }

        if (!empty($this->request['classStartDate']) && is_null($this->request['holidayIds'])) {
            $classEndDate = date('Y-m-d', strtotime($this->request['classStartDate'] . ' + ' . $productNumber * 2 . ' months'));
            $params['classEndDate'] = $classEndDate;
        }
        $newClass = Classes::create($params);

        $holidayParams = [
            'classId' => $classId,
            'holidayIds' => $holidayIds,
        ];
        ClassHolidayController::store($holidayParams);

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
    public function update($classId)
    {
        $validator = Validator::make($this->request->all(), [
            'name' => 'string',
            'numberOfStudent' => 'integer',
            'onlineTeacher' => 'string',
            'productIds' => 'array',
            'classTime' => 'array',
            'classStartDate' => 'date',
            'status' => 'string',
            'typeOfClass' => 'string',
            'initialTextbook' => 'string',
            'level' => 'string',
            'holidayIds' => 'array',
            'category' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $class = Classes::where('classId', $classId)->first();
        if (!empty($this->request['name'])) {
            $params['name'] = $this->request['name'];
        }
        if (!empty($this->request['numberOfStudent'])) {
            $params['numberOfStudent'] = $this->request['numberOfStudent'];
        }
        if (!empty($this->request['onlineTeacher'])) {
            $params['onlineTeacher'] = $this->request['onlineTeacher'];
        }
        if (!empty($this->request['classStartDate'])) {
            $params['classStartDate'] = $this->request['classStartDate'];
            $classTimeParams['classStartDate'] = $this->request['classStartDate'];
            if (!empty($this->request['productIds']) && empty($this->request['holidayIds'])) {
                $productNumber = count($this->request['productIds']);
                $classEndDate = date('Y-m-d', strtotime($this->request['classStartDate'] . ' + ' . $productNumber * 2 . ' months'));
                $params['classEndDate'] = $classEndDate;
                $classTimeParams['classEndDate'] = $classEndDate;
                $classProductParams = [
                    'classId' => $classId,
                    'productIds' => $this->request['productIds'],
                ];
                ClassProductController::updateMultipleProduct($classProductParams);
            }
            if (!empty($this->request['holidayIds']) && !empty($this->request['productIds'])) {
                $productNumber = count($this->request['productIds']);
                $holidayIds = $this->request['holidayIds'];
                $holidays = Holidays::whereIn('holidayId', $holidayIds)->get('duration');
                $offDates = 0;
                foreach ($holidays as $holiday) {
                    $offDates = $offDates + $holiday->duration;
                }
                $classEndDate = date('Y-m-d', strtotime($this->request['classStartDate'] . ' + ' . $productNumber * 2 . ' months'));
                $classEndDate = date('Y-m-d', strtotime($classEndDate . ' + ' . $offDates . ' days'));
                $params['classEndDate'] = $classEndDate;
                $classTimeParams['classEndDate'] = $classEndDate;
                $holidayParams = [
                    'classId' => $classId,
                    'holidayIds' => $holidayIds,
                ];
                ClassHolidayController::update($holidayParams);
                $classProductParams = [
                    'classId' => $classId,
                    'productIds' => $this->request['productIds'],
                ];
                ClassProductController::updateMultipleProduct($classProductParams);
            }
        }
        if (!empty($this->request['holidayIds']) && empty($this->request['classStartDate'])) {
            $holidayIds = $this->request['holidayIds'];
            $holidays = Holidays::whereIn('holidayId', $holidayIds)->get('duration');
            $offDates = 0;
            foreach ($holidays as $holiday) {
                $offDates = $offDates + $holiday->duration;
            }
            $classProducts = ClassProducts::where('classId', $classId)->get();
            $productNumber = count($classProducts);
            $classEndDate = date('Y-m-d', strtotime($class->classStartDate . ' + ' . $productNumber * 2 . ' months'));
            $classEndDate = date('Y-m-d', strtotime($classEndDate . ' + ' . $offDates . ' days'));
            $params['classEndDate'] = $classEndDate;
            $classTimeParams['classStartDate'] = $class->classStartDate;
            $classTimeParams['classEndDate'] = $classEndDate;
            $holidayParams = [
                'classId' => $classId,
                'holidayIds' => $holidayIds,
            ];
            ClassHolidayController::update($holidayParams);
        }
        if (!empty($this->request['status'])) {
            $params['status'] = $this->request['status'];
        }
        if (!empty($this->request['typeOfClass'])) {
            $params['typeOfClass'] = $this->request['typeOfClass'];
        }
        if (!empty($this->request['initialTextbook'])) {
            $params['initialTextbook'] = $this->request['initialTextbook'];
        }
        if (!empty($this->request['level'])) {
            $params['level'] = $this->request['level'];
        }
        $newInfoClass = $class->update($params);
        if (!empty($this->request['classTime'])) {
            foreach ($this->request['classTime'] as $classTime) {
                $classTimeSlot = $classTime['classTimeSlot'];
                $days = $classTime['day'];

                foreach ($days as $day) {
                    $formatted = $day . "-" . $classTimeSlot;
                    $classTimeResults[] = $formatted;
                }
            }
            $classTimeParams['classTime'] = $classTimeResults;
            $classTimeParams['classId'] = $classId;
            ClassTimeController::update($classTimeParams);
        }

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

    public function getClassOffFromProduct()
    {
        $validator = Validator::make($this->request->all(), [
            'productIds' => 'array|required',
            'campusId' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $productIds = $this->request->get('productIds');
        $campusId = $this->request->get('campusId');
        $classesOff = Classes::join('class_products', 'classes.classId', '=', 'class_products.classId')
        ->select(
            'classes.classId',
            'classes.name',
            DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS("-",class_products.productId)) as productIds'),
        )
        ->whereIn('class_products.productId', $productIds)
        ->where('classes.category', 'offline')
        ->where('classes.campusId', $campusId)
        ->groupBy('classes.classId')
        ->get();

        foreach($classesOff as $classOff) {
            $classOff->productIds = explode(',', $classOff->productIds);
        }

        return $this->successClassRequest($classesOff);
    }
}
