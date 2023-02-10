<?php

use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\CampusManagerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('admin', AdminController::class);
Route::resource('campus', CampusController::class);
Route::resource('campus-manger', CampusManagerController::class);
Route::resource('parent', ParentController::class);
Route::resource('student', StudentController::class);
Route::resource('teacher', TeacherController::class);
