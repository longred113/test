<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampusResource;
use App\Models\Campus;
use GrahamCampbell\ResultType\Success;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampusController extends Controller
{
    protected Request $request;

    public function __construct(
        Request $request
        )       
    {
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campusesData = CampusResource::collection(Campus::all());
        return $this->successCampusRequest($campusesData);
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
            'name' => 'required|string',
            // 'indicated' => 'required|string',
            // 'contact' => 'required|string',
            // 'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $campusId = IdGenerator::generate(['table'=>'campuses', 'trow' => 'campusId', 'length' => 8, 'prefix' => 'CP-']);
        $params = [
            'campusId' => $campusId,
            'name' => request('name'),
            'indicated' => request('indicated'),
            'contact' => request('contact'),
            'activate' => request('activate'),
        ];
        $newCampus = new CampusResource(Campus::create($params));
        return $this->successCampusRequest($newCampus);
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
        $campusData = new CampusResource($campus);
        return $this->successCampusRequest($campusData);
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
        if(empty($request->indicated)) {
            $request['indicated'] = $campus['indicated'];
        }
        if(empty($request->contact)) {
            $request['contact'] = $campus['contact'];
        }
        if(empty($request->activate)) {
            $request['activate'] = $campus['activate'];
        }

        $validator = validator::make($request->all(), [
            'name' => 'required|string',
            'indicated' => 'required|string',
            'contact' => 'required|string',
            'activate' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        
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

    public function switchActivate()
    {
        $validator = validator::make($this->request->all(), [
            'campusId' => 'string|required_without:campusIds',
            'campusIds' => 'array|required_without:campusId'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }
        
        if (!empty($this->request->get('campusId'))) {
            $ids[] = $this->request->get('campusId');
        } else {
            $ids = $this->request->get('campusIds');
        }
        $campuses = Campus::find(($ids));
        foreach ($campuses as $campus) {
            if ($campus['activate'] == 1) {
                Campus::where('campusId', $campus['campusId'])->update(['activate' => 0]);
            }
            else{
                Campus::where('campusId', $campus['campusId'])->update(['activate' => 0]);
            }
        }
        return $this->successCampusRequest();
    }
}