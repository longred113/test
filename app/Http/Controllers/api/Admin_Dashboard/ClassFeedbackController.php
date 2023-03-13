<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ClassFeedbacks;
use App\Http\Resources\ClassFeedbackResource;
use Carbon\Carbon;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class ClassFeedbackController extends Controller
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
        $data = ClassFeedbackResource::collection(ClassFeedbacks::all());
        return $this->successClassRequest($data);
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
            'teacherId' => 'string|required',
            // 'classId' => 'string|required',
            // 'studentId' => 'string|required',
            // 'campusId' => 'string|required',
            // 'date' => 'string|required',
            'satisfaction' => 'integer',
            // 'comment' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $classFeedbackId = IdGenerator::generate(['table' => 'class_feedbacks', 'trow' => 'classFeedbackId', 'length' => 8, 'prefix' => 'CFB']);
        $params = [
            'classFeedbackId' => $classFeedbackId,
            'teacherId' => $this->request['teacherId'],
            'classId' => $this->request['classId'],
            'studentId' => $this->request['studentId'],
            'campusId' => $this->request['campusId'],
            'date' => Carbon::now(),
            'satisfaction' => $this->request['satisfaction'],
            'comment' => $this->request['comment'],
        ];
        $newClassFeedback = new ClassFeedbackResource(ClassFeedbacks::create($params));
        return $this->successClassFeedback($newClassFeedback);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ClassFeedback)
    {
        $ClassFeedbacks = ClassFeedbacks::find($ClassFeedback);
        $ClassFeedbacksData = new ClassFeedbackResource($ClassFeedbacks);
        return $this->successClassFeedback($ClassFeedbacksData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($classFeedbackId)
    {
        $classFeedback = ClassFeedbacks::find($classFeedbackId);
        if (empty($this->request['teacherId'])) {
            $this->request['teacherId'] = $classFeedback['teacherId'];
        }
        if (empty($this->request['classId'])) {
            $this->request['classId'] = $classFeedback['classId'];
        }
        if (empty($this->request['studentId'])) {
            $this->request['studentId'] = $classFeedback['studentId'];
        }
        if (empty($this->request['campusId'])) {
            $this->request['campusId'] = $classFeedback['campusId'];
        }
        if (empty($this->request['date'])) {
            $this->request['date'] = $classFeedback['date'];
        }
        if (empty($this->request['satisfaction'])) {
            $this->request['satisfaction'] = $classFeedback['satisfaction'];
        }
        if (empty($this->request['comment'])) {
            $this->request['comment'] = $classFeedback['comment'];
        }
        $validator = Validator::make($this->request->all(), [
            // 'teacherId' => 'string|required',
            // 'classId' => 'string|required',
            // 'studentId' => 'string|required',
            // 'campusId' => 'string|required',
            // 'date' => 'string|required',
            // 'satisfaction' => 'string|required',
            'comment' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $classFeedback['teacherId'] = $this->request['teacherId'],
            $classFeedback['classId'] = $this->request['classId'],
            $classFeedback['studentId'] = $this->request['studentId'],
            $classFeedback['campusId'] = $this->request['campusId'],
            $classFeedback['date'] = $this->request['date'],
            $classFeedback['satisfaction'] = $this->request['satisfaction'],
            $classFeedback['comment'] = $this->request['comment'],
        ];

        $newClassFeedbacksData = $classFeedback->update($params);
        return $this->successClassFeedback($newClassFeedbacksData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($classFeedbackId)
    {
        $ClassFeedback = ClassFeedbacks::find($classFeedbackId);
        $deleteClassFeedback = $ClassFeedback->delete();
        return $this->successClassFeedback($deleteClassFeedback);
    }
}
