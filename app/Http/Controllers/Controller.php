<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // protected function errorBadRequest($message = '', $data = [])
    // {
    //     $response = array(-
    //         'error_code' => 0,
    //         'message' => ['Successfully'],
    //         'campusData' => $campusData,
    //     ],200);
    // }
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
    protected function successPackagesRequest($packagesData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'packagesData' => $packagesData,
        ],200);
    }
    protected function successProductsRequest($productData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'productData' => $productData,
        ],200);
    }
    protected function successUnitRequest($unitData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'unitData' => $unitData,
        ],200);
    }
    protected function successMatchedActivityRequest($matchedActivityData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'matchedActivityData' => $matchedActivityData,
        ],200);
    }
    protected function successClassFeedback($matchedClassFeedbackData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'matchedClassFeedbackData' => $matchedClassFeedbackData,
        ],200);
    }
    protected function successClassRequest($classData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'ClassData' => $classData,
        ],200);
    }
    protected function successClassMatchActivityRequest($classMatchActivityData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'classMatchActivityData' => $classMatchActivityData,
        ],200);
    }
    protected function successParentRequest($parentData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'parentData' => $parentData,
        ],200);
    }
}