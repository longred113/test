<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function successRequest($data = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'CM_Data' => $data,
        ],200);
    }
    protected function successCampusRequest($data = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'campusData' => $data,
        ],200);
    }
    protected function successRoleRequest($data = array()) {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'roleData' => $data,
        ],200);
    }
    protected function successAdminRequest($data = array()) {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'adminData' => $data,
        ],200);
    }
    protected function studentRequest($newInfostudent = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'data student' => $newInfostudent,
        ],200);
    }
    protected function teacherRequest($newInfoteacher = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'data teacher' => $newInfoteacher,
        ],200);
    }
    protected function userRequest($newInfoUser = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'data user' => $newInfoUser,
        ],200);
    }
     protected function PackagesRequest($Packages = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'data Packages' => $Packages,
        ],200);
    }
}