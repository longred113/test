<?php

use App\Http\Controllers\api\Admin_Dashboard\AdminController;
use App\Http\Controllers\api\Admin_Dashboard\CampusController;
use App\Http\Controllers\api\Admin_Dashboard\CampusManagerController;
use App\Http\Controllers\api\Admin_Dashboard\ClassBoardController;
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
use App\Http\Controllers\api\Admin_Dashboard\ClassReportController;
use App\Http\Controllers\api\Admin_Dashboard\ClassFeedbackController;
use App\Http\Controllers\api\Admin_Dashboard\ClassMaterialController;
use App\Http\Controllers\api\Admin_Dashboard\ClassProductController;
use App\Http\Controllers\api\Admin_Dashboard\ClassTimeSlotController;
use App\Http\Controllers\api\Admin_Dashboard\EnrollmentControllerA;
use App\Http\Controllers\api\Admin_Dashboard\ImageController;
use App\Http\Controllers\api\Admin_Dashboard\ProductEnrollmentController;
use App\Http\Controllers\api\Admin_Dashboard\ProductMatchedActivityController;
use App\Http\Controllers\api\Admin_Dashboard\ProductPackageController;
use App\Http\Controllers\api\Admin_Dashboard\StudentClassController;
use App\Http\Controllers\api\Admin_Dashboard\StudentEnrollmentController;
use App\Http\Controllers\api\Admin_Dashboard\StudentMatchedActivityController;
use App\Http\Controllers\api\Admin_Dashboard\StudentProductController;
use App\Http\Controllers\api\Campus_Dashboard\OffStudentController;
use App\Http\Controllers\api\Campus_Dashboard\OffTeachController;
use App\Http\Controllers\api\Campus_Dashboard\EnrollmentController;
use App\Http\Controllers\api\Message_Dashboard\PrivateMessageController;
use App\Http\Controllers\api\Teacher_Dashboard\StudyPlannerController;
use App\Http\Controllers\api\Student_Dashboard\StudyPlannerSController;
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

