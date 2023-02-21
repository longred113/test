<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampusResource;
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
        $campusesData = CampusResource::collection(Campus::all());
<<<<<<< HEAD:app/Http/Controllers/api/Backup/CampusController.php
        return $this->successCampusRequest($campusesData);
=======
        return $campusesData;
>>>>>>> 8c921956d2c9cf2cfbc93d33eb35c28f8fd16936:app/Http/Controllers/api/CampusController.php
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
            'campusId' => 'required|string|unique:campuses',
            'name' => 'required|string',
            'indicated' => 'required|string',
            'contact' => 'required|string',
            'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            'campusId' => request('campusId'),
            'name' => request('name'),
            'indicated' => request('indicated'),
            'contact' => request('contact'),
            'activate' => request('activate'),
        ];
        $newCampus = new CampusResource(Campus::create($params));
<<<<<<< HEAD:app/Http/Controllers/api/Backup/CampusController.php
        return $this->successCampusRequest($newCampus);
=======
        return $newCampus;
>>>>>>> 8c921956d2c9cf2cfbc93d33eb35c28f8fd16936:app/Http/Controllers/api/CampusController.php
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($campusId)
    {
        $campus = Campus::find($campusId);
<<<<<<< HEAD:app/Http/Controllers/api/Backup/CampusController.php
        $campusData = new CampusResource($campus);
        return $this->successCampusRequest($campusData);
=======
        $campusesData = new CampusResource($campus);
        return $campusesData;
>>>>>>> 8c921956d2c9cf2cfbc93d33eb35c28f8fd16936:app/Http/Controllers/api/CampusController.php
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
        $campus = Campus::find($campusId);
        if(empty($request->name)) {
            $request['name'] = $campus['name'];
        }
<<<<<<< HEAD:app/Http/Controllers/api/Backup/CampusController.php
        if(empty($request->indicated)) {
            $request['indicated'] = $campus['indicated'];
        }
        if(empty($request->contact)) {
            $request['contact'] = $campus['contact'];
        }
        if(empty($request->activate)) {
            $request['activate'] = $campus['activate'];
        }

=======
        if(empty($request->name)) {
            $request['name'] = $campus['name'];
        }
        if(empty($request->name)) {
            $request['name'] = $campus['name'];
        }
        if(empty($request->name)) {
            $request['name'] = $campus['name'];
        }
>>>>>>> 8c921956d2c9cf2cfbc93d33eb35c28f8fd16936:app/Http/Controllers/api/CampusController.php
        $validator = validator::make($request->all(), [
            'name' => 'required|string',
            'indicated' => 'required|string',
            'contact' => 'required|string',
            'activate' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
<<<<<<< HEAD:app/Http/Controllers/api/Backup/CampusController.php
        
=======
>>>>>>> 8c921956d2c9cf2cfbc93d33eb35c28f8fd16936:app/Http/Controllers/api/CampusController.php
        $params = [
            $campus['name'] = $request['name'],
            $campus['indicated'] = $request['indicated'],
            $campus['contact'] = $request['contact'],
            $campus['activate'] = $request['activate'],
        ];
        $newInfoCampus = $campus->update($params);
        return $this->successCampusRequest($newInfoCampus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($campusId)
    {
        $campus = Campus::find($campusId);
        $deleteCampus = $campus->delete();
        return $this->successCampusRequest($deleteCampus);
    }
}
