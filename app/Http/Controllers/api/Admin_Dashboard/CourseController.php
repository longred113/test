<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Courses;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
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
        try {
            $courses = Courses::all();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successCourseRequest($courses);
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
            'level' => 'string|required',
            'courses' => 'array|required',
            'unit' => 'integer|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        try {
            foreach($this->request->courses as $course) {
                $courseId = IdGenerator::generate(['table' => 'courses', 'trow' => 'courseId', 'length' => 7, 'prefix' => 'CO']);
                $params = [
                    'courseId' => $courseId, // 'CO00001
                    'level' => $this->request->level,
                    'course' => $course,
                    'unit' => $this->request->unit,
                ];
                $newCourse [] = Courses::create($params);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successCourseRequest($newCourse);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($courseId)
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
    public function update($courseId)
    {
        try {
            $course = Courses::where('courseId', $courseId)->first();
            $params = [];
            if (!empty($this->request->level)) {
                $params['level'] = $this->request->level;
            }
            if (!empty($this->request->course)) {
                $params['course'] = $this->request->course;
            }
            if (!empty($this->request->unit)) {
                $params['unit'] = $this->request->unit;
            }
            $course->update($params);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successCourseRequest($course);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($courseId)
    {
        try {
            $course = Courses::where('courseId', $courseId)->first();
            $course->delete();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successCourseRequest($course);
    }
}
