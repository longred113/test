<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CampusManager;
use App\Http\Resources\CampusManager as CampusManagerResource;

class CampusManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = CampusManager::all();
        return $data;
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
            'campusManagerId' => 'required|unique:campus_managers',
            'name' => 'required',
            'email' => 'required',
            'gender' => 'required',
            'dateOfBirth' => 'required',
            'country' => 'required',
            'timeZone' => 'required',
            'startDate' => 'required',
            'resignation' => 'required',
            'campusId' => 'required',
            'memo' => 'required',
            'offlineStudentId' => 'required',
            'offlineTeacherId' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $params = [
            'campusManagerId' => request('campusManagerId'),
            'name' => request('name'),
            'email' => request('email'),
            'gender' => request('gender'),
            'dateOfBirth' => request('dateOfBirth'),
            'country' => request('country'),
            'timeZone' => request('timeZone'),
            'startDate' => request('startDate'),
            'resignation' => request('resignation'),
            'campusId' => request('campusId'),
            'memo' => request('memo'),
            'offlineStudentId' => request('offlineStudentId'),
            'offlineTeacherId' => request('offlineTeacherId')
        ];
        $newCampusManager = new CampusManagerResource(CampusManager::create($params));
        return $newCampusManager;
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
    public function update(Request $request, CampusManager $CampusManager)
    {

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