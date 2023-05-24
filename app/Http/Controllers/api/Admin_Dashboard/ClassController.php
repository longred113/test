<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Events\ClassExpired;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
use App\Models\ClassProducts;
use App\Models\ClassTimes;
use App\Models\Holidays;
use App\Models\Teachers;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
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
        $classesData = Classes::where('category', 'online')->get();
        return $this->successClassRequest($classesData);
    }

    public function getClass()
    {
        $classesData = Classes::all();
        $currentDate = date('Y-m-d');
        try {
            foreach ($classesData as $class) {
                if (now() > $class->classEndDate) {
                    event(new ClassExpired($class));
                }
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
                        DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classStartDate)) as startDate'),
                        DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_times.classEndDate)) as endDate'),
                    )
                    ->where('classes.classId', $classId)
                    // ->where('classes.typeOfClass', 'online')
                    ->where('classes.expired', 0)
                    ->groupBy('class_times.classTimeSlot')
                    ->get();
                $classProducts = Classes::leftJoin('class_products', 'classes.classId', '=', 'class_products.classId')
                    ->leftJoin('products', 'class_products.productId', '=', 'products.productId')
                    ->select(
                        DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",products.productId,products.name)) as products'),
                    )
                    ->where('classes.classId', $classId)
                    ->groupBy('classes.classId')
                    ->get();
                $classProducts = $classProducts->pluck('products')->toArray();
                $groupProduct = [];
                foreach ($classProducts as $product) {
                    $groupProduct = explode(',', $product);
                }
                // $class['product'] = $groupProduct;
                $classGroupProduct = [];
                foreach ($groupProduct as $clProduct) {
                    $classProduct = explode(':', $clProduct);
                    if (count($classProduct) >= 2) {
                        $classGroupProduct[] = [
                            'value' => $classProduct[0],
                            'label' => $classProduct[1],
                        ];
                    }
                }
                // return $classGroupProduct;
                $class['products'] = $classGroupProduct;

                $classHolidays = Classes::leftJoin('class_holidays', 'classes.classId', '=', 'class_holidays.classId')
                    ->leftJoin('holidays', 'class_holidays.holidayId', '=', 'holidays.holidayId')
                    ->select(
                        DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",holidays.holidayId, holidays.name)) as holidays'),
                    )
                    ->where('classes.classId', $classId)
                    ->groupBy('classes.classId')
                    ->get();
                $classHolidays = $classHolidays->pluck('holidays')->toArray();
                $holiday = [];
                foreach ($classHolidays as $classHoliday) {
                    $clHoliday = explode(':', $classHoliday);
                    if (count($clHoliday) >= 2) {
                        $holiday[] = [
                            'value' => $clHoliday[0],
                            'label' => $clHoliday[1],
                        ];
                    }
                }
                $class['holiday'] = $holiday;
                // Lấy ngày bắt đầu và ngày kết thúc của lớp học
                $startDate = $class['classStartDate'];
                $endDate = $class['classEndDate'];

                // Chuyển đổi ngày thành đối tượng DateTime
                $startDateTime = new DateTime($startDate);
                $endDateTime = new DateTime($endDate);
                $currentDateTime = new DateTime($currentDate);
                // Kiểm tra nếu ngày hiện tại nằm trong khoảng ngày bắt đầu và kết thúc
                if ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime) {
                    // Tính toán số tuần từ ngày bắt đầu đến ngày hiện tại
                    $diff = $startDateTime->diff($currentDateTime);
                    $currentWeek = ceil($diff->days / 7);

                    // Sử dụng biến $currentWeek ở đây để làm gì đó
                    // Ví dụ: in ra tuần hiện tại của lớp học
                }
                // Tính toán ngày bắt đầu và ngày kết thúc của tuần hiện tại
                $currentWeekStartDate = clone $currentDateTime;
                $currentWeekStartDate->modify('monday this week');
                $currentWeekEndDate = clone $currentDateTime;
                $currentWeekEndDate->modify('sunday this week');

                // Chuyển đổi thành định dạng mong muốn (Y-m-d)
                $currentWeekStartDateFormatted = $currentWeekStartDate->format('Y-m-d');
                $currentWeekEndDateFormatted = $currentWeekEndDate->format('Y-m-d');
                foreach ($class['classTime'] as $classTime) {
                    $classTime['currentWeek'] = $currentWeek;
                    $classTime['currentWeekStartDate'] = $currentWeekStartDateFormatted;
                    $classTime['currentWeekEndDate'] = $currentWeekEndDateFormatted;
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successClassRequest($classesData);
    }

    public function getDetailsOfClass($classId)
    {
        try {
            $class = Classes::join('teachers', 'classes.onlineTeacher', '=', 'teachers.teacherId')
                ->where('classId', $classId)
                ->select(
                    'classes.classId',
                    'classes.name as className',
                    'classes.level',
                    'classes.numberOfStudent',
                    'classes.onlineTeacher',
                    'teachers.name as teacherName',
                    'classes.classStartDate',
                    'classes.classEndDate',
                    'classes.status',
                    'classes.typeOfClass',
                    'classes.initialTextbook',
                    'classes.expired',
                )
                ->first();
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
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classStartDate)) as startDate'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_times.classEndDate)) as endDate'),
                )
                ->where('classes.classId', $classId)
                // ->where('classes.typeOfClass', 'online')
                ->where('classes.expired', 0)
                ->groupBy('class_times.classTimeSlot')
                ->get();
            $classProducts = Classes::leftJoin('class_products', 'classes.classId', '=', 'class_products.classId')
                ->leftJoin('products', 'class_products.productId', '=', 'products.productId')
                ->select(
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",products.productId,products.name)) as products'),
                )
                ->where('classes.classId', $classId)
                ->groupBy('classes.classId')
                ->get();
            $classProducts = $classProducts->pluck('products')->toArray();
            $groupProduct = [];
            foreach ($classProducts as $product) {
                $groupProduct = explode(',', $product);
            }
            // $class['product'] = $groupProduct;
            $classGroupProduct = [];
            foreach ($groupProduct as $clProduct) {
                $classProduct = explode(':', $clProduct);
                if (count($classProduct) >= 2) {
                    $classGroupProduct[] = [
                        'value' => $classProduct[0],
                        'label' => $classProduct[1],
                    ];
                }
            }
            // return $classGroupProduct;
            $class['products'] = $classGroupProduct;

            $classHolidays = Classes::leftJoin('class_holidays', 'classes.classId', '=', 'class_holidays.classId')
                ->leftJoin('holidays', 'class_holidays.holidayId', '=', 'holidays.holidayId')
                ->select(
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",holidays.holidayId, holidays.name)) as holidays'),
                )
                ->where('classes.classId', $classId)
                ->groupBy('classes.classId')
                ->get();
            $classHolidays = $classHolidays->pluck('holidays')->toArray();
            $holiday = [];
            foreach ($classHolidays as $classHoliday) {
                $clHoliday = explode(':', $classHoliday);
                if (count($clHoliday) >= 2) {
                    $holiday[] = [
                        'value' => $clHoliday[0],
                        'label' => $clHoliday[1],
                    ];
                }
            }
            $class['holiday'] = $holiday;
            $currentDate = date('Y-m-d');
            // Lấy ngày bắt đầu và ngày kết thúc của lớp học
            $startDate = $class['classStartDate'];
            $endDate = $class['classEndDate'];

            // Chuyển đổi ngày thành đối tượng DateTime
            $startDateTime = new DateTime($startDate);
            $endDateTime = new DateTime($endDate);
            $currentDateTime = new DateTime($currentDate);
            // Kiểm tra nếu ngày hiện tại nằm trong khoảng ngày bắt đầu và kết thúc
            if ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime) {
                // Tính toán số tuần từ ngày bắt đầu đến ngày hiện tại
                $diff = $startDateTime->diff($currentDateTime);
                $currentWeek = ceil($diff->days / 7);

                // Sử dụng biến $currentWeek ở đây để làm gì đó
                // Ví dụ: in ra tuần hiện tại của lớp học
            }
            // Tính toán ngày bắt đầu và ngày kết thúc của tuần hiện tại
            $currentWeekStartDate = clone $currentDateTime;
            $currentWeekStartDate->modify('monday this week');
            $currentWeekEndDate = clone $currentDateTime;
            $currentWeekEndDate->modify('sunday this week');

            // Chuyển đổi thành định dạng mong muốn (Y-m-d)
            $currentWeekStartDateFormatted = $currentWeekStartDate->format('Y-m-d');
            $currentWeekEndDateFormatted = $currentWeekEndDate->format('Y-m-d');
            foreach ($class['classTime'] as $classTime) {
                $classTime['currentWeek'] = $currentWeek;
                $classTime['currentWeekStartDate'] = $currentWeekStartDateFormatted;
                $classTime['currentWeekEndDate'] = $currentWeekEndDateFormatted;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successClassRequest($class);
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
            'holidayIds' => 'array',
            'availableNumStudent' => 'integer|required',
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
        if(!empty($this->request['availableNumStudent'])){
            if($this->request['availableNumStudent'] > $this->request['numberOfStudent']){
                return $this->errorBadRequest('Available number of student must be less than or equal to number of student');
            }
            $params['availableNumStudent'] = $this->request['availableNumStudent'];
        }

        $productNumber = count($this->request['productIds']);
        if (!empty($this->request['duration'])) {
            $params['duration'] = $this->request['duration'];
        }

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
        $newClass = new ClassResource(Classes::create($params));

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
            'holidayIds' => 'array',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try {
            $class = Classes::where('classId', $classId)->first();
            if (!empty($this->request['name'])) {
                $params['name'] = $this->request['name'];
            }
            if (!empty($this->request['level'])) {
                $params['level'] = $this->request['level'];
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
            if (!empty($this->request['expired'])) {
                $params['expired'] = $this->request['expired'];
            }
            // if (!empty($this->request['holidayIds'])) {
            //     $holidayIds = $this->request['holidayIds'];
            //     $holidayParams = [
            //         'classId' => $classId,
            //         'holidayIds' => $holidayIds,
            //     ];
            //     ClassHolidayController::update($holidayParams);
            //     $holidays = Holidays::whereIn('holidayId', $holidayIds)->get();
            //     $classTimed = ClassTimes::where('classId', $classId)->get();
            // foreach($classTimed as $time){
            //     $classTimeDays[] = $time->day;
            // }
            // // dd(end($days));
            // $holidayDays = [];
            // foreach($holidays as $holiday){
            //     $startDate = new DateTime($holiday->startDate);
            //     $endDate = new DateTime($holiday->endDate);
            //     while($startDate<=$endDate){
            //         $day = strtoupper($startDate->format('D'));
            //         $holidayDays[] = $day;
            //         $startDate->add(new DateInterval('P1D')); // add this to increment by 1 day
            //     }
            //     foreach($holidayDays as $holidayDay){
            //         if(in_array($holidayDay, $classTimeDays)){
            //             // dd(1);
            //             $lastClassDay = end($days);

            //             if($holiday->endDate > $lastClassDay){
            //                 $lastClassDay = $holiday->endDate;
            //             }
            //         }
            //     }
            //     // return $days;
            //     $holidayDates[] = $holiday->startDate. " - " .$holiday->endDate;

            // }
            // }
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
        } catch (Exception $e) {
            return $e->getMessage();
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

    public function updateTeacherTeachingClass()
    {
        $validator = Validator::make($this->request->all(), [
            'classIds' => 'array|required',
            'teacherId' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try {
            $classIds = $this->request['classIds'];
            $teacherId = $this->request['teacherId'];
            $classOfTeacher = Classes::where('onlineTeacher', $teacherId)->update(['onlineTeacher' => null]);
            foreach ($classIds as $classId) {
                $class = Classes::where('classId', $classId)->update(['onlineTeacher' => $teacherId]);
            }
            
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successClassRequest();
    }
}
