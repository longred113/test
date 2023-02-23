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
        $adminsData = AdminResource::collection(Admin::all());
        return $this->successAdminRequest($adminsData);
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
            'email' => 'required|string|unique:admins',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $adminId = IdGenerator::generate(['table'=>'admins', 'trow' => 'adminId', 'length' => 7, 'prefix' => 'AD']);
        $params = [
            'adminId' => $adminId,
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'password' => $this->request['password'],
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
    public function update($adminId)
    {
        $admin = Admin::find($adminId);
        if(empty($this->request['name'])) {
            $this->request['name'] = $admin['name'];
        }
        if(empty($this->request['activate'])) {
            $this->request['email'] = $admin['email'];
        }
        if(empty($request['password'])) {
            $this->request['password'] = $admin['password'];
        }

        $validator = validator::make($this->request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:admins',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        
        $params = [
            $admin['name'] = $this->request['name'],
            $admin['email'] = $this->request['email'],
            $admin['password'] = $this->request['password'],
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
