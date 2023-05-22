<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\StudentClasses;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CalendarController extends Controller
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
    public function getTimeZone()
    {
        $timezones = timezone_identifiers_list();
        $selectedTimezones = array(
            'Asia/Ho_Chi_Minh',
            'Asia/Seoul',
            'Asia/Tokyo',
            'Asia/Manila',
            'America/Toronto'
        );
        foreach ($selectedTimezones as $timezone) {
            $timezoneObj = new DateTimeZone($timezone);
            $currentTime = new DateTime('now', $timezoneObj);

            $offset = $timezoneObj->getOffset($currentTime) / 3600; // Chuyển đổi sang đơn vị giờ

            $timezoneFormatted = 'GMT';
            if ($offset >= 0) {
                $timezoneFormatted .= '+';
            } else {
                $timezoneFormatted .= '-';
                $offset *= -1;
            }
            $timezoneFormatted .= $offset;

            $result[] =  $timezoneFormatted . ' ' . $timezone;
        }
        return ($result);
    }

    public function getClassByStudent($studentId)
    {
        $studentClassesData = StudentClasses::join('classes', 'student_classes.classId', '=', 'classes.classId')
            ->select(
                'classes.classId',
                'classes.name',
            )
            ->where('studentId', $studentId)
            ->distinct()
            ->get();
        return $this->successStudentClassRequest($studentClassesData);
    }

    public function classCalendarTimeLine()
    {
        $validator = Validator::make($this->request->all(), [
            'classStartDate' => 'Date|required',
            'classTime' => 'array|required',
            'products' => 'array|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $classStartDate = $this->request['classStartDate'];
        $classTime = $this->request['classTime'];
        $products = $this->request['products'];
        $productLength = count($this->request['products']);
        $classEndDate = date('Y-m-d', strtotime($classStartDate . ' + ' . $productLength * 2 . ' months'));

        $startDate = new DateTime($classStartDate);
        $endDate = new DateTime($classEndDate);

        $interval = $startDate->diff($endDate);
        $numberOfWeeks = floor($interval->days / 7);

        $classtime = [
            'MON' => null,
            'TUE' => null,
            'WED' => null,
            'THU' => null,
            'FRI' => null,
            'SAT' => null,
            'SUN' => null,
        ];

        // Xử lý thông tin về $classtime
        foreach ($classTime as $time) {
            $timeSlot = explode("-", $time); // Chuyển mảng thời gian lớp học thành chuỗi
            $day = substr($time, 0, 3); // Lấy 3 ký tự đầu tiên (ngày)

            $classtime[$day] = $timeSlot[1]; // Gán thời gian lớp học cho ngày tương ứng
        }

        $weeks = [];

        for ($i = 0; $i < $numberOfWeeks; $i++) {
            $startOfWeek = clone $startDate;
            $endOfWeek = clone $startDate->add(new DateInterval('P6D'));

            $weekRange = [
                'start' => $startOfWeek->format('Y-m-d'),
                'end' => $endOfWeek->format('Y-m-d'),
                'products' => array_slice($products, $i * 7, 7), // Sử dụng array_slice để chia nhỏ mảng sản phẩm thành các tuần
                'classtime' => $classtime,
            ];

            $weeks[] = $weekRange;
        }

        foreach ($weeks as $week) {
            foreach ($week['classtime'] as $day => $timeLine) {
                if ($timeLine) {
                    $timeClass = $day . ": " . $timeLine;
                    $week['classtime'] = $timeClass;
                }
            }
        }

        $output = '';

        foreach ($weeks as $week) {
            $output .= "Tuần bắt đầu từ: " . $week['start'] . " đến: " . $week['end'] . "<br>";
            $output .= "Sản phẩm trong tuần: " . implode(", ", $week['products']) . "<br>";

            $output .= "Thời gian lớp học:<br>";
            foreach ($week['classtime'] as $day => $time) {
                if ($time) {
                    $output .= "Ngày " . $day . ": " . $time . "<br>";
                }
            }

            $output .= "<br>";
        }
        return ($weeks);
        //write code to count how many weeks in classStartDate and classEndDate
    }

    public function ClassTimeLineByDate()
    {
        // $validator = Validator::make($this->request->all(), [
        //     'classStartDate' => 'Date|required',
        // ]);
        // if ($validator->fails()) {
        //     return $this->errorBadRequest($validator->getMessageBag()->toArray());
        // }
        // $classStartDate = $this->request['classStartDate'];
        try {
            $classData = Classes::join('class_times', 'classes.classId', '=', 'class_times.classId')
                ->join('teachers', 'classes.onlineTeacher', '=', 'teachers.teacherId')
                ->leftJoin('class_time_slots', 'class_times.classTimeSlot', '=', 'class_time_slots.name')
                ->select(
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classId,classes.name,class_times.day,teachers.name)) as Class'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_times.day)) as day'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.onlineTeacher,teachers.name)) as teacher'),
                    // DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS("->",classes.classStartDate, class_times.classEndDate)) as Date'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_time_slots.classStart)) as startTime'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_time_slots.classEnd)) as endTime'),
                    // DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS("-",class_times.classTimeSlot)) as classTimeSlot'),

                    // 'classes.classStartDate',
                    // 'class_times.classEndDate',
                    'class_times.classTimeSlot',
                    // 'class_times.day',
                    // 'classes.onlineTeacher',
                    // 'teachers.name as teacherName',
                    // 'class_time_slots.classStart as startTime',
                    // 'class_time_slots.classEnd as endTime'
                )
                ->where('classes.expired', 0)
                ->groupBy('class_times.classTimeSlot')
                ->get();
            // return $classData;
            // $classTime = [
            //     'MON' => [],
            //     'TUE' => [],
            //     'WED' => [],
            //     'THU' => [],
            //     'FRI' => [],
            //     'SAT' => [],
            //     'SUN' => [],
            // ];
            // foreach ($classData as $time) {
            //     $timeSlot = $time['classTimeSlot'];
            //     $className = $time['name'];
            //     $mappedData = [
            //         'classId' => $time['classId'],
            //         'className' => $className,
            //         'timeSlot' => $timeSlot,
            //         'teacherId ' => $time['onlineTeacher'],
            //         'teacherName' => $time['teacherName'],
            //         'startTime' => $time['startTime'],
            //         'endTime' => $time['endTime'],
            //     ];

            //     $day = $time['day'];
            //     if ($day == 'MON') {
            //         $classTime[$day][] = $mappedData;
            //     }
            //     if ($day == 'TUE') {
            //         $classTime[$day][] = $mappedData;
            //     }
            //     if ($day == 'WED') {
            //         $classTime[$day][] = $mappedData;
            //     }
            //     if ($day == 'THU') {
            //         $classTime[$day][] = $mappedData;
            //     }
            //     if ($day == 'FRI') {
            //         $classTime[$day][] = $mappedData;
            //     }
            //     if ($day == 'SAT') {
            //         $classTime[$day][] = $mappedData;
            //     }
            //     if ($day == 'SUN') {
            //         $classTime[$day][] = $mappedData;
            //     }
            //     $classTime[$day][] = $mappedData; // Gán thời gian lớp học cho ngày tương ứng
            // }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successClassTimeLineRequest($classData);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCalendarOfStudent($studentId)
    {
        try {
            $studentClassesData = StudentClasses::join('classes', 'student_classes.classId', '=', 'classes.classId')
                ->join('class_times', 'classes.classId', '=', 'class_times.classId')
                ->join('teachers', 'classes.onlineTeacher', '=', 'teachers.teacherId')
                ->leftJoin('class_time_slots', 'class_times.classTimeSlot', '=', 'class_time_slots.name')
                ->select(
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classId,classes.name,class_times.day,teachers.name)) as Class'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_times.day)) as day'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.onlineTeacher,teachers.name)) as teacher'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_time_slots.classStart)) as startTime'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_time_slots.classEnd)) as endTime'),
                    'class_times.classTimeSlot',
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",student_classes.studentId)) as student'),
                )
                ->where('studentId', $studentId)
                ->where('classes.expired', 0)
                ->groupBy('class_times.classTimeSlot')
                ->get();
        } catch (Exception $e) {
            return ($e->getMessage());
        }

        return $this->successClassTimeLineRequest($studentClassesData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCalendarOfClass()
    {
        $validator = Validator::make($this->request->all(), [
            'classIds' => 'array|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $classIds = $this->request['classIds'];
        $classData = Classes::join('class_times', 'classes.classId', '=', 'class_times.classId')
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
            ->whereIn('classes.classId', $classIds)
            // ->where('classes.typeOfClass', 'online')
            ->where('classes.expired', 0)
            ->groupBy('class_times.classTimeSlot')
            ->get();
        return $this->successClassTimeLineRequest($classData);
    }

    public function getCalendarOfTeacher($teacherId)
    {
        $classData = Classes::join('class_times', 'classes.classId', '=', 'class_times.classId')
            ->join('teachers', 'classes.onlineTeacher', '=', 'teachers.teacherId')
            ->leftJoin('class_time_slots', 'class_times.classTimeSlot', '=', 'class_time_slots.name')
            ->select(
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classId,classes.name,class_times.day,teachers.name)) as Class'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classId)) as classId'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_times.day)) as day'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.onlineTeacher,teachers.name)) as teacher'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_time_slots.classStart)) as startTime'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_time_slots.classEnd)) as endTime'),
                'class_times.classTimeSlot',
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classStartDate, class_times.classEndDate)) as Date'),
            )
            ->where('classes.onlineTeacher', $teacherId)
            // ->where('classes.typeOfClass', 'online')
            ->where('classes.expired', 0)
            ->groupBy('class_times.classTimeSlot')
            ->get();
        foreach ($classData as $class) {
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
        return $this->successClassTimeLineRequest($classData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
