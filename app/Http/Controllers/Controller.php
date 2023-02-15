<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function successRequest($newInfoCampusManager = array())
    {
        // $response = array(
        //     'error_code' => 0,
        //     'message' => ['Successfully'],
        //     'data' => $newInfoCampus,
        //     );
       
        return response()->json([
            'error_code' => 0,
            'message' => ['Successfully'],
            'data CM' => $newInfoCampusManager,
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
}