// START ADMIN DASHBOARD
Route::prefix('dash-board-admin')
    ->name('dash-board-admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/create', [AdminController::class, 'store'])->name('store');
        Route::get('/{adminId}', [AdminController::class, 'show'])->name('show');
        Route::put('/update/{adminId}', [AdminController::class, 'update'])->name('update');
        Route::delete('/{adminId}', [AdminController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-campus-management')
    ->name('admin-campus-management.')
    ->group(function () {
        Route::get('/', [CampusController::class, 'index'])->name('index');
        Route::post('/create', [CampusController::class, 'store'])->name('store');
        Route::get('/{campusId}', [CampusController::class, 'show'])->name('show');
        Route::put('/update/{campusId}', [CampusController::class, 'update'])->name('update');
        Route::delete('/{campusId}', [CampusController::class, 'destroy'])->name('destroy');
        Route::post('/change-active', [CampusController::class, 'switchActivate'])->name('switchActivate');
    });

Route::prefix('admin-campus-manger')
    ->name('admin-campus-manger.')
    ->group(function () {
        Route::get('/', [CampusManagerController::class, 'index'])->name('index');
        Route::post('/create', [CampusManagerController::class, 'store'])->name('store');
        Route::get('/{campusManagerId}', [CampusManagerController::class, 'show'])->name('show');
        Route::get('/get-campus-manager/{campusId}', [CampusManagerController::class, 'getCampusManagerByCampus'])->name('getCampusManagerByCampus');
        Route::put('/update/{campusManagerId}', [CampusManagerController::class, 'update'])->name('update');
        Route::delete('/{campusManagerId}', [CampusManagerController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-role-management')
    ->name('admin-role-management.')
    ->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/create', [RoleController::class, 'store'])->name('store');
        Route::get('/{roleId}', [RoleController::class, 'show'])->name('show');
        Route::put('/update/{roleId}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{roleId}', [RoleController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-teacher-management')
    ->name('admin-teacher-management')
    ->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->name('index');
        Route::get('/get-data-before-update', [TeacherController::class, 'getTeacherDataBeforeUpdate'])->name('getTeacherDataBeforeUpdate');
        Route::get('/all-online-teacher', [TeacherController::class, 'getAllOnlineTeacher'])->name('getAllOnlineTeacher');
        Route::post('/create', [TeacherController::class, 'store'])->name('store');
        Route::get('/online-teacher', [TeacherController::class, 'showOnlineTeacher'])->name('showOnlineTeacher');
        Route::get('/{teacherId}', [TeacherController::class, 'show'])->name('show');
        Route::put('/update/{teacherId}', [TeacherController::class, 'update'])->name('update');
        Route::delete('/{teacherId}', [TeacherController::class, 'destroy'])->name('destroy');
        Route::post('/', [TeacherController::class, 'multiDeleteTeacher'])->name('multiDeleteTeacher');
    });

Route::prefix('admin-package-management')
    ->name('admin-package-management.')
    ->group(function () {
        Route::get('/', [PackagesController::class, 'index'])->name('index');
        Route::post('/create', [PackagesController::class, 'store'])->name('store');
        Route::get('/{packageId}', [PackagesController::class, 'show'])->name('show');
        Route::post('/update/{packageId}', [PackagesController::class, 'update'])->name('update');
        Route::delete('/{packageId}', [PackagesController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-product-management')
    ->name('admin-product-management.')
    ->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/all-product-and-package', [ProductController::class, 'getAllProductHavePackage'])->name('getAllProductHavePackage');
        Route::get('/all-product-and-match-activity', [ProductController::class, 'getProductAndMatchActivity'])->name('getProductAndMatchActivity');
        Route::post('/create', [ProductController::class, 'store'])->name('store');
        Route::get('/{productId}', [ProductController::class, 'show'])->name('show');
        Route::put('/update/{productId}', [ProductController::class, 'update'])->name('update');
        Route::put('/add-package/{productId}', [ProductController::class, 'addPackages'])->name('addPackages');
        Route::delete('/{productId}', [ProductController::class, 'destroy'])->name('destroy');
        Route::put('/update-package', [ProductController::class, 'updatePackage'])->name('updatePackage');
    });

Route::prefix('admin-unit-management')
    ->name('admin-unit-management.')
    ->group(function () {
        Route::get('/', [UnitController::class, 'index'])->name('index');
        Route::post('/create', [UnitController::class, 'store'])->name('store');
        Route::get('/{unitId}', [UnitController::class, 'show'])->name('show');
        Route::put('/update/{unitId}', [UnitController::class, 'update'])->name('update');
        Route::delete('/{unitId}', [UnitController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-match-activity-management')
    ->name('admin-match-activity-management.')
    ->group(function () {
        Route::get('/', [MatchedActivityController::class, 'index'])->name('index');
        Route::post('/create', [MatchedActivityController::class, 'store'])->name('store');
        Route::get('/{matchedActivityId}', [MatchedActivityController::class, 'show'])->name('show');
        Route::get('/get-match-activity/{productId}', [MatchedActivityController::class, 'getMatchActivityFromProduct'])->name('getMatchActivityFromProduct');
        Route::put('/update/{matchedActivityId}', [MatchedActivityController::class, 'update'])->name('update');
        Route::delete('/{matchedActivityId}', [MatchedActivityController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-class-management')
    ->name('admin-class-management.')
    ->group(function () {
        Route::get('/', [ClassController::class, 'index'])->name('index');
        Route::post('/create', [ClassController::class, 'store'])->name('store');
        Route::get('/{classId}', [ClassController::class, 'show'])->name('show');
        Route::get('/get-class/{onlineTeacher}', [ClassController::class, 'getClassFromTeacher'])->name('getClassFromTeacher');
        Route::put('/update/{classId}', [ClassController::class, 'update'])->name('update');
        Route::delete('/{classId}', [ClassController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-class-match-activity-management')
    ->name('admin-class-match-activity-management.')
    ->group(function () {
        Route::get('/', [ClassMatchActivityController::class, 'index'])->name('index');
        Route::post('/create', [ClassMatchActivityController::class, 'store'])->name('store');
        Route::post('/{classMatchActivityId}', [ClassMatchActivityController::class, 'show'])->name('show');
        Route::get('/display-by-class/{classId}', [ClassMatchActivityController::class, 'displayByClass'])->name('displayByClass');
        Route::get('/display-by-matched-activity/{matchedActivityId}', [ClassMatchActivityController::class, 'displayByMatchActivity'])->name('displayByMatchActivity');
        Route::put('/update/{classMatchActivityId}', [ClassMatchActivityController::class, 'update'])->name('update');
        Route::delete('/{classMatchActivityId}', [ClassMatchActivityController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-student-management')
    ->name('admin-student-management.')
    ->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/joined', [StudentController::class, 'studentJoinedList'])->name('studentJoinedList');
        Route::get('/withdrawal', [StudentController::class, 'studentWithdrawalList'])->name('studentWithdrawalList');
        Route::get('/get-product-and-match-activity', [StudentController::class, 'viewStudentProductAndStudyPlanner'])->name('viewStudentProductAndStudyPlanner');
        Route::get('/student-with-id/{studentId}', [StudentController::class, 'getStudentWithId'])->name('getStudentWithId');
        Route::get('/get-student-online', [StudentController::class, 'getStudentOnline'])->name('getStudentOnline');
        Route::post('/create', [StudentController::class, 'store'])->name('store');
        Route::get('/{studentId}', [StudentController::class, 'show'])->name('show');
        Route::put('/update-enrollment-count/{studentId}', [StudentController::class, 'updateEnrollmentCount'])->name('updateEnrollmentCount');
        Route::put('/update/{studentId}', [StudentController::class, 'update'])->name('update');
        Route::put('/update-withdrawal/{studentId}', [StudentController::class, 'updateWithdrawalStudent'])->name('updateWithdrawalStudent');
        Route::delete('/{studentId}', [StudentController::class, 'destroy'])->name('destroy');
        Route::get('/get-student/{parentId}', [StudentController::class, 'getStudentByParent'])->name('getStudentByParent');
        Route::get('/get-enrollment-count/{studentId}', [StudentController::class, 'getEnrollmentCount'])->name('getEnrollmentCount');
        Route::get('/get-by-campus/{campusId}', [StudentController::class, 'getStudentByCampus'])->name('getStudentByCampus');
    });

Route::prefix('admin-parent-management')
    ->name('admin-parent-management.')
    ->group(function () {
        Route::get('/', [ParentController::class, 'index'])->name('index');
        Route::post('/create', [ParentController::class, 'store'])->name('store');
        Route::get('/{parentId}', [ParentController::class, 'show'])->name('show');
        Route::put('/update/{parentId}', [ParentController::class, 'update'])->name('update');
        Route::delete('/{parentId}', [ParentController::class, 'destroy'])->name('destroy');
        Route::post('/add-into-student/{parentId}', [ParentController::class, 'addParentIntoStudent'])->name('addParentIntoStudent');
    });

Route::prefix('admin-enrollment')
    ->name('admin-enrollment.')
    ->group(function () {
        Route::get('/', [EnrollmentControllerA::class, 'index'])->name('index');
        Route::get('/Enroll/{level}', [EnrollmentControllerA::class, 'Enrollmentshow'])->name('Enrollmentshow');
        Route::get('/Enrolls/{product}', [EnrollmentControllerA::class, 'showErollmentByPro'])->name('showErollmentByPro');
        Route::get('/get-enrollment/{campusId}', [EnrollmentControllerA::class, 'getEnrollment'])->name('getEnrollment');
        Route::get('/get-history', [EnrollmentControllerA::class, 'enrollmentHistory'])->name('enrollmentHistory');
        Route::get('/get-product-and-student', [EnrollmentControllerA::class, 'getEnrollmentHaveProductAndStudent'])->name('getEnrollmentHaveProductAndStudent');
        Route::post('/create', [EnrollmentControllerA::class, 'store'])->name('store');
        Route::get('/{studentId}', [EnrollmentControllerA::class, 'show'])->name('show');
        Route::put('/update/{enrollmentId}', [EnrollmentControllerA::class, 'update'])->name('update');
        Route::put('/update-history/{enrollmentId}', [EnrollmentControllerA::class, 'updateEnrollmentHistory'])->name('updateEnrollmentHistory');
        Route::delete('/{studentId}', [EnrollmentControllerA::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-class-feedback')
    ->name('admin-class-feedback.')
    ->group(function () {
        Route::get('/', [ClassFeedbackController::class, 'index'])->name('index');
        Route::post('/create', [ClassFeedbackController::class, 'store'])->name('store');
        Route::get('/{classFeedbackId}', [ClassFeedbackController::class, 'show'])->name('show');
        Route::put('/update/{classFeedbackId}', [ClassFeedbackController::class, 'update'])->name('update');
        Route::delete('/{classFeedbackId}', [ClassFeedbackController::class, 'destroy'])->name('destroy');
        Route::get('/show-feedback-of-one-teach/{teacherId}', [ClassFeedbackController::class, 'showClassFeedbackOfOneTeacher'])->name('showClassFeedbackOfOneTeacher');
    });

Route::prefix('admin-user-management')
    ->name('admin-user-management.')
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/create', [UserController::class, 'store'])->name('store');
        Route::post('/create-new-user', [UserController::class, 'createUserAccount'])->name('createUserAccount');
        Route::get('/{userId}', [UserController::class, 'show'])->name('show');
        Route::get('/get-by-teacher/{teacherId}', [UserController::class, 'getUserFromTeacher'])->name('getUserFromTeacher');
        Route::get('/get-by-student/{studentId}', [UserController::class, 'getUserFromStudent'])->name('getUserFromStudent');
        Route::get('/get-by-parent/{parentId}', [UserController::class, 'getUserFromParent'])->name('getUserFromParent');
        Route::get('/get-by-campus/{campusId}', [UserController::class, 'getUserFromCampus'])->name('getUserFromCampus');
        Route::put('/update/{userId}', [UserController::class, 'update'])->name('update');
        Route::delete('/{userId}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/update-check-login/{userId}', [UserController::class, 'checkLogin'])->name('checkLogin');
        Route::post('/check-information', [UserController::class, 'checkInfoLogin'])->name('checkInfoLogin');
        Route::post('/export-user', [UserController::class, 'exportUser'])->name('exportUser');
    });

Route::prefix('admin-student-class-management')
    ->name('admin-student-class-management.')
    ->group(function () {
        Route::get('/', [StudentClassController::class, 'index'])->name('index');
        Route::post('/create', [StudentClassController::class, 'store'])->name('store');
        Route::get('/{studentClassId}', [StudentClassController::class, 'show'])->name('show');
        Route::get('/get-student/{classId}', [StudentClassController::class, 'getStudentFromClass'])->name('getStudentFromClass');
        Route::post('/get-class', [StudentClassController::class, 'getClassFromStudent'])->name('getClassFromStudent');
        Route::put('/update/{studentClassId}', [StudentClassController::class, 'update'])->name('update');
        Route::delete('/{studentClassId}', [StudentClassController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-class-board-management')
    ->name('admin-class-board-management.')
    ->group(function () {
        Route::get('/', [ClassBoardController::class, 'index'])->name('index');
        Route::post('/create', [ClassBoardController::class, 'store'])->name('store');
        Route::post('/send-message', [ClassBoardController::class, 'sendMessage'])->name('sendMessage');
        Route::get('/{classBoardId}', [ClassBoardController::class, 'show'])->name('show');
        Route::get('/get-student-announcement/{studentId}', [ClassBoardController::class, 'getStudentAnnouncement'])->name('getStudentAnnouncement');
        Route::get('/get-teacher-announcement/{teacherId}', [ClassBoardController::class, 'getTeacherAnnouncement'])->name('getTeacherAnnouncement');
        Route::put('/update/{classBoardId}', [ClassBoardController::class, 'update'])->name('update');
        Route::delete('/{classBoardId}', [ClassBoardController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-class-material-management')
    ->name('admin-class-material-management.')
    ->group(function () {
        Route::get('/', [ClassMaterialController::class, 'index'])->name('index');
        Route::post('/create', [ClassMaterialController::class, 'store'])->name('store');
        Route::get('/{classMaterialId}', [ClassMaterialController::class, 'show'])->name('show');
        Route::put('/update/{classMaterialId}', [ClassMaterialController::class, 'update'])->name('update');
        Route::delete('/{classMaterialId}', [ClassMaterialController::class, 'destroy'])->name('destroy');
    });
Route::prefix('admin-class-report')
    ->name('admin-class-report.')
    ->group(function () {
        Route::get('/', [ClassReportController::class, 'index'])->name('index');
        Route::post('/create', [ClassReportController::class, 'store'])->name('store');
        Route::get('/{classReportId}', [ClassReportController::class, 'show'])->name('show');
        Route::put('/update/{classReportId}', [ClassReportController::class, 'update'])->name('update');
        Route::delete('/{classReportId}', [ClassReportController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-class-product-management')
    ->name('admin-class-product-management.')
    ->group(function () {
        Route::get('/', [ClassProductController::class, 'index'])->name('index');
        Route::post('/create', [ClassProductController::class, 'store'])->name('store');
        Route::get('/{classProductId}', [ClassProductController::class, 'show'])->name('show');
        Route::get('/display-by-class/{classId}', [ClassProductController::class, 'displayByClass'])->name('displayByClass');
        Route::get('/display-by-product/{productId}', [ClassProductController::class, 'displayByProduct'])->name('displayByProduct');
        Route::put('/update/{classProductId}', [ClassProductController::class, 'update'])->name('update');
        Route::delete('/{classProductId}', [ClassProductController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-product-package-management')
    ->name('admin-product-package-management.')
    ->group(function () {
        Route::get('/', [ProductPackageController::class, 'index'])->name('index');
        Route::post('/create', [ProductPackageController::class, 'store'])->name('store');
        Route::get('/{productPackageId}', [ProductPackageController::class, 'show'])->name('show');
        Route::get('/display-by-product/{productId}', [ProductPackageController::class, 'displayByProduct'])->name('displayByProduct');
        Route::get('/display-by-package/{packageId}', [ProductPackageController::class, 'displayByPackage'])->name('displayByPackage');
        Route::put('/update/{productPackageId}', [ProductPackageController::class, 'update'])->name('update');
        Route::delete('/{productPackageId}', [ProductPackageController::class, 'destroy'])->name('destroy');
    });

Route::prefix('admin-student-product-management')
    ->name('admin-student-product-management.')
    ->group(function () {
        Route::get('/', [StudentProductController::class, 'index'])->name('index');
        Route::post('/create', [StudentProductController::class, 'store'])->name('store');
        Route::get('/{studentProductId}', [StudentProductController::class, 'show'])->name('show');
        Route::get('/display-by-product/{productId}', [StudentProductController::class, 'displayByProductId'])->name('getWithProductId');
        Route::get('/display-by-student/{studentId}', [StudentProductController::class, 'displayByStudentId'])->name('getWithStudentId');
        Route::get('/get-info-product/{studentId}', [StudentProductController::class, 'getInfoStudentFromProductId'])->name('getInfoStudentFromProductId');
        Route::put('/update/{studentProductId}', [StudentProductController::class, 'update'])->name('update');
        Route::delete('/{studentProductId}', [StudentProductController::class, 'destroy'])->name('destroy');
        Route::post('/update-product-of-student', [StudentProductController::class, 'updateProductOfStudent'])->name('updateProductOfStudent');
    });

Route::prefix('admin-student-matched-activity-management')
    ->name('admin-student-matched-activity-management.')
    ->group(function () {
        Route::get('/', [StudentMatchedActivityController::class, 'index'])->name('index');
        Route::post('/create', [StudentMatchedActivityController::class, 'store'])->name('store');
        Route::get('/{studentMatchedActivityId}', [StudentMatchedActivityController::class, 'show'])->name('show');
        Route::get('/get-from-student/{studentId}', [StudentMatchedActivityController::class, 'getMatchedActivityFromStudent'])->name('getMatchedActivityFromStudent');
        Route::post('/update-activate', [StudentMatchedActivityController::class, 'updateActivate'])->name('updateActivate');
        Route::put('/update/{studentMatchedActivityId}', [StudentMatchedActivityController::class, 'update'])->name('update');
        Route::delete('/{studentMatchedActivityId}', [StudentMatchedActivityController::class, 'destroy'])->name('destroy');
        Route::post('/update-match-activity-of-student', [StudentMatchedActivityController::class, 'updateMatchActivityOfStudent'])->name('updateMatchActivityOfStudent');
    });

Route::prefix('admin-student-enrollment-management')
    ->name('admin-student-enrollment-management.')
    ->group(function () {
        Route::get('/', [StudentEnrollmentController::class, 'index'])->name('index');
        Route::post('/create', [StudentEnrollmentController::class, 'store'])->name('store');
        Route::get('/{studentEnrollmentId}', [StudentEnrollmentController::class, 'show'])->name('show');
        Route::get('/get-student/{enrollmentId}', [StudentEnrollmentController::class, 'getStudent'])->name('getStudent');
        Route::get('/get-student-by-enrollment/{enrollmentId}', [StudentEnrollmentController::class, 'getStudentByEnrollment'])->name('getStudentByEnrollment');
        Route::post('/update-check', [StudentEnrollmentController::class, 'updateCheck'])->name('updateCheck');
        Route::put('/update-enrollment', [StudentEnrollmentController::class, 'updateEnrollment'])->name('updateEnrollment');
        Route::post('/update-student', [StudentEnrollmentController::class, 'updateStudentOfEnrollment'])->name('updateStudentOfEnrollment');
        Route::delete('/{studentEnrollmentId}', [StudentEnrollmentController::class, 'destroy'])->name('destroy');
    });    

Route::prefix('admin-product-enrollment-management')
    ->name('admin-product-enrollment-management.')
    ->group(function () {
        Route::get('/', [ProductEnrollmentController::class, 'index'])->name('index');
        Route::post('/create', [ProductEnrollmentController::class, 'store'])->name('store');
        Route::get('/{productEnrollmentId}', [ProductEnrollmentController::class, 'show'])->name('show');
        Route::post('/get-product', [ProductEnrollmentController::class, 'getProduct'])->name('getProduct');
        Route::put('/update/{productEnrollmentId}', [ProductEnrollmentController::class, 'update'])->name('update');
        Route::post('/update-product',[ProductEnrollmentController::class, 'updateProductOfEnrollment'])->name('updateProductOfEnrollment');
        Route::delete('/{productEnrollmentId}', [ProductEnrollmentController::class, 'destroy'])->name('destroy');
    }); 

Route::prefix('admin-image-management')
    ->name('admin-image-management.')
    ->group(function () {
        Route::get('/', [ImageController::class, 'index'])->name('index');
        Route::post('/create-image', [ImageController::class, 'store'])->name('store');
        Route::post('/update-image/{id}', [ImageController::class, 'update'])->name('update');
        Route::delete('/delete-image', [ImageController::class, 'destroy'])->name('destroy');
        Route::get('/get-student-image', [ImageController::class, 'getStudentImage'])->name('getStudentImage');
        Route::get('/get-teacher-image', [ImageController::class, 'getTeacherImage'])->name('getTeacherImage');
    });

Route::prefix('admin-product-match-activity-management')
    ->name('admin-product-match-activity-management.')
    ->group(function () {
        Route::get('/', [ProductMatchedActivityController::class, 'index'])->name('index');
        Route::get('/get-match-activity/{productId}', [ProductMatchedActivityController::class, 'getMatchedActivitiesByProductId'])->name('getMatchedActivitiesByProductId');
        Route::post('/create', [ProductMatchedActivityController::class, 'store'])->name('store');
        Route::post('/update-matched-activity', [ProductMatchedActivityController::class, 'updateMatchedActivity'])->name('updateMatchedActivity');
    });  

Route::prefix('admin-class-time-slot-management')
    ->name('admin-class-time-slot-management.')
    ->group(function () {
        Route::get('/', [ClassTimeSlotController::class, 'index'])->name('index');
        Route::post('/create', [ClassTimeSlotController::class, 'store'])->name('store');
        Route::get('/{classTimeSlotId}', [ClassTimeSlotController::class, 'show'])->name('show');
        Route::put('/update/{classTimeSlotId}', [ClassTimeSlotController::class, 'update'])->name('update');
        Route::delete('/{classTimeSlotId}', [ClassTimeSlotController::class, 'destroy'])->name('destroy');
    });
//END ADMIN DASHBOARD

// START CAMPUS DASHBOARD
Route::prefix('campus-teacherOff')
    ->name('campus-teacherOff.')
    ->group(function () {
        Route::get('/', [OffTeachController::class, 'index'])->name('index');
        Route::post('/create', [OffTeachController::class, 'store'])->name('store');
        Route::get('/{teacherId}', [OffTeachController::class, 'show'])->name('show');
        Route::put('/update/{teacherId}', [OffTeachController::class, 'update'])->name('update');
        Route::delete('/{teacherId}', [OffTeachController::class, 'destroy'])->name('destroy');
    });

Route::prefix('campus-studentOff')
    ->name('campus-studentOff.')
    ->group(function () {
        Route::get('/', [OffStudentController::class, 'index'])->name('index');
        Route::post('/create', [OffStudentController::class, 'store'])->name('store');
        Route::get('/{studentId}', [OffStudentController::class, 'show'])->name('show');
        Route::get('/campus-student/{campusId}', [OffStudentController::class, 'showStudentWithCampus'])->name('showStudentWithCampus');
        Route::put('/update/{studentId}', [OffStudentController::class, 'update'])->name('update');
        Route::delete('/{studentId}', [OffStudentController::class, 'destroy'])->name('destroy');
    });

Route::prefix('campus-enrollment')
    ->name('campus-enrollment.')
    ->group(function () {
        Route::get('/{campusId}/{productId}', [EnrollmentController::class, 'index'])->name('index');
        Route::post('/create', [EnrollmentController::class, 'store'])->name('store');
        Route::get('/showEnroll/{level}', [EnrollmentController::class, 'showErollment'])->name('showErollment');
        Route::get('/showEnrolls/{level}/{productId}', [EnrollmentController::class, 'showEnrollmentByPro'])->name('showEnrollmentByPro');
        Route::get('/{campusId}', [EnrollmentController::class, 'show'])->name('show');
        Route::put('/update/{enrollmentId}', [EnrollmentController::class, 'update'])->name('update');
        Route::delete('/{studentId}', [EnrollmentController::class, 'destroy'])->name('destroy');
        Route::get('/', [EnrollmentController::class, 'getAllEnrollment'])->name('getAllEnrollment');
    });
// CAMPUS DASHBOARD END

//START TEACHER DASHBOARD
Route::prefix('teacher-study-planner')
    ->name('teacher-study-planner.')
    ->group(function () {
        Route::get('/{teacherId}', [StudyPlannerController::class, 'showClasses'])->name('showClasses');
        Route::post('/create', [StudyPlannerController::class, 'store'])->name('store');
        Route::get('/show/{classId}', [StudyPlannerController::class, 'showStudentInClass'])->name('showStudentInClass');
        Route::get('/to-do-list/{classId}/{studentId}', [StudyPlannerController::class, 'getToDoListOfStudent'])->name('getToDoListOfStudent');
        Route::put('/update-status/{studentMatchedActivityId}', [StudyPlannerController::class, 'updateStatusOfStudyPlanner'])->name('updateStatusOfStudyPlanner');
        Route::put('/update', [StudyPlannerController::class, 'update'])->name('update');
        Route::delete('/', [StudyPlannerController::class, 'destroy'])->name('destroy');
    });
//END TEACHER DASHBOARD

//START STUDENT DASHBOARD
Route::prefix('student-study-planner')
    ->name('student-study-planner.')
    ->group(function () {
        Route::get('/{studentId}', [StudyPlannerSController::class, 'showStuClass'])->name('showStuClass');
        Route::get('/StudyPlanner/{studentId}', [StudyPlannerSController::class, 'showStudyPlanner'])->name('showStudyPlanner');
        Route::post('/create', [StudyPlannerSController::class, 'store'])->name('store');
        Route::get('/show/{classId}', [StudyPlannerSController::class, 'showStudentInClass'])->name('showStudentInClass');
        // Route::put('/update-status-study-planner/{matchedActivityId}', [StudyPlannerSController::class, 'showStudentInClass'])->name('showStudentInClass');
        Route::put('/update', [StudyPlannerSController::class, 'update'])->name('update');
        Route::delete('/', [StudyPlannerSController::class, 'destroy'])->name('destroy');
    });
//END STUDENT DASHBOARD

//START MESSAGE DASHBOARD
Route::prefix('private-message')
    ->name('private-message.')
    ->group(function () {
        Route::post('/private-message', [PrivateMessageController::class, 'sendMessages'])->name('sendMessages');
        Route::get('/get-all-message', [PrivateMessageController::class, 'getAllMessage'])->name('getAllMessage');
        Route::get('/get-student-message/{studentId}', [PrivateMessageController::class, 'getStudentMessage'])->name('getStudentMessage');
        Route::get('/get-teacher-message/{teacherId}', [PrivateMessageController::class, 'getTeacherMessage'])->name('getTeacherMessage');
        Route::get('/get-admin-message', [PrivateMessageController::class, 'getAdminMessage'])->name('getAdminMessage');
        Route::post('/create', [PrivateMessageController::class, 'store'])->name('store');
    });

//END MESSAGE DASHBOARD