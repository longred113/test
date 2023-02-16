<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Packages;
use App\Http\Resources\Packages as PackagesResource;
class PackagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = PackagesResource::collection(Packages::all());
        return $this->successPackagesRequest($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'packageId' => 'required|string|unique:packages',
            'name' => 'required|string',
            // 'startLevel' => 'required|string',
            // 'endLevel' => 'required|string',
            // 'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            'packageId' => request('packageId'),
            'name' => request('name'),
            'startLevel' => request('startLevel'),
            'endLevel' => request('endLevel'),
            'activate' => request('activate'),
        ];
        $newPackages = Packages::create($params);
        return $this->successPackagesRequest($newPackages);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($packageId)
    {
        $package = Packages::find($packageId);
        $packageData = $package;
        return $this->successPackagesRequest($packageData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, $packageId)
    {
        $packages = Packages::find($packageId);
        if(empty($request->name)) {
            $request['name'] = $packages['name'];
        }
        if(empty($request->startLevel)) {
            $request['startLevel'] = $packages['startLevel'];
        }
        if(empty($request->endLevel)) {
            $request['endLevel'] = $packages['endLevel'];
        }
        if(empty($request->activate)) {
            $request['activate'] = $packages['activate'];
        }

        $validator = validator::make($request->all(), [
            'name' => 'required|string',
            // 'startLevel' => 'required|string',
            // 'endLevel' => 'required|string',
            // 'activate' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        
        $params = [
            $packages['name'] = $request['name'],
            $packages['startLevel'] = $request['startLevel'],
            $packages['endLevel'] = $request['endLevel'],
            $packages['activate'] = $request['activate'],
        ];
        $newInfoPackages = $packages->update($params);
        return $this->successPackagesRequest($newInfoPackages);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($packageId)
    {
        $package = Packages::find($packageId);
        $deletePackages = $package->delete();
        return $this->successPackagesRequest($deletePackages);
    }
}