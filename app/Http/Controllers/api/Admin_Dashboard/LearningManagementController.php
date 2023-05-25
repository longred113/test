<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Classes;
use App\Models\ClassFeedbacks;
use App\Models\ClassReports;
use App\Models\GroupActivities;
use App\Models\ProductGroups;
use App\Models\ProductMatchedActivities;
use App\Models\StudentClasses;
use App\Models\StudentMatchedActivities;
use App\Models\StudentProducts;
use App\Models\Students;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $learningData = Students::leftJoin('campuses', 'students.campusId', '=', 'campuses.campusId')
                ->leftJoin('student_classes', 'students.studentId', '=', 'student_classes.studentId')
                ->leftJoin('classes', 'student_classes.classId', '=', 'classes.classId')
                ->select(
                    'students.studentId',
                    'students.name',
                    'students.campusId',
                    'campuses.name as campusName',
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.level)) as levels'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classId,classes.name)) as classes'),
                    'students.joinedDate',
                )
                ->groupBy('students.studentId')
                ->get();
            // return $learningData;
            foreach ($learningData as $value) {
                // $value->levels = explode(',', $value->levels);
                $value->classes = explode(',', $value->classes);
                $classLists = [];
                foreach ($value->classes as $class) {
                    $classes = explode(':', $class);
                    if (isset($classes[1])) {
                        $classList = [
                            'classId' => $classes[0],
                            'className' => $classes[1],
                        ];
                        $classLists[] = $classList;
                    }
                }
                $value->classes = $classLists;
            }
        } catch (Exception $e) {
            return ($e->getMessage());
        }

        return $this->successLearningManagementRequest($learningData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($studentId)
    {
        try {
            $class = Classes::join('student_classes', 'classes.classId', '=', 'student_classes.classId')
                ->join('student_products', 'student_classes.studentId', '=', 'student_products.studentId')
                ->join('product_groups', 'student_products.productId', '=', 'product_groups.productId')
                ->select(
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classStartDate)) as classStartDate'),
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classEndDate)) as classEndDate'),
                    'product_groups.productId',
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",product_groups.groupId,product_groups.groupName)) as `groups`'),
                )
                ->where('student_classes.studentId', $studentId)
                ->distinct()
                ->groupBy('product_groups.productId')
                ->get();

            $modifiedResults = [];

            foreach ($class as $item) {
                $startDate = $item->classStartDate;
                $endDate = $item->classEndDate;
                $productId = $item->productId;
                $groups = explode(',', $item->groups);
                $numOfWeeks = ceil(strtotime($endDate) - strtotime($startDate)) / (7 * 24 * 60 * 60);

                // Assign groups to each week
                $weekGroups = [];
                $currentDate = $startDate;
                for ($week = 1; $week <= $numOfWeeks; $week++) {
                    $groupIndex = ($week - 1) % count($groups);
                    $weekStartDate = $currentDate;
                    $weekEndDate = date('Y-m-d', strtotime($currentDate . ' + 6 days'));
                    $weekGroups[$week] = [
                        'group' => $groups[$groupIndex],
                        'startDate' => $weekStartDate,
                        'endDate' => $weekEndDate
                    ];
                    $currentDate = date('Y-m-d', strtotime($weekEndDate . ' + 1 days'));
                }

                // Add modified item to the result array
                $modifiedResults[] = [
                    'classStartDate' => $startDate,
                    'classEndDate' => $endDate,
                    'productId' => $productId,
                    'weekGroups' => $weekGroups
                ];
            }

            // Convert the modified results to JSON
            // $jsonOutput = json_encode($modifiedResults);

            // Print the JSON output
            return $modifiedResults;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $class;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($studentId)
    {
        try {
            $student = Students::leftJoin('users', 'students.studentId', '=', 'users.studentId')
                ->leftJoin('campuses', 'students.campusId', '=', 'campuses.campusId')
                ->select(
                    'students.studentId',
                    'students.name as studentName',
                    'students.campusId',
                    'campuses.name as campusName',
                    'students.email',
                    'students.gender',
                    'students.dateOfBirth',
                    'students.type',
                    'students.country',
                    'students.timeZone',
                    'users.userName',
                    'users.password',
                )
                ->where('students.type', 'online')
                ->where('students.studentId', $studentId)
                ->first();
            $student->campusName = Campus::where('campusId', $student->campusId)->first()->name;
            $products = StudentProducts::join('products', 'student_products.productId', '=', 'products.productId')
                ->selectRaw(
                    'GROUP_CONCAT(DISTINCT CONCAT_WS(":",products.productId, products.name))as products',
                )
                ->where('studentId', $studentId)
                ->pluck('products')
                ->toArray();
            foreach ($products as $product) {
                $eachProducts = explode(',', $product);
            }
            $studentProducts = [];
            foreach ($eachProducts as $eachProduct) {
                $productParts = explode(':', $eachProduct);
                if (count($productParts) >= 2) {
                    $studentProducts[] = [
                        'productId' => $productParts[0],
                        'productName' => $productParts[1],
                    ];
                }
            }
            $productGroups = ProductGroups::where('productId', $productParts[0])->select('groupId', 'groupName')->get();
            $groupId = $productGroups->pluck('groupId')->toArray();
            $groupActivities = GroupActivities::whereIn('groupId', $groupId)->select('matchedActivityId', 'matchedActivityName')->get();
            $student->products = $studentProducts;
            $student->productGroups = $productGroups;
            $student->groupActivities = $groupActivities;
            $classes = Classes::join('student_classes', 'classes.classId', '=', 'student_classes.classId')
                ->join('teachers', 'classes.onlineTeacher', '=', 'teachers.teacherId')
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
                    'classes.initialTextbook'
                )
                ->where('studentId', $studentId)
                ->get();
            $currentDate = date('Y-m-d');
            foreach ($classes as $class) {
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
                            'productId' => $classProduct[0],
                            'productName' => $classProduct[1],
                        ];
                    }
                }
                $classProductGroup = ProductGroups::where('productId', $classProduct[0])->select('groupId', 'groupName')->get();
                $classGroupId = $classProductGroup->pluck('groupId')->toArray();
                $classGroupActivities = GroupActivities::join('student_matched_activities', 'group_activities.matchedActivityId', '=', 'student_matched_activities.matchedActivityId')
                    ->join('matched_activities', 'group_activities.matchedActivityId', '=', 'matched_activities.matchedActivityId')
                    ->whereIn('groupId', $classGroupId)
                    ->where('student_matched_activities.status', 'incomplete')
                    ->select(
                        'student_matched_activities.matchedActivityId',
                        'student_matched_activities.name',
                        'student_matched_activities.status',
                        'matched_activities.type',
                        'matched_activities.time',
                    )
                    ->distinct()
                    ->get();
                // return $classGroupProduct;
                $class['products'] = $classGroupProduct;
                $class['productGroups'] = $classProductGroup;
                $class['groupActivities'] = $classGroupActivities;

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
            $student->classes = $classes;
            $classFeedback = ClassFeedbacks::where('classId', $classId)->where('studentId', $studentId)->get();
            $class->classFeedback = $classFeedback;
            $classReport = ClassReports::where('classId', $classId)->where('studentId', $studentId)->get();
            $class->classReport = $classReport;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successLearningManagementRequest($student);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $class = Classes::all()->update([
            'category' => 'online',
        ]);
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
