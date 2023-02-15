<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Roles::all();
        return $this->successRoleRequest($data);
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
            'roleId' => 'required|string|unique:roles',
            'name' => 'required|string',
            'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        
        $params = [
            'roleId' => request('roleId'),
            'name' => request('name'),
            'activate' => request('activate'),
        ];
        $newRole = new RoleResource(Roles::create($params));
        return $this->successRoleRequest($newRole);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($roleId)
    {
        $role = Roles::find($roleId);
        $roleData = new RoleResource($role);
        return $this->successRoleRequest($roleData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $roleId)
    {
        $role = Roles::find($roleId);
        if(empty($request->name)) {
            $request['name'] = $role['name'];
        }
        if(empty($request->activate)) {
            $request['activate'] = $role['activate'];
        }

        $validator = validator::make($request->all(), [
            'name' => 'required',
            'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            $role['name'] = $request['name'],
            $role['activate'] = $request['activate'],
        ];
        $newInfoRole = new RoleResource($role->update($params));
        return $this->successRoleRequest($newInfoRole);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($roleId)
    {
        $role = Roles::find($roleId);
        $deleteRole = $role->delete();
        return $this->successRoleRequest($deleteRole);
    }
}
