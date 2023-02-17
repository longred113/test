<?php

use App\Http\Controllers\api\Admin_Dashboard\AdminController;
use App\Http\Controllers\api\Admin_Dashboard\CampusManagerController;
use App\Http\Controllers\api\Admin_Dashboard\CampusController;
use App\Http\Controllers\api\Admin_Dashboard\RoleController;
use App\Http\Controllers\api\Admin_Dashboard\TeacherController;
use App\Http\Controllers\api\Admin_Dashboard\UserController;
use App\Http\Controllers\api\Admin_Dashboard\PackagesController;
use App\Http\Controllers\api\Campus_Dashboard\OffTeachController;
use App\Models\Teachers;
use App\Http\Controllers\api\ProductController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('dash-board-admin')
    ->name('dash-board-admin.')
    ->group(function() {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/create', [AdminController::class, 'store'])->name('store');
        Route::get('/{adminId}', [AdminController::class, 'show'])->name('show');
        Route::put('/update/{adminId}', [AdminController::class, 'update'])->name('update');
        Route::delete('/{adminId}', [AdminController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-campus-management')
    ->name('admin-campus-management.')
    ->group(function() {
        Route::get('/', [CampusController::class, 'index'])->name('index');
        Route::post('/create', [CampusController::class, 'store'])->name('store');
        Route::get('/{campusId}', [CampusController::class, 'show'])->name('show');
        Route::put( '/update/{campusId}', [CampusController::class, 'update'])->name('update');
        Route::delete('/{campusId}', [CampusController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-campus-manger')
    ->name('admin-campus-manger.')
    ->group(function() {
        Route::get('/', [CampusManagerController::class, 'index'])->name('index');
        Route::post('/create', [CampusManagerController::class, 'store'])->name('store');
        Route::get('/{campusManagerId}', [CampusManagerController::class, 'show'])->name('show');
        Route::put( '/update/{campusManagerId}', [CampusManagerController::class, 'update'])->name('update');
        Route::delete('/{campusManagerId}', [CampusManagerController::class, 'destroy'])->name('destroy');
    });

Route::resource('parent', ParentController::class);
Route::resource('student', StudentController::class);
Route::resource('teacher', TeacherController::class);

Route::prefix('admin-role-management')
    ->name('admin-role-management.')
    ->group(function() {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/create', [RoleController::class, 'store'])->name('store');
        Route::get('/{roleId}', [RoleController::class, 'show'])->name('show');
        Route::put( '/update/{roleId}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{roleId}', [RoleController::class, 'destroy'])->name('destroy');
    });

Route::resource('users', UserController::class);

Route::prefix('admin-teacher-management')
    ->name('admin-teacher-management')
    ->group(function() {
        Route::get('/', [TeacherController::class, 'index'])->name('index');
        Route::post('/create', [TeacherController::class, 'store'])->name('store');
        Route::get('/{teacherId}', [TeacherController::class, 'show'])->name('show');
        Route::put('/update/{teacherId}', [TeacherController::class, 'update'])->name('update');
        Route::delete('/{teacherId}', [TeacherController::class], 'destroy')->name('destroy');
    });

Route::prefix('packages')
    ->name('packages.')
    ->group(function() {
        Route::get('/', [PackagesController::class, 'index'])->name('index');
        Route::post('/create', [PackagesController::class, 'store'])->name('store');
        Route::get('/{packageId}', [PackagesController::class, 'show'])->name('show');
        Route::put( '/update/{packageId}', [PackagesController::class, 'update'])->name('update');
        Route::delete('/{packageId}', [PackagesController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-product-management')
    ->name('admin-product-management.')
    ->group(function() {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::post('/create', [ProductController::class, 'store'])->name('store');
        Route::get('/{productId}', [ProductController::class, 'show'])->name('show');
        Route::put( '/update/{productId}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{productId}', [ProductController::class, 'destroy'])->name('destroy');
    });

// CAMPUS

Route::prefix('campus-teacherOff')
    ->name('campus-teacherOff.')
    ->group(function() {
        Route::get('/', [OffTeachController::class, 'index'])->name('index');
        Route::post('/create', [OffTeachController::class, 'store'])->name('store');
        Route::get('/{teacherId}', [OffTeachController::class, 'show'])->name('show');
        Route::put( '/update/{teacherId}', [OffTeachController::class, 'update'])->name('update');
        Route::delete('/{teacherId}', [OffTeachController::class, 'destroy'])->name('destroy');
    });

Route::prefix('campus-student')
    ->name('campus-student.')
    ->group(function() {
        Route::get('/', [OffStudentController::class, 'index'])->name('index');
        Route::post('/create', [OffStudentController::class, 'store'])->name('store');
        Route::get('/{studentId}', [OffStudentController::class, 'show'])->name('show');
        Route::put( '/update/{studentId}', [OffStudentController::class, 'update'])->name('update');
        Route::delete('/{studentId}', [OffStudentController::class, 'destroy'])->name('destroy');
    });