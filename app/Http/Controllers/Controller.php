<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function errorBadRequest($message = '', $data = [])
    {
        $response = array(
            'error_code' => 0,
            'message' => ['Successfully'],
            'data' => $data,
            );
       
        return $response;
    }
}