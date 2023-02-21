<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Users;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Models\Roles;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class UserController extends Controller
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
        $usersData = UserResource::collection(Users::all());
        return $this->successUserRequest($usersData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($userParams)
    {
        // $validator = Validator::make($this->request->all(), [
        //     'userId' => 'string|required|unique:users',
        //     'name' => 'string|required',
        //     'email' => 'string|required|unique:users',
        //     'password' => 'string|required|min:8',
        //     'roleId' => 'string|required',
        // ]);
        // if ($validator->fails()) {
        //     return $validator->errors();
        // }
        // $id = Str::uuid()->toString();
        $userId = Helper::IDGenerator(new Users, 'userId', 5, 'US');
        $params = [
            'userId' => $userId,
            'name' => $userParams['name'],
            'email' => $userParams['email'],
            'password' => $userParams['password'],
        ];
        $roles = Roles::all();
        foreach($roles as $role) {
            if (!empty($userParams['teacherId'])) {
                $params['teacherId'] = $userParams['teacherId'];
                $params['roleId'] = $role->where('name', 'teacher')->get('roleId');
            }
            if (!empty($userParams['studentId'])) {
                $params['studentId'] = $userParams['studentId'];
                $params['roleId'] = $role->where('name', 'student')->get('roleId');
            }
            if (!empty($userParams['parentId'])) {
                $params['parentId'] = $userParams['parentId'];
                $params['roleId'] = $role->where('name', 'parent')->get('roleId');
            }
            if (!empty($userParams['campusManagerId'])) {
                $params['campusManagerId'] = $userParams['campusManagerId'];
                $params['roleId'] = $role->where('name', 'campus manager')->get('roleId');
            }
        }
        $newUserData = new UserResource(Users::create($params));
        return $newUserData;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($userId)
    {
        $user = Users::find($userId);
        $userData = new UserResource($user);
        return $this->successUserRequest($userData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($userId)
    {
        $user = Users::find($userId);
        if (empty($this->request->name)) {
            $this->request['name'] = $user['name'];
        }
        if (empty($this->request->email)) {
            $this->request['email'] = $user['email'];
        }
        if (empty($this->request->password)) {
            $this->request['password'] = $user['password'];
        }
        if (empty($this->request->roleId)) {
            $this->request['roleId'] = $user['roleId'];
        }
        if (empty($this->request->teacherId)) {
            $this->request['teacherId'] = $user['teacherId'];
        }
        if (empty($this->request->studentId)) {
            $this->request['studentId'] = $user['studentId'];
        }
        if (empty($this->request->parentId)) {
            $this->request['parentId'] = $user['parentId'];
        }
        if (empty($this->request->campusManagerId)) {
            $this->request['campusManagerId'] = $user['campusManagerId'];
        }
        $validator = Validator::make($this->request->all(), [
            'name' => 'string|required',
            'email' => 'string|required',
            'password' => 'string|required|min:8',
            'roleId' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            $user['name'] = $this->request['name'],
            $user['email'] = $this->request['email'],
            $user['password'] = $this->request['password'],
            $user['roleId'] = $this->request['roleId'],
            $user['teacherId'] = $this->request['teacherId'],
            $user['studentId'] = $this->request['studentId'],
            $user['parentId'] = $this->request['parentId'],
            $user['campusManagerId'] = $this->request['campusManagerId'],
        ];
        $newInfoUser = $user->update($params);
        return $this->successUserRequest($newInfoUser);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId)
    {
        $user = Users::find($userId);
        $deleteUser = $user->delete();
        return $this->successUserRequest($deleteUser);
    }
}
