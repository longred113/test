<?php

use App\Http\Controllers\api\Admin_Dashboard\AdminController;
use App\Http\Controllers\api\Admin_Dashboard\CampusController;
use App\Http\Controllers\api\Admin_Dashboard\ClassController;
use App\Http\Controllers\api\Admin_Dashboard\ClassMatchActivityController;
use App\Http\Controllers\api\Admin_Dashboard\MatchedActivityController;
use App\Http\Controllers\api\Admin_Dashboard\RoleController;
use App\Http\Controllers\api\Admin_Dashboard\TeacherController;
use App\Http\Controllers\api\Admin_Dashboard\UserController;
use App\Http\Controllers\api\Admin_Dashboard\PackagesController;
use App\Http\Controllers\api\Admin_Dashboard\ParentController;
use App\Http\Controllers\api\Admin_Dashboard\ProductController;
use App\Http\Controllers\api\Admin_Dashboard\StudentController;
use App\Http\Controllers\api\Admin_Dashboard\UnitController;
use App\Http\Controllers\api\Admin_Dashboard\ClassFeedbackController;
use App\Http\Controllers\api\Admin_Dashboard\EnrollmentControllerA;
use App\Http\Controllers\api\Campus_Dashboard\OffStudentController;
use App\Http\Controllers\api\Campus_Dashboard\OffTeachController;
use App\Http\Controllers\api\Campus_Dashboard\EnrollmentController;

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
        Route::put('/', [CampusController::class, 'switchActivate'])->name('switchActivate');
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

Route::prefix('admin-role-management')
    ->name('admin-role-management.')
    ->group(function() {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/create', [RoleController::class, 'store'])->name('store');
        Route::get('/{roleId}', [RoleController::class, 'show'])->name('show');
        Route::put( '/update/{roleId}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{roleId}', [RoleController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-teacher-management')
    ->name('admin-teacher-management')
    ->group(function() {
        Route::get('/', [TeacherController::class, 'index'])->name('index');
        Route::post('/create', [TeacherController::class, 'store'])->name('store');
        Route::get('/{teacherId}', [TeacherController::class, 'show'])->name('show');
        Route::put('/update/{teacherId}', [TeacherController::class, 'update'])->name('update');
        Route::delete('/{teacherId}', [TeacherController::class, 'destroy'])->name('destroy');
        Route::post('/', [TeacherController::class, 'multiDeleteTeacher'])->name('multiDeleteTeacher');
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
        Route::put('/update/{productId}', [ProductController::class, 'update'])->name('update');
        Route::put('/add-package/{productId}', [ProductController::class, 'addPackages'])->name('addPackages');
        Route::delete('/{productId}', [ProductController::class, 'destroy'])->name('destroy');
        Route::put('/update-package', [ProductController::class, 'updatePackage'])->name('updatePackage');
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

Route::prefix('campus-studentOff')
    ->name('campus-studentOff.')
    ->group(function() {
        Route::get('/', [OffStudentController::class, 'index'])->name('index');
        Route::post('/create', [OffStudentController::class, 'store'])->name('store');
        Route::get('/{studentId}', [OffStudentController::class, 'show'])->name('show');
        Route::put( '/update/{studentId}', [OffStudentController::class, 'update'])->name('update');
        Route::delete('/{studentId}', [OffStudentController::class, 'destroy'])->name('destroy');
    });

Route::prefix('campus-enrollment')
    ->name('campus-enrollment.')
    ->group(function() {
        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        Route::post('/create', [EnrollmentController::class, 'store'])->name('store');
        Route::get('/{studentId}', [EnrollmentController::class, 'show'])->name('show');
        Route::put( '/update/{studentId}', [EnrollmentController::class, 'update'])->name('update');
        Route::delete('/{studentId}', [EnrollmentController::class, 'destroy'])->name('destroy');
    });

// CAMPUS END

Route::prefix('admin-unit-management')
    ->name('admin-unit-management.')
    ->group(function() {
        Route::get('/', [UnitController::class, 'index'])->name('index');
        Route::post('/create', [UnitController::class, 'store'])->name('store');
        Route::get('/{productId}', [UnitController::class, 'show'])->name('show');
        Route::put( '/update/{productId}', [UnitController::class, 'update'])->name('update');
        Route::delete('/{productId}', [UnitController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-match-activity-management')
    ->name('admin-match-activity-management.')
    ->group(function() {
        Route::get('/', [MatchedActivityController::class, 'index'])->name('index');
        Route::post('/create', [MatchedActivityController::class, 'store'])->name('store');
        Route::get('/{matchedActivityId}', [MatchedActivityController::class, 'show'])->name('show');
        Route::put('/update/{matchedActivityId}', [MatchedActivityController::class, 'update'])->name('update');
        Route::delete('/{matchedActivityId}', [MatchedActivityController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-class-management')
    ->name('admin-class-management.')
    ->group(function() {
        Route::get('/', [ClassController::class, 'index'])->name('index');
        Route::post('/create', [ClassController::class, 'store'])->name('store');
        Route::get('/{classId}', [ClassController::class, 'show'])->name('show');
        Route::put('/update/{classId}', [ClassController::class, 'update'])->name('update');
        Route::delete('/{classId}', [ClassController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-class-match-activity-management')
    ->name('admin-class-match-activity-management.')
    ->group(function() {
        Route::get('/', [ClassMatchActivityController::class, 'index'])->name('index');
        Route::post('/create', [ClassMatchActivityController::class, 'store'])->name('store');
        Route::post('/show', [ClassMatchActivityController::class, 'show'])->name('show');
        Route::put('/update', [ClassMatchActivityController::class, 'update'])->name('update');
        Route::delete('/{classId}/{matchedActivityId}', [ClassMatchActivityController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-student-management')
    ->name('admin-student-management.')
    ->group(function() {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::post('/create', [StudentController::class, 'store'])->name('store');
        Route::get('/{studentId}', [StudentController::class, 'show'])->name('show');
        Route::put('/update/{studentId}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{studentId}', [StudentController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-parent-management')
    ->name('admin-parent-management.')
    ->group(function() {
        Route::get('/', [ParentController::class, 'index'])->name('index');
        Route::post('/create', [ParentController::class, 'store'])->name('store');
        Route::get('/{parentId}', [ParentController::class, 'show'])->name('show');
        Route::put('/update/{parentId}', [ParentController::class, 'update'])->name('update');
        Route::delete('/{parentId}', [ParentController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-enrollment')
    ->name('admin-enrollment.')
    ->group(function() {
        Route::get('/', [EnrollmentControllerA::class, 'index'])->name('index');
        Route::post('/create', [EnrollmentControllerA::class, 'store'])->name('store');
        Route::get('/{studentId}', [EnrollmentControllerA::class, 'show'])->name('show');
        Route::put('/update/{studentId}', [EnrollmentControllerA::class, 'update'])->name('update');
        Route::delete('/{studentId}', [EnrollmentControllerA::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-class-feedback')
    ->name('admin-class-feedback.')
    ->group(function() {
        Route::get('/', [ClassFeedbackController::class, 'index'])->name('index');
        Route::post('/create', [ClassFeedbackController::class, 'store'])->name('store');
        Route::get('/show', [ClassFeedbackController::class, 'show'])->name('show');
        Route::put('/update', [ClassFeedbackController::class, 'update'])->name('update');
        Route::delete('/{studentId}', [ClassFeedbackController::class, 'destroy'])->name('destroy');
    });