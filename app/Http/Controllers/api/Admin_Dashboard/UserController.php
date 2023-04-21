<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Users;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Imports\UsersImport;
use App\Models\Roles;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
    public static function store($userParams)
    {
        $userId = IdGenerator::generate(['table' => 'users', 'trow' => 'userId', 'length' => 7, 'prefix' => 'US']);
        if (empty($userParams['name'])) {
            $userParams['name'] = "";
        }
        $params = [
            'userId' => $userId,
            'name' => $userParams['name'],
            'email' => $userParams['email'],
            'password' => $userParams['password'],
        ];
        if (!empty($userParams['teacherId'])) {
            $params['teacherId'] = $userParams['teacherId'];
        }
        if (!empty($userParams['studentId'])) {
            $params['studentId'] = $userParams['studentId'];
        }
        if (!empty($userParams['parentId'])) {
            $params['parentId'] = $userParams['parentId'];
        }
        if (!empty($userParams['campusManagerId'])) {
            $params['campusManagerId'] = $userParams['campusManagerId'];
        }
        if (!empty($userParams['campusId'])) {
            $params['campusId'] = $userParams['campusId'];
        }
        $roles = Roles::all();
        foreach ($roles as $role) {
            if (!empty($userParams['teacherId']) && $role['name'] == 'teacher') {
                $params['roleId'] = $role['roleId'];
            }
            if (!empty($userParams['studentId']) && $role['name'] == 'student') {
                $params['roleId'] = $role['roleId'];
            }
            if (!empty($userParams['parentId']) && $role['name'] == 'parent') {
                $params['roleId'] = $role['roleId'];
            }
            if (!empty($userParams['campusManagerId']) && $role['name'] == 'campus manager') {
                $params['roleId'] = $role['roleId'];
            }
            if (!empty($userParams['campusId']) && $role['name'] == 'campus manager') {
                $params['roleId'] = $role['roleId'];
            }
        }
        if (!empty($userParams)) {
            $params['activate'] = 1;
        }
        $newUserData = new UserResource(Users::create($params));
        return $newUserData;
    }

    public function createUserAccount()
    {
        $validator = Validator::make($this->request->all(), [
            'name' => 'string|required',
            'userName' => 'string',
            'email' => 'string|required|unique:users',
            'password' => 'string|required|min:8',
            'activate' => 'integer|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        try{

            $userId = IdGenerator::generate(['table' => 'users', 'trow' => 'userId', 'length' => 7, 'prefix' => 'US']);
            $params = [
                'userId' => $userId,
                'name' => $this->request['name'],
                'userName' => $this->request['userName'],
                'email' => $this->request['email'],
                'password' => $this->request['password'],
                'activate' => $this->request['activate'],
            ];
            if (!empty($this->request['teacherId'])) {
                $params['teacherId'] = $this->request['teacherId'];
            }
            if (!empty($this->request['studentId'])) {
                $params['studentId'] = $this->request['studentId'];
            }
            if (!empty($this->request['parentId'])) {
                $params['parentId'] = $this->request['parentId'];
            }
            if (!empty($this->request['campusManagerId'])) {
                $params['campusManagerId'] = $this->request['campusManagerId'];
            }
            $roles = Roles::all();
            foreach ($roles as $role) {
                if (!empty($this->request['teacherId']) && $role['name'] == 'teacher') {
                    $params['roleId'] = $role['roleId'];
                }
                if (!empty($this->request['studentId']) && $role['name'] == 'student') {
                    $params['roleId'] = $role['roleId'];
                }
                if (!empty($this->request['parentId']) && $role['name'] == 'parent') {
                    $params['roleId'] = $role['roleId'];
                }
                if (!empty($this->request['campusManagerId']) && $role['name'] == 'campus manager') {
                    $params['roleId'] = $role['roleId'];
                }
            }
            $newUserData = new UserResource(Users::create($params));
            return $this->successUserRequest($newUserData);
        }catch(Exception $e) {
            return $e->getMessage();
        }
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

    public function getUserFromTeacher($teacherId)
    {
        $user = Users::where('teacherId', $teacherId)->get();
        return $this->successUserRequest($user);
    }

    public function getUserFromStudent($studentId)
    {
        $user = Users::where('studentId', $studentId)->get();
        return $this->successUserRequest($user);
    }

    public function getUserFromParent($parentId)
    {
        $user = Users::where('parentId', $parentId)->get();
        return $this->successUserRequest($user);
    }

    public function getUserFromCampus($campusId)
    {
        $user = Users::where('campusId', $campusId)->get();
        return $this->successUserRequest($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function update($userParams)
    {
        $params = [
            'name' => $userParams['name'],
            'email' => $userParams['email'],
            'password' => $userParams['password'],
        ];
        if(!empty($userParams['userName'])){
            $params['userName'] = $userParams['userName'];
        }
        if(!empty($userParams['teacherId'])){
            $params['teacherId'] = $userParams['teacherId'];
            $newInfoUser = Users::where('teacherId',$userParams['teacherId'])->update($params);
        }
        if(!empty($userParams['studentId'])){
            $params['studentId'] = $userParams['studentId'];
            $newInfoUser = Users::where('studentId',$userParams['studentId'])->update($params);
        }
        if(!empty($userParams['parentId'])){
            $params['parentId'] = $userParams['parentId'];
            $newInfoUser = Users::where('parentId',$userParams['parentId'])->update($params);
        }
        if(!empty($userParams['campusManagerId'])){
            $params['campusManagerId'] = $userParams['campusManagerId'];
            $newInfoUser = Users::where('campusManagerId',$userParams['campusManagerId'])->update($params);
        }
        return $newInfoUser;
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

    public function checkLogin($userId)
    {
        $validator = Validator::make($this->request->all(), [
            'checkLogin' => 'integer|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        try{
            $updateCheckLogin = Users::where('userId', $userId)->update(['checkLogin' => $this->request['checkLogin']]);
        }catch(Exception $e) {
            return $e->getMessage();
        }
        return $this->successUserRequest($updateCheckLogin);
    }

    public function checkInfoLogin() {
        $validator = Validator::make($this->request->all(), [
            'email' => 'string|required',
            'password' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $checkInfo = Users::where('email', $this->request['email'])
            ->where('password', $this->request['password'])->get();

        return $this->successUserRequest($checkInfo);
    }

    public function exportUser()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
    
    public function importUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $file = $request->file('file');
        Excel::import(new UsersImport, $file);
        return $this->successUserRequest('Import successfully');
    }
}
