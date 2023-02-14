<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campusesData = Campus::all();
        return $campusesData;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request->all());
        $validator = validator::make($request->all(), [
            'campusId' => 'required|unique:campuses',
            'name' => 'required',
            'indicated' => 'required',
            'contact' => 'required',
            'activate' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $params = [
            'campusId' => request('campusId'),
            'name' => request('name'),
            'indicated' => request('indicated'),
            'contact' => request('contact'),
            'activate' => request('activate')
        ];
        $newCampus = Campus::create($params);
        return $newCampus;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Campus $campus)
    {
        $campusId = Campus::get('campusId');
        // $data
        return $campus;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $campusId)
    {
        $validator = validator::make($request->all(), [
            'name' => 'required',
            'indicated' => 'required',
            'contact' => 'required',
            'activate' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $nameCampus = $request['name'];
        $indicated = $request['indicated'];
        $contact = $request['contact'];
        $activate = $request['activate'];

        $campus = Campus::find($campusId);
        if(empty ($nameCampus)) {
            $nameCampus = $campus['name'];
        }
        if(empty($indicated)) {
            $indicated = $campus['indicated'];
        }
        if(empty($contact)) {
            $contact = $campus['contact'];
        }
        if(empty($activate)) {
            $activate = $campus['activate'];
        }
        $params = [
            $campus['name'] = $nameCampus,
            $campus['indicated'] = $indicated,
            $campus['contact'] = $contact,
            $campus['activate'] = $activate,
        ];
        $newInfoCampus = $campus->update($params);
        return $this->successRequest($newInfoCampus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campus $campus)
    {
        $deleteCampus = $campus->delete();
        return $this->successRequest($deleteCampus);
    }
}
