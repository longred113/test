<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function errorBadRequest($message = '', $data = [])
    {
        if (is_array($message)) {
            $tmp = array();
            foreach ($message as $key => $value) {
                if (is_array($value)) {
                    $tmp[] = $value[0];
                } else {
                    $tmp[] = $value;
                }
            }
            $message = $tmp;
        } else {
            $message = array($message);
        }

        $response = array(
            'error_code' => 400,
            'message' => $message,
            'data' => $data
        );
        return Response($response, 400);
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
    protected function successClassReport($ClassReportData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'ClassReportData' => $ClassReportData,
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
    protected function successCampusRequest($campusData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'campusData' => $campusData,
        ],200);
    }
    protected function successCampusManagerRequest($campusManagerData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'campusManagerData' => $campusManagerData,
        ],200);
    }
    protected function successEnrollmentRequest($enrollmentData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'enrollmentData' => $enrollmentData,
        ],200);
    }
    protected function successStudentClassRequest($studentClassData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'studentClassData' => $studentClassData,
        ],200);
    }
    protected function successClassBoardRequest($classBoardData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'classBoardData' => $classBoardData,
        ],200);
    }
    protected function successClassMaterialRequest($classMaterialData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'classMaterialData' => $classMaterialData,
        ],200);
    }
    protected function successClassProductRequest($classProductData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'classProductData' => $classProductData,
        ],200);
    }
    protected function successProductPackageRequest($productPackageData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'productPackageData' => $productPackageData,
        ],200);
    }
    protected function successStudyPlannerRequest($studyPlannerData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'studyPlannerData' => $studyPlannerData,
        ],200);
    }
    protected function successStudentProductRequest($studentProductData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'studentProductData' => $studentProductData,
        ],200);
    }
    protected function successStudentMatchedActivityRequest($studentMatchedActivityData = array())
    {
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'studentMatchedActivityData' => $studentMatchedActivityData,
        ],200);
    }
}