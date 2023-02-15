<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
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
        return $adminsData;
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
            'adminId' => 'required|unique:admins',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $params = [
            'adminId' => request('adminId'),
            'name' => request('name'),
            'email' => request('email'),
            'password' => Hash::make(request('password')),
        ];
        $newAdmin = new AdminResource(Admin::create($params));
        return $newAdmin;
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
        return $adminData;
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
        $validator = validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|unique:admins',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $params = [
            $admin['name'] = $request['name'],
            $admin['email'] = $request['email'],
            $admin['password'] = Hash::make($request['password']),
        ];
        $newInfoAdmin = $admin->update($params);
        return $newInfoAdmin;
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
        return $this->successRequest($deleteAdmin);
    }
}
