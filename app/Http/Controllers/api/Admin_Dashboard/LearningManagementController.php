<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\ClassFeedbacks;
use App\Models\ClassReports;
use App\Models\GroupActivities;
use App\Models\ProductGroups;
use App\Models\ProductMatchedActivities;
use App\Models\StudentClasses;
use App\Models\StudentMatchedActivities;
use App\Models\StudentProducts;
use App\Models\Students;
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
                        $classLists []= $classList;
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
    public function store(Request $request)
    {
        //
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
            $student = Students::where('studentId', $studentId)->first();
            $student->campusName = Campus::where('campusId', $student->campusId)->first()->name;
            $student->products = StudentProducts::join('product_matched_activities', 'student_products.productId', '=', 'product_matched_activities.productId')
                ->selectRaw(
                    'GROUP_CONCAT(DISTINCT CONCAT_WS(":",product_matched_activities.productId, product_matched_activities.productName))as products',
                )
                ->where('studentId', $studentId)
                ->pluck('products')
                ->first();
            $products = explode(',', $student->products);
            foreach ($products as $product) {
                $parts = explode(':', $product);
                $productId = $parts[0];
                $matchActivities = ProductMatchedActivities::where('productId', $productId)
                    ->select('matchedActivityId', 'matchedActivityName')->get();
                foreach ($matchActivities as $activity) {
                    $productActivities[$product][] = [
                        'matchActivityId' => $activity->matchedActivityId,
                        'matchActivityName' => $activity->matchedActivityName,
                    ];
                }
                $productGroup[$product] = ProductGroups::where('productId', $productId)
                    ->select('groupId', 'groupName')->get();
            }
            $student->productGroup = $productGroup;
            foreach($student->productGroup as $product => $groups){
                foreach($groups as $group){
                    $groupId = $group->groupId;
                    $groupName = $group->groupName;
                    $groupActivities = GroupActivities::where('groupId', $groupId)
                        ->select('matchedActivityId', 'matchedActivityName')->get();
                    $productGroupActivity[$product][$groupId.":".$groupName] = $groupActivities;
                }
            }
            $student->products = $productGroupActivity;
            $student->Level = StudentClasses::join('classes', 'student_classes.classId', '=', 'classes.classId')
                ->select(
                    'classes.level',
                )
                ->where('student_classes.studentId', $studentId)
                ->distinct()
                ->get();
            $student->class = StudentClasses::join('classes', 'student_classes.classId', '=', 'classes.classId')
                ->join('class_products', 'classes.classId', '=', 'class_products.classId')
                ->join('product_matched_activities', 'class_products.productId', '=', 'product_matched_activities.productId')
                ->join('student_matched_activities', 'student_classes.studentId', '=', 'student_matched_activities.studentId')
                ->leftJoin('class_times', 'classes.classId', '=', 'class_times.classId')
                ->select(
                    'classes.classId',
                    'classes.name as className',
                    'classes.level',
                    'classes.classStartDate',
                    // 'class_times.classEndDate',
                    'classes.classday',
                    'classes.classTimeSlot',
                    'classes.typeOfClass',
                    'classes.numberOfStudent',
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",product_matched_activities.productId, product_matched_activities.productName)) as productOfClasses'),
                )
                ->where('student_classes.studentId', $studentId)
                ->groupBy('classes.classId')
                ->get();
            foreach ($student->class as $class) {
                $productOfClasses = explode(',', $class->productOfClasses);
                $classId = $class->classId;
                foreach ($productOfClasses as $productOfClass) {
                    $productPart = explode(':', $productOfClass);
                    $productIdOfClass = $productPart[0];
                    $matchedActivities = ProductMatchedActivities::where('productId', $productIdOfClass)->select('matchedActivityId', 'matchedActivityName')->get();
                    $matchedActivityId = $matchedActivities->pluck('matchedActivityId')->toArray();
                    $status = StudentMatchedActivities::join('matched_activities', 'student_matched_activities.matchedActivityId', '=', 'matched_activities.matchedActivityId')
                        ->where('studentId', $studentId)
                        ->whereIn('student_matched_activities.matchedActivityId', $matchedActivityId)
                        ->select(
                            'matched_activities.matchedActivityId',
                            'student_matched_activities.name as matchedActivityName',
                            'student_matched_activities.status'
                        )
                        ->get();
                    $productActivityOfClasses[$productOfClass] = $status;
                }
                $class->productOfClasses = $productActivityOfClasses;
                $classFeedback = ClassFeedbacks::where('classId', $classId)->where('studentId', $studentId)->get();
                $class->classFeedback = $classFeedback;
                $classReport = ClassReports::where('classId', $classId)->where('studentId', $studentId)->get();
                $class->classReport = $classReport;
            }

            // return $student->class;
            // $student->studyPlaners = StudentMatchedActivities::where('studentId', $studentId)->get();
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
