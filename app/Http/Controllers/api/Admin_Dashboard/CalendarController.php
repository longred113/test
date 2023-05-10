<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\StudentClasses;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
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
    public function index()
    {
        //
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
                    'classes.classId',
                    'classes.name',
                    'classes.classStartDate',
                    'class_times.classEndDate',
                    'class_times.classTimeSlot',
                    'class_times.day',
                    'classes.onlineTeacher',
                    'teachers.name as teacherName',
                    'class_time_slots.classStart as startTime',
                    'class_time_slots.classEnd as endTime'
                )
                ->where('classes.expired', 0)
                ->get();
            $classTime = [
                // 'MON' => [],
                // 'TUE' => [],
                // 'WED' => [],
                // 'THU' => [],
                // 'FRI' => [],
                // 'SAT' => [],
                // 'SUN' => [],
            ];
            foreach ($classData as $time) {
                $timeSlot = $time['classTimeSlot'];
                $className = $time['name'];
                $mappedData = [
                    'classId' => $time['classId'],
                    'className' => $className,
                    'timeSlot' => $timeSlot,
                    'teacherId ' => $time['onlineTeacher'],
                    'teacherName' => $time['teacherName'],
                    'startTime' => $time['startTime'],
                    'endTime' => $time['endTime'],
                ];
                
                $day = $time['day'];
                if ($day == 'MON') {
                    $classTime[$day][] = $mappedData;
                }
                if ($day == 'TUE') {
                    $classTime[$day][] = $mappedData;
                }
                if ($day == 'WED') {
                    $classTime[$day][] = $mappedData;
                }
                if ($day == 'THU') {
                    $classTime[$day][] = $mappedData;
                }
                if ($day == 'FRI') {
                    $classTime[$day][] = $mappedData;
                }
                if ($day == 'SAT') {
                    $classTime[$day][] = $mappedData;
                }
                if ($day == 'SUN') {
                    $classTime[$day][] = $mappedData;
                }
                // $classTime[][] = $mappedData; // Gán thời gian lớp học cho ngày tương ứng
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successClassTimeLineRequest($classTime);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
