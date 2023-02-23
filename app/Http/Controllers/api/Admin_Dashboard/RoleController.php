<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class RoleController extends Controller
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
        $data = RoleResource::collection(Roles::all());
        return $this->successRoleRequest($data);
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
            'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $roleId = IdGenerator::generate(['table'=>'roles', 'trow' => 'roleId', 'length' => 7, 'prefix' => 'RL']);
        $params = [
            'roleId' => $roleId,
            'name' => $this->request['name'],
            'activate' => $this->request['activate'],
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
    public function update($roleId)
    {
        $role = Roles::find($roleId);
        if(empty($this->request['name'])) {
            $this->request['name'] = $role['name'];
        }
        if(empty($this->request['activate'])) {
            $this->request['activate'] = $role['activate'];
        }

        $validator = validator::make($this->request->all(), [
            'name' => 'required',
            'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            $role['name'] = $this->request['name'],
            $role['activate'] = $this->request['activate'],
        ];
        $newInfoRole = $role->update($params);
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
