<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function successCampusRequest($campusData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'campusData' => $campusData,
        ],200);
    }
    protected function successRoleRequest($roleData = array()) {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'roleData' => $roleData,
        ],200);
    }
    protected function successAdminRequest($adminData = array()) {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'adminData' => $adminData,
        ],200);
    }
    protected function successStudentRequest($studentData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'studentData' => $studentData,
        ],200);
    }
    protected function successTeacherRequest($teacherData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'teacherData' => $teacherData,
        ],200);
    }
    protected function successUserRequest($userData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'userData' => $userData,
        ],200);
    }
}
     protected function successPackagesRequest($packagesdata = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'packagesData' => $packagesdata,
        ],200);
    }
}