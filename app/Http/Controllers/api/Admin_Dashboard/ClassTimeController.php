<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\ClassTimes;
use DateTime;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        try {
            foreach ($classTimes as $classTime) {
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
        } catch (Exception $e) {
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

    public function getByProduct()
    {
        $validator = Validator::make($this->request->all(), [
            'productIds' => 'array|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try {
            $productIds = $this->request->productIds;
            $classTime = Classes::join('class_times', 'classes.classId', '=', 'class_times.classId')
                ->join('teachers', 'classes.onlineTeacher', '=', 'teachers.teacherId')
                ->leftJoin('class_time_slots', 'class_times.classTimeSlot', '=', 'class_time_slots.name')
                ->join('class_products', 'classes.classId', '=', 'class_products.classId')
                ->select(
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classId,classes.name,class_times.day,teachers.name)) as Class'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classId)) as classId'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_times.day)) as day'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.onlineTeacher,teachers.name)) as teacher'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_time_slots.classStart)) as startTime'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_time_slots.classEnd)) as endTime'),
                    'class_times.classTimeSlot',
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classStartDate)) as startDate'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_times.classEndDate)) as endDate'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_products.productId)) as productId'),
                )
                ->whereIn('class_products.productId', $productIds)
                // ->where('classes.typeOfClass', 'online')
                ->where('classes.expired', 0)
                ->groupBy('class_times.classTimeSlot')
                ->get();

                foreach ($classTime as $class) {
                    $classId = $class['classId'];
                    $classId = explode(',', $classId);
                    $classes = Classes::whereIn('classId', $classId)->get();
                    $week = [];
                    foreach ($classes as $oneClass) {
                        $classStartDate = $oneClass['classStartDate'];
                        $classEndDate = $oneClass['classEndDate'];
                        $currentDate = date('Y-m-d');
                        $startDateTime = new DateTime($classStartDate);
                        $endDateTime = new DateTime($classEndDate);
                        $currentDateTime = new DateTime($currentDate);
                        // Kiểm tra nếu ngày hiện tại nằm trong khoảng ngày bắt đầu và kết thúc
                        if ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime) {
                            // Tính toán số tuần từ ngày bắt đầu đến ngày hiện tại
                            $diff = $startDateTime->diff($currentDateTime);
                            $currentWeek = ceil($diff->days / 7);
        
                            // Sử dụng biến $currentWeek ở đây để làm gì đó
                            // Ví dụ: in ra tuần hiện tại của lớp học
                            // Tính toán ngày bắt đầu và ngày kết thúc của tuần hiện tại
                            $currentWeekStartDate = clone $currentDateTime;
                            $currentWeekStartDate->modify('monday this week');
                            $currentWeekEndDate = clone $currentDateTime;
                            $currentWeekEndDate->modify('sunday this week');
        
                            // Chuyển đổi thành định dạng mong muốn (Y-m-d)
                            $currentWeekStartDateFormatted = $currentWeekStartDate->format('Y-m-d');
                            $currentWeekEndDateFormatted = $currentWeekEndDate->format('Y-m-d');
                            // Tính toán số tuần còn lại từ ngày hiện tại đến ngày kết thúc của lớp học
                            $diffRemaining = $currentDateTime->diff($endDateTime);
                            $remainingWeeks = ceil($diffRemaining->days / 7);
                            $oneClass['currentWeek'] = $currentWeek;
                            $oneClass['currentWeekStartDate'] = $currentWeekStartDateFormatted;
                            $oneClass['currentWeekEndDate'] = $currentWeekEndDateFormatted;
                            $oneClass['remainingWeeks'] = $remainingWeeks;
                        }
                        $week[] = [
                            'oneClass' => $oneClass['classId'],
                            'currentWeek' => $oneClass['currentWeek'],
                            'currentWeekStartDate' => $oneClass['currentWeekStartDate'],
                            'currentWeekEndDate' => $oneClass['currentWeekEndDate'],
                            'remainingWeeks' => $oneClass['remainingWeeks'],
                        ];
                    }
                    $class['week'] = $week;
                }
        } catch (Exception $e) {
            return $e->getMessage();
        }

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
        try {
            if (!$classHaveTime->isEmpty()) {
                ClassTimes::where('classId', $classId)->delete();
                foreach ($classTimes as $classTime) {
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
            if ($classHaveTime->isEmpty()) {
                foreach ($classTimes as $classTime) {
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
        } catch (Exception $e) {
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
