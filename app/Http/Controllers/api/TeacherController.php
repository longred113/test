<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampusResource;
use App\Http\Resources\TeacherResource;
use App\Models\Campus;
use App\Models\Teachers;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TypeError;

use function PHPUnit\Framework\returnSelf;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teachersData = TeacherResource::collection(Teachers::all());
        return $this->successTeacherRequest($teachersData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $campusId = Campus::get('campusId');
        $nameCampus = Campus::where($campusId, 'campusId')->get('name');
        $validator = validator::make($request->all(), [
            'teacherId' => 'required|string|unique:teachers',
            'name' => 'required|string',
            // 'email' => 'required|string|unique:teachers',
            // 'gender' => 'required|string',
            // 'dateOfBirth' => 'required|date',
            // 'status' => 'required|string',
            // 'activate' => 'required',
            // 'country' => 'required|string',
            // 'timeZone' => 'required|string',
            // 'startDate' => 'required|string',
            // 'resignation' => 'required',
            // 'resume' => 'required|string',
            // 'certificate' => 'required|string',
            // 'contract' => 'required|string',
            // 'basicPoint' => 'required|integer',
            // 'type' => 'required|string',
            // 'talkSamId' => 'require|string',
            'campusId' => 'required|string',
        ]);
        if ($validator->failed()) {
            return $validator->errors();
        }
        $inputCampusId = $campusId::get($request->campusId);
        dd($inputCampusId);
        if (!empty($request->campusId)) {
            var_dump($request->campusId == $campusId::find($request->campusId));
            if ($request->campusId == $campusId) {
                $request['campusId'] = $campusId;
            } else {
                echo 'effff';
            }
            
        }
        $params = [
            'teacherId' => request('teacherId'),
            'name' => request('name'),
            'email' => request('email'),
            'gender' => request('gender'),
            'dateOfBirth' => request('dateOfBirth'),
            'status' => request('status'),
            'activate' => request('activate'),
            'country' => request('country'),
            'timeZone' => request('timeZone'),
            'startDate' => request('startDate'),
            'resignation' => request('resignation'),
            'resume' => request('resume'),
            'certificate' => request('certificate'),
            'contract' => request('contract'),
            'basicPoint' => request('basicPoint'),
            'campusId' => request('campusId'),
            'type' => request('type'),
            'talkSamId' => request('talkSamId'),
        ];
        dd($params);
        $newTeacherData = new TeacherResource(Teachers::create($params));
        return $this->successTeacherRequest($newTeacherData);
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
