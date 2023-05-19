<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Campus;
use App\Models\Classes;
use App\Models\ClassFeedbacks;
use App\Models\ClassReports;
use App\Models\GroupActivities;
use App\Models\Parents;
use App\Models\ProductGroups;
use App\Models\ProductMatchedActivities;
use App\Models\Products;
use App\Models\StudentClasses;
use App\Models\StudentMatchedActivities;
use App\Models\StudentProducts;
use App\Models\Students;
use App\Models\Users;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
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
        $studentsData = StudentResource::collection(Students::all());
        return $this->successStudentRequest($studentsData);
    }

    public function studentWithdrawalList()
    {
        $studentWithdrawal = StudentResource::collection(Students::where('type', 'online-break')->orWhere('type', 'offline-break')->get());
        return $this->successStudentRequest($studentWithdrawal);
    }

    public function studentJoinedList()
    {
        $studentId = Users::get('studentId');
        $joinedStudent = Students::join('users', 'students.studentId', '=', 'users.studentId')
            ->join('campuses', 'students.campusId', '=', 'campuses.campusId')
            ->select(
                'students.studentId',
                'students.campusId',
                'campuses.name as campusName',
                'students.name',
                'students.email',
                'users.email as userName',
                'users.password',
                'students.gender',
                'students.dateOfBirth',
            )
            ->where('students.type', 'online')->get();
        return $this->successStudentRequest($joinedStudent);
    }

    public function viewStudentProductAndStudyPlanner()
    {
        try {
            $students = Students::leftJoin('users', 'students.studentId', '=', 'users.studentId')
                ->leftJoin('campuses', 'students.campusId', '=', 'campuses.campusId')
                ->leftJoin('student_products', 'students.studentId', '=', 'student_products.studentId')
                ->leftJoin('student_matched_activities', 'students.studentId', '=', 'student_matched_activities.studentId')
                ->leftJoin('products', 'student_products.productId', '=', 'products.productId')
                ->leftJoin('matched_activities', 'student_matched_activities.matchedActivityId', '=', 'matched_activities.matchedActivityId')
                ->selectRaw(
                    'students.studentId,
                    students.campusId,
                    campuses.name as campusName,
                    students.name,
                    students.email,
                    MAX(users.userName) as userName,
                    MAX(users.password) as password,
                    students.gender,
                    students.dateOfBirth,
                    GROUP_CONCAT(DISTINCT CONCAT_WS(":",student_products.productId, products.name)) as products,
                    GROUP_CONCAT(DISTINCT CONCAT_WS(":", student_matched_activities.matchedActivityId, matched_activities.name)) as studyPlaners',
                )
                ->where('students.type', 'online')
                ->whereNotNull('users.studentId')
                ->groupBy('students.studentId', 'students.campusId')
                ->get();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successStudentRequest($students);
    }

    public function detailsOfStudent($studentId)
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
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successStudentRequest($student);
    }

    public function getStudentWithId($studentId)
    {
        $student = Students::find($studentId);
        if ($student != NULL) {
            $studentData = $student;
        } else {
            $studentData = "";
        }
        return $this->successStudentRequest($studentData);
    }

    public function getStudentByParent($parentId)
    {
        $student = Students::where('parentId', $parentId)->get();
        return $this->successStudentRequest($student);
    }

    public function getEnrollmentCount($studentId)
    {
        $student = Students::where('studentId', $studentId)->get('enrollmentCount');
        return $this->successStudentRequest($student);
    }

    public function getStudentByCampus($campusId)
    {
        $students = Students::where('campusId', $campusId)->get();
        return $this->successStudentRequest($students);
    }

    public function getStudentOnline()
    {
        $students = Students::where('type', 'online')->get();
        return $this->successStudentRequest($students);
    }

    public function getStudentForClassRegister()
    {
        $validator = Validator::make($this->request->all(), [
            'studentIds' => 'array|required',
            'timeZone' => 'string|required',
            'classTime' => 'array|required',
            'productIds' => 'array|required',
            // 'day' => 'string|required',
            // 'timeSlot' => 'string|required',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try {
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
                ->where('students.timeZone', $this->request['timeZone'])
                ->where('students.type', 'online')
                ->groupBy('students.studentId')
                ->get();

            // return $studentsData;
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
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successStudentRequest($studentDataOutput);
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
            'email' => 'string|required|unique:students',
            'password' => 'string|required|min:8',
            // 'gender' => 'string|required',
            // 'dateOfBirth' => 'date|required',
            // 'country' => 'string|required',
            // 'timeZone' => 'string|required',
            // 'status' => 'string|required',
            // 'joinedDate' => 'date|required',
            // 'withDrawal' => 'date|required',
            // 'introduction' => 'string|required',
            'talkSamId' => 'string|required',
            // 'basicPoint' => 'integer|required',
            'campusId' => 'string|required',
            'type' => [Rule::in(['online', 'offline', 'online-break', 'offline-break', 'reserve', 'wait-for-approval'])],
            'enrollmentCount' => 'integer',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $studentId = IdGenerator::generate(['table' => 'students', 'trow' => 'studentId', 'length' => 7, 'prefix' => 'ST']);
        $studentParams = [
            'studentId' => $studentId,
            'name' => $this->request['name'],
            'enrollmentCount' => $this->request['enrollmentCount'],
            'email' => $this->request['email'],
            'gender' => $this->request['gender'],
            'dateOfBirth' => $this->request['dateOfBirth'],
            'country' => $this->request['country'],
            'timeZone' => $this->request['timeZone'],
            'status' => $this->request['status'],
            'joinedDate' => $this->request['joinedDate'],
            'withDrawal' => $this->request['withDrawal'],
            'introduction' => $this->request['introduction'],
            'talkSamId' => $this->request['talkSamId'],
            'basicPoint' => $this->request['basicPoint'],
            'campusId' => $this->request['campusId'],
            'type' => $this->request['type'],
        ];

        $userParams = [
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'password' => $this->request['password'],
            'studentId' => $studentId,
        ];
        $newStudentData = new StudentResource(Students::create($studentParams));
        dd(1);
        UserController::store($userParams);
        return $this->successStudentRequest($newStudentData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function show($studentId)
    {
        $student = Students::find($studentId);
        $studentData = new StudentResource($student);
        return $this->successStudentRequest($studentData);
    }

    public function updateEnrollmentCount($studentId)
    {
        $validator = Validator::make($this->request->all(), [
            'enrollmentCount' => 'int',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $student = Students::where('studentId', $studentId)->update(['enrollmentCount' => $this->request['enrollmentCount']]);
        return $this->successStudentRequest($student);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function update($studentId)
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'name' => 'string',
                'email' => 'string',
                'userName' => 'string',
                'password' => 'string|min:8',
                // 'gender' => 'string|required',
                // 'dateOfBirth' => 'date|required',
                // 'country' => 'string|required',
                // 'timeZone' => 'string|required',
                // 'status' => 'string|required',
                // 'joinedDate' => 'date|required',
                // 'withDrawal' => 'date|required',
                // 'introduction' => 'string|required',
                // 'talkSamId' => 'string|required',
                // 'basicPoint' => 'integer|required',
                'campusId' => 'string|',
                'type' => 'string',
                // 'enrollmentId' => 'string',
                // 'parentId' => 'string',
                'enrollmentCount' => 'int',
                'productIds' => 'array',
            ]);
            if ($validator->fails()) {
                return $this->errorBadRequest($validator->getMessageBag()->toArray());
            }

            $student = Students::where('studentId', $studentId)->first();
            $user = Users::where('studentId', $studentId)->first();
            if (!empty($this->request['email'])) {
                if ($this->request['email'] != $student['email']) {
                    $email = Students::where('email', $this->request['email'])->first();
                    $params['email'] = $this->request['email'];
                    $userParams['email'] = $this->request['email'];
                    if (!empty($email)) {
                        return $this->errorBadRequest('Email already exists');
                    }
                } else {
                    $params['email'] = $this->request['email'];
                    $userParams['email'] = $this->request['email'];
                }
            }
            if (!empty($this->request['userName'])) {
                if ($this->request['userName'] != $user['userName']) {
                    $userName = Users::where('userName', $this->request['userName'])->first();
                    $userParams['userName'] = $this->request['userName'];
                    if (!empty($userName)) {
                        return $this->errorBadRequest('Username already exists');
                    }
                } else {
                    $userParams['userName'] = $this->request['userName'];
                }
            }
            if (!empty($this->request['name'])) {
                $params['name'] = $this->request['name'];
                $userParams['name'] = $this->request['name'];
            }
            if (!empty($this->request['password'])) {
                $userParams['password'] = $this->request['password'];
            }
            if (!empty($this->request['gender'])) {
                $params['gender'] = $this->request['gender'];
            }
            if (!empty($this->request['dateOfBirth'])) {
                $params['dateOfBirth'] = $this->request['dateOfBirth'];
            }
            if (!empty($this->request['country'])) {
                $params['country'] = $this->request['country'];
            }
            if (!empty($this->request['timeZone'])) {
                $params['timeZone'] = $this->request['timeZone'];
            }
            if (!empty($this->request['status'])) {
                $params['status'] = $this->request['status'];
            }
            if (!empty($this->request['joinedDate'])) {
                $params['joinedDate'] = $this->request['joinedDate'];
            }
            if (!empty($this->request['withDrawal'])) {
                $params['withDrawal'] = $this->request['withDrawal'];
            }
            if (!empty($this->request['introduction'])) {
                $params['introduction'] = $this->request['introduction'];
            }
            if (!empty($this->request['talkSamId'])) {
                $params['talkSamId'] = $this->request['talkSamId'];
            }
            if (!empty($this->request['basicPoint'])) {
                $params['basicPoint'] = $this->request['basicPoint'];
            }
            if (!empty($this->request['campusId'])) {
                $params['campusId'] = $this->request['campusId'];
            }
            if (!empty($this->request['type'])) {
                $params['type'] = $this->request['type'];
            }
            if (!empty($this->request['parentId'])) {
                $params['parentId'] = $this->request['parentId'];
            }
            if (!is_null($this->request['enrollmentCount'])) {
                $params['enrollmentCount'] = $this->request['enrollmentCount'];
            }

            $newStudentInfoData = $student->update($params);

            if (!empty($user)) {
                $userParams['studentId'] = $studentId;
                UserController::update($userParams);
            }

            if (!empty($this->request['productIds'])) {
                $productIds = $this->request['productIds'];
                $studentProductParams = [
                    'studentId' => $studentId,
                    'productIds' => $productIds,
                ];
                StudentProductController::updateStudentProductByAdmin($studentProductParams);

                $productGroups = ProductGroups::whereIn('productId', $productIds)->get();
                if (!empty($productGroups)) {
                    $studentMatchedActivity = StudentMatchedActivities::where('studentId', $studentId)->delete();
                    foreach ($productGroups as $productGroup) {
                        $groupId = $productGroup['groupId'];
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
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successStudentRequest($newStudentInfoData);
    }

    public function updateWithdrawalStudent($studentId)
    {
        $student = Students::find($studentId);
        if (empty($this->request['name'])) {
            $this->request['name'] = $student['name'];
        }
        if (empty($this->request['email'])) {
            $this->request['email'] = $student['email'];
        }
        if (empty($this->request['gender'])) {
            $this->request['gender'] = $student['gender'];
        }
        if (empty($this->request['dateOfBirth'])) {
            $this->request['dateOfBirth'] = $student['dateOfBirth'];
        }
        if (empty($this->request['country'])) {
            $this->request['country'] = $student['country'];
        }
        if (empty($this->request['timeZone'])) {
            $this->request['timeZone'] = $student['timeZone'];
        }
        if (empty($this->request['introduction'])) {
            $this->request['introduction'] = $student['introduction'];
        }
        if (empty($this->request['talkSamId'])) {
            $this->request['talkSamId'] = $student['talkSamId'];
        }
        if (empty($this->request['basicPoint'])) {
            $this->request['basicPoint'] = $student['basicPoint'];
        }
        if (empty($this->request['campusId'])) {
            $this->request['campusId'] = $student['campusId'];
        }
        if (empty($this->request['status'])) {
            $this->request['status'] = $student['status'];
        }
        if (empty($this->request['enrollmentId'])) {
            $this->request['enrollmentId'] = $student['enrollmentId'];
        }
        if (empty($this->request['parentId'])) {
            $this->request['parentId'] = $student['parentId'];
        }
        if (empty($this->request['enrollmentCount'])) {
            $this->request['enrollmentCount'] = $student['enrollmentCount'];
        }
        $validator = Validator::make($this->request->all(), [
            'name' => 'string',
            'email' => 'string',
            // 'gender' => 'string|required',
            // 'dateOfBirth' => 'date|required',
            // 'country' => 'string|required',
            // 'timeZone' => 'string|required',
            // 'status' => 'string|required',
            // 'joinedDate' => 'date|required',
            // 'withDrawal' => 'date|required',
            // 'introduction' => 'string|required',
            // 'talkSamId' => 'string|required',
            // 'basicPoint' => 'integer|required',
            'campusId' => 'string',
            'type' => 'string',
            // 'enrollmentId' => 'string',
            // 'parentId' => 'string',
            'enrollmentCount' => 'integer',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $student['name'] = $this->request['name'],
            $student['email'] = $this->request['email'],
            $student['gender'] = $this->request['gender'],
            $student['dateOfBirth'] = $this->request['dateOfBirth'],
            $student['country'] = $this->request['country'],
            $student['timeZone'] = $this->request['timeZone'],
            $student['status'] = $this->request['status'],
            $student['joinedDate'] = Carbon::now(),
            $student['withDrawal'] = "",
            $student['introduction'] = $this->request['introduction'],
            $student['talkSamId'] = $this->request['talkSamId'],
            $student['basicPoint'] = $this->request['basicPoint'],
            $student['campusId'] = $this->request['campusId'],
            $student['type'] = 'online',
            $student['enrollmentId'] = $this->request['enrollmentId'],
            $student['parentId'] = $this->request['parentId'],
            $student['enrollmentCount'] = $this->request['enrollmentCount'],
        ];
        $newStudentInfoData = $student->update($params);
        $user = Users::where('studentId', $studentId)->update(['activate' => 1]);
        return $this->successStudentRequest($newStudentInfoData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function destroy($studentId)
    {
        $student = Students::find($studentId);
        $deleteStudent = $student->delete();
        return $this->successStudentRequest($deleteStudent);
    }

    public function createStudentByAdmin()
    {
        try {

            $validator = Validator::make($this->request->all(), [
                'name' => 'string|required',
                'email' => 'string|required|unique:students',
                'userName' => 'string|required|unique:users',
                'password' => 'string|required|min:8',
                // 'gender' => 'string|required',
                // 'dateOfBirth' => 'date|required',
                // 'country' => 'string|required',
                // 'timeZone' => 'string|required',
                // 'status' => 'string|required',
                // 'joinedDate' => 'date|required',
                // 'withDrawal' => 'date|required',
                // 'introduction' => 'string|required',
                // 'talkSamId' => 'string|required',
                // 'basicPoint' => 'integer|required',
                'campusId' => 'string|required',
                'type' => [Rule::in(['online', 'offline', 'online-break', 'offline-break', 'reserve', 'wait-for-approval'])],
                'enrollmentCount' => 'integer',
                'productIds' => 'array|required',
                'classId' => 'string|required',
                // 'level' => 'string|required',
            ]);
            if ($validator->fails()) {
                return $this->errorBadRequest($validator->getMessageBag()->toArray());
            }

            $studentId = IdGenerator::generate(['table' => 'students', 'trow' => 'studentId', 'length' => 7, 'prefix' => 'ST']);

            $studentParams = [
                'studentId' => $studentId,
                'name' => $this->request['name'],
                'enrollmentCount' => $this->request['enrollmentCount'],
                'email' => $this->request['email'],
                'gender' => $this->request['gender'],
                'dateOfBirth' => $this->request['dateOfBirth'],
                'country' => $this->request['country'],
                'timeZone' => $this->request['timeZone'],
                'status' => $this->request['status'],
                'joinedDate' => $this->request['joinedDate'],
                'withDrawal' => $this->request['withDrawal'],
                'introduction' => $this->request['introduction'],
                'talkSamId' => $this->request['talkSamId'],
                'basicPoint' => $this->request['basicPoint'],
                'campusId' => $this->request['campusId'],
                'type' => $this->request['type'],
            ];
            $newStudent = Students::create($studentParams);

            $productIds = $this->request['productIds'];
            $classId = $this->request['classId'];

            $userParams = [
                'name' => $this->request['name'],
                'userName' => $this->request['userName'],
                'email' => $this->request['email'],
                'password' => $this->request['password'],
                'studentId' => $studentId,
            ];
            UserController::store($userParams);

            $studentProductParams = [
                'studentId' => $studentId,
                'productIds' => $productIds,
            ];
            StudentProductController::createStudentProductByAdmin($studentProductParams);

            $studentClassParams = [
                'studentId' => $studentId,
                'classId' => $classId,
            ];
            StudentClassController::createStudentClassByAdmin($studentClassParams);

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
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successStudentRequest($newStudent);
    }
}
