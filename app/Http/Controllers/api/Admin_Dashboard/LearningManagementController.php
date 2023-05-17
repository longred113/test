<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
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
