<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Packages;
use App\Http\Resources\Packages as PackagesResource;

class PackagesController extends Controller
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
    public function store()
    {
        $validator = validator::make($this->request->all(), [
            'name' => 'required|string',
            'startLevel' => 'required',
            'endLevel' => 'required',
            'activate' => 'required',
            // 'details' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            'name' => $this->request['name'],
            'startLevel' => $this->request['startLevel'],
            'endLevel' => $this->request['endLevel'],
            'activate' => $this->request['activate'],
            'details' => $this->request['details'],
        ];
        $newPackages = new PackagesResource(Packages::create($params));
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
        if (empty($this->request['name'])) {
            $this->request['name'] = $packages['name'];
        }
        if (empty($this->request['startLevel'])) {
            $this->request['startLevel'] = $packages['startLevel'];
        }
        if (empty($this->request['endLevel'])) {
            $this->request['endLevel'] = $packages['endLevel'];
        }
        if (empty($this->request['activate'])) {
            $this->request['activate'] = $packages['activate'];
        }
        if (empty($this->request['details'])) {
            $this->request['details'] = $packages['details'];
        }

        $validator = validator::make($request->all(), [
            'name' => 'required|string',
            // 'startLevel' => 'required|string',
            // 'endLevel' => 'required|string',
            // 'activate' => 'required'
            // 'details' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $packages['name'] = $this->request['name'],
            $packages['startLevel'] = $this->request['startLevel'],
            $packages['endLevel'] = $this->request['endLevel'],
            $packages['activate'] = $this->request['activate'],
            $packages['details'] = $this->request['details'],
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
