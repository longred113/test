<?php

namespace App\Http\Controllers\api\Campus_Dashboard;

use App\Http\Controllers\api\Admin_Dashboard\StudentMatchedActivityController;
use App\Http\Controllers\api\Admin_Dashboard\StudentProductController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Students;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Resources\Student as StudentsResource;
use App\Http\Resources\StudentResource;
use App\Models\Classes;
use App\Models\ClassFeedbacks;
use App\Models\ClassReports;
use App\Models\GroupActivities;
use App\Models\ProductGroups;
use App\Models\StudentProducts;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;

class OffStudentController extends Controller
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
        $data = Students::join('campuses', 'students.campusId', '=', 'campuses.campusId')
            ->select(
                'students.studentId',
                'students.campusId',
                'campuses.name as campusName',
                'students.name',
                'students.email',
                'students.gender',
                'students.dateOfBirth',
            )->where('type', 'off')->get();
        return $this->successStudentRequest($data);
    }

    public function showStudentWithCampus($campusId)
    {
        $studentData = Students::where('campusId', $campusId)->where('type', 'off')->get();
        $getStData = StudentResource::collection($studentData);
        return $this->successStudentRequest($getStData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = validator::make($request->all(), [
            'name' => 'required',
            // 'talkSamId' => 'required',
            'email' => 'required|unique:students',
            'productIds' => 'array|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $studentId = IdGenerator::generate(['table' => 'students', 'trow' => 'studentId', 'length' => 7, 'prefix' => 'ST']);
        $params = [
            'studentId' => $studentId,
            'name' => request('name'),
            'enrollmentCount' => 0,
            'email' => request('email'),
            'gender' => request('gender'),
            'dateOfBirth' => request('dateOfBirth'),
            'country' => request('country'),
            'timeZone' => request('timeZone'),
            'joinedDate' => request('joinedDate'),
            'withDrawal' => request('withDrawal'),
            'introduction' => request('introduction'),
            'talkSamId' => request('talkSamId'),
            'basicPoint' => request('basicPoint'),
            'campusId' => request('campusId'),
            'type' => request('type'),
        ];
        $newStudents = new StudentsResource(Students::create($params));

        $productIds = $request->productIds;
        $studentProductParams = [
            'studentId' => $studentId,
            'productIds' => $productIds,
        ];
        StudentProductController::createStudentProductByAdmin($studentProductParams);

        $productGroups = ProductGroups::whereIn('productId', $productIds)->get();
        $groupActivity = [];
        if (!empty($productGroups)) {
            foreach ($productGroups as $productGroup) {
                $groupId = $productGroup->groupId;
                $groupActivity = GroupActivities::where('groupId', $groupId)->select('matchedActivityId', 'matchedActivityName')->get();
                foreach ($groupActivity as $activity) {
                    $studentMatchedActivityParams = [
                        'studentId' => $studentId,
                        'matchedActivityId' => $activity->matchedActivityId,
                        'matchedActivityName' => $activity->matchedActivityName,
                        'status' => 'to-do',
                    ];
                    StudentMatchedActivityController::createStudentMatchedActivityByAdmin($studentMatchedActivityParams);
                }
            }
        }
        return $this->successStudentRequest($newStudents);
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
                ->where('students.studentId', $studentId)
                ->where('students.type', 'off')
                ->first();
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
                ->select(
                    'classes.classId',
                    'classes.name as className',
                    'classes.level',
                    'classes.numberOfStudent',
                    'classes.onlineTeacher',
                    'classes.classStartDate',
                    'classes.classEndDate',
                    'classes.status',
                    'classes.typeOfClass',
                    'classes.initialTextbook'
                )
                ->where('studentId', $studentId)
                ->get();
            if($classes->isEmpty()){
                $student->classes = $classes;
            }else {
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
                    $classGroupActivities = GroupActivities::whereIn('groupId', $classGroupId)->select('matchedActivityId', 'matchedActivityName')->get();
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
                }
                $student->classes = $classes;
                $classFeedback = ClassFeedbacks::where('classId', $classId)->where('studentId', $studentId)->get();
                $class->classFeedback = $classFeedback;
                $classReport = ClassReports::where('classId', $classId)->where('studentId', $studentId)->get();
                $class->classReport = $classReport;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successStudentRequest($student);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $studentId)
    {
        $students = Students::find($studentId);
        if (empty($request->name)) {
            $request['name'] = $students['name'];
        }
        if (empty($request->email)) {
            $request['email'] = $students['email'];
        }
        if (empty($request->enrollmentId)) {
            $request['enrollmentId'] = $students['enrollmentId'];
        }
        if (empty($request->gender)) {
            $request['gender'] = $students['gender'];
        }
        if (empty($request->dateOfBirth)) {
            $request['dateOfBirth'] = $students['dateOfBirth'];
        }
        if (empty($request->country)) {
            $request['country'] = $students['country'];
        }
        if (empty($request->timeZone)) {
            $request['timeZone'] = $students['timeZone'];
        }
        if (empty($request->joinedDate)) {
            $request['joinedDate'] = $students['joinedDate'];
        }
        if (empty($request->withDrawal)) {
            $request['withDrawal'] = $students['withDrawal'];
        }
        if (empty($request->introduction)) {
            $request['introduction'] = $students['introduction'];
        }
        if (empty($request->talkSamId)) {
            $request['talkSamId'] = $students['talkSamId'];
        }
        if (empty($request->basicPoint)) {
            $request['basicPoint'] = $students['basicPoint'];
        }
        if (empty($request->campusId)) {
            $request['campusId'] = $students['campusId'];
        }
        if (empty($request->type)) {
            $request['type'] = $students['type'];
        }

        $validator = validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        if ($request['email'] != $students['email']) {
            $email = Students::where('email', $request['email'])->first();
            if (!empty($email)) {
                return $this->errorBadRequest('Email already exists');
            }
        }
        $params = [
            $students['studentId'] = $request['studentId'],
            $students['name'] = $request['name'],
            $students['email'] = $request['email'],
            $students['enrollmentId'] = $request['enrollmentId'],
            $students['gender'] = $request['gender'],
            $students['dateOfBirth'] = $request['dateOfBirth'],
            $students['country'] = $request['country'],
            $students['timeZone'] = $request['timeZone'],
            $students['joinedDate'] = $request['joinedDate'],
            $students['withDrawal'] = $request['withDrawal'],
            $students['introduction'] = $request['introduction'],
            $students['talkSamId'] = $request['talkSamId'],
            $students['basicPoint'] = $request['basicPoint'],
            $students['campusId'] = $request['campusId'],
            $students['type'] = $request['type'],
        ];
        $newInfoStudents = $students->update($params);
        return $this->successStudentRequest($newInfoStudents);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($studentId)
    {
        $student = Students::find($studentId);
        $deleteStudents = $student->delete();
        return $this->successStudentRequest($deleteStudents);
    }

    public function GetAvailableOffStudent()
    {
        $validator = validator::make($this->request->all(), [
            'studentIds' => 'required|array',
            'timezone' => 'string|required',
            'classTime' => 'array|required',
            'productIds' => 'array|required',
            'campusId' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try{
            $studentsData = Students::leftJoin('student_classes', 'students.studentId', '=', 'student_classes.studentId')
            ->leftJoin('classes', 'student_classes.classId', '=', 'classes.classId')
            ->leftJoin('class_times', 'classes.classId', '=', 'class_times.classId')
            ->leftJoin('class_products', 'classes.classId', '=', 'class_products.classId')
            ->select(
                'students.studentId',
                'students.name',
                'students.campusId',
                'students.email',
                'students.gender',
                'students.dateOfBirth',
                'students.timeZone',
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classId, classes.name)) as classes'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",class_products.productId)) as classProducts'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",classes.classday)) as classDay'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS("-",class_times.day,class_times.classTimeSlot)) as classTime'),
            )
            ->whereIn('students.studentId', $this->request['studentIds'])
            ->where('students.timeZone', $this->request['timezone'])
            ->where('students.type', 'off')
            ->where('students.campusId', $this->request['campusId'])
            ->groupBy('students.studentId')
            ->get();
            if ($studentsData->isEmpty()) {
                return $this->successStudentRequest($studentsData);
            } else {
                $classTimes = $this->request['classTime'];
                foreach ($classTimes as $classTime) {
                    $classTimeSlot = $classTime['classTimeSlot'];
                    $days = $classTime['day'];

                    foreach ($days as $day) {
                        $formatted = $day . "-" . $classTimeSlot;
                        $classTimeResults[] = $formatted;
                    }
                }

                $filteredTeachersData = collect([]);

                $productIds = $this->request['productIds'];
                foreach ($studentsData as $student) {
                    $classProducts = explode(',', $student->classProducts);
                    $classTime = explode(',', $student->classTime);

                    $shouldExclude = false;
                    foreach ($classProducts as $product) {
                        if (in_array($product, $productIds)) {
                            $shouldExclude = true;
                        }
                    }

                    foreach ($classTime as $time) {
                        if (in_array($time, $classTimeResults)) {
                            $shouldExclude = true;
                        }
                    }

                    if (!$shouldExclude) {
                        $filteredTeachersData->push($student);
                    }
                    $studentDataOutput = $filteredTeachersData;
                }
            }
        }catch (Exception $e){
            return $e->getMessage();
        }

        return $this->successStudentRequest($studentDataOutput);
    }
}
