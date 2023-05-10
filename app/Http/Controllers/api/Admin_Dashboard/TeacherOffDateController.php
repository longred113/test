<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TeacherOffDates;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherOffDateController extends Controller
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
            $teacherOffDates = TeacherOffDates::all();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successTeacherOffDateRequest($teacherOffDates);
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
            'date' => 'date|required', // '2021-09-01
            'day' => 'string|required',
            'classTimeSlotId' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $teacherOffDateId = IdGenerator::generate(['table' => 'teacher_off_dates', 'trow' => 'teacherOffDateId', 'length' => 8, 'prefix' => 'TOD']);
        $params = [
            'teacherOffDateId' => $teacherOffDateId,
            'teacherId' => $this->request['teacherId'],
            'date' => $this->request['date'],
            'day' => $this->request['day'],
            'classTimeSlotId' => $this->request['classTimeSlotId'],
        ];

        $newTeacherOffDate = TeacherOffDates::create($params);

        return $this->successTeacherOffDateRequest($newTeacherOffDate);
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
