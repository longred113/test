<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $adminsData = AdminResource::collection(Admin::all());
        return $this->successAdminRequest($adminsData);
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
            'email' => 'required|string|unique:admins',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $adminId = IdGenerator::generate(['table'=>'admins', 'trow' => 'adminId', 'length' => 8, 'prefix' => 'AD-']);
        $params = [
            'adminId' => $adminId,
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password'),
        ];
        $newAdmin = new AdminResource(Admin::create($params));
        return $this->successAdminRequest($newAdmin);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($adminId)
    {
        $admin = Admin::find($adminId);
        $adminData = new AdminResource($admin);
        return $this->successAdminRequest($adminData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $adminId)
    {
        $admin = Admin::find($adminId);
        if(empty($request->name)) {
            $request['name'] = $admin['name'];
        }
        if(empty($request->activate)) {
            $request['email'] = $admin['email'];
        }
        if(empty($request->activate)) {
            $request['password'] = $admin['password'];
        }

        $validator = validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:admins',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        
        $params = [
            $admin['name'] = $request['name'],
            $admin['email'] = $request['email'],
            $admin['password'] = $request['password'],
        ];
        $newInfoAdmin = $admin->update($params);
        return $this->successAdminRequest($newInfoAdmin);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($adminId)
    {
        $admin = Admin::find($adminId);
        $deleteAdmin = $admin->delete();
        return $this->successAdminRequest($deleteAdmin);
    }
}
