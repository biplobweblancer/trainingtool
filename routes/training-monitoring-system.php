<?php

use App\Http\Controllers\Api\TrainingMonitoring\CategoryController;
use App\Http\Controllers\Api\TrainingMonitoring\DashboardController;
use App\Http\Controllers\Api\TrainingMonitoring\LoginController;
use App\Http\Controllers\Api\TrainingMonitoring\ProfileController;
use App\Http\Controllers\Api\TrainingMonitoring\RegisterController;
use App\Http\Controllers\Api\TrainingMonitoring\RoleController;
use App\Http\Controllers\Api\TrainingMonitoring\SubCategoryController;
use App\Http\Controllers\Api\TrainingMonitoring\CheckDbController;
use App\Http\Controllers\Api\TrainingMonitoring\UserController;
use App\Http\Controllers\Api\TrainingMonitoring\AdminController;
use App\Http\Controllers\Api\TrainingMonitoring\BatchController;
use App\Http\Controllers\Api\TrainingMonitoring\DivisionController;
use App\Http\Controllers\Api\TrainingMonitoring\DistrictController;
use App\Http\Controllers\Api\TrainingMonitoring\UpazilaController;
use App\Http\Controllers\Api\TrainingMonitoring\ProviderController;
use App\Http\Controllers\Api\TrainingMonitoring\CommitteeController;
use App\Http\Controllers\Api\TrainingMonitoring\PreliminarySelectionController;
use App\Http\Controllers\Api\TrainingMonitoring\ProviderBatchesController;
use App\Http\Controllers\Api\TrainingMonitoring\TraineeEnrollController;
use App\Http\Controllers\Api\TrainingMonitoring\TrainingBatchScheduleController;
use App\Http\Controllers\Api\TrainingMonitoring\TrainerController;
use App\Http\Controllers\Api\TrainingMonitoring\TrainerEnrollController;
use App\Http\Controllers\Api\TrainingMonitoring\PermissionController;
use App\Http\Controllers\Api\TrainingMonitoring\InspectionController;
use App\Http\Controllers\Api\TrainingMonitoring\AttendanceController;
use App\Http\Controllers\Api\TrainingMonitoring\CoordinatorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

/* user registration Api Routes */
Route::post('/register', [RegisterController::class, 'register'])->middleware('localization');
/* New Email Verification Link Routes And Admin Dashboard Routes */
Route::get('/account/verify/{token}', [RegisterController::class, 'verifyAccount'])->name('user.verify');
// change the location languages
Route::get('/language', [LoginController::class, 'index'])->middleware('localization');
/* Login Api Routes */
Route::post('/login', [LoginController::class, 'login'])->middleware('localization');

/* Login Api for role permissions */
Route::get('/role-permissions/{profileId}', [LoginController::class, 'rolePermissionAccess'])->middleware('localization');
/* Logout Api Routes */
Route::get('/logout', [LoginController::class, 'logout'])->middleware("auth:soms", "localization");

/* Role Api Routes */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'role'], function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/create', [RoleController::class, 'create']);
    Route::get('/{tmsRole}/show', [RoleController::class, 'show']);
    Route::get('/{tmsRole}/edit', [RoleController::class, 'edit']);
    Route::patch('/{tmsRole}/update', [RoleController::class, 'update']);
    Route::get('/{tmsRole}/delete', [RoleController::class, 'destroy']);
});


/* Profile Routes */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'profile'], function () {
    Route::get('/', [ProfileController::class, 'index']);
    Route::patch('/{user}/update', [ProfileController::class, 'update']);
});

/**
 * User Api Routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'users'], function () {
    Route::match(['get', 'post'], '/', [UserController::class, 'index']);
    Route::get('/user/{userId}', [UserController::class, 'show']);
    Route::post('/preliminary/select', [UserController::class, 'preliminary_select']);
});

/**
 * User Api Routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'preliminary-selected'], function () {
    Route::match(['get', 'post'], '/', [PreliminarySelectionController::class, 'index']);
    Route::get('/user/{userId}', [PreliminarySelectionController::class, 'show']);
    Route::post('/final/select', [PreliminarySelectionController::class, 'final_select']);
});

/**
 * User Api Routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'admins'], function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::post('/create', [AdminController::class, 'store']);
    Route::get('/admin/{userProfileId}', [AdminController::class, 'show']);
    Route::get('/{tmsUserType}/edit', [AdminController::class, 'edit']);
    Route::patch('/{tmsUserType}/update', [AdminController::class, 'update']);
    Route::get('/{tmsUserType}/delete', [AdminController::class, 'destroy']);
});

/**
 * Categories api routes
 */
Route::group(['middleware' => ['auth:soms', 'localization'], 'prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/create', [CategoryController::class, 'store']);
    Route::get('/{category}/show', [CategoryController::class, 'show']);
    Route::get('/{category}/edit', [CategoryController::class, 'edit']);
    Route::patch('/{category}/update', [CategoryController::class, 'update']);
    Route::get('/{category}/delete', [CategoryController::class, 'destroy']);
});

/**
 * Sub Categories api routes
 */
Route::group(['middleware' => ['auth:soms', 'localization'], 'prefix' => 'subcategories'], function () {
    Route::get('/{category_id?}', [SubCategoryController::class, 'index']);
    Route::post('/create', [SubCategoryController::class, 'store']);
    Route::get('/{subCategory}/show', [SubCategoryController::class, 'show']);
    Route::get('/{subCategory}/edit', [SubCategoryController::class, 'edit']);
    Route::patch('/{subCategory}/update', [SubCategoryController::class, 'update']);
    Route::get('/{subCategory}/delete', [SubCategoryController::class, 'destroy']);
});

/**
 * Division api routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'divisions'], function () {
    Route::get('/', [DivisionController::class, 'index']);
    Route::post('/create', [DivisionController::class, 'store']);
    Route::get('/{division}/show', [DivisionController::class, 'show']);
    Route::get('/{division}/edit', [DivisionController::class, 'edit']);
    Route::patch('/{division}/update', [DivisionController::class, 'update']);
    Route::get('/{division}/delete', [DivisionController::class, 'destroy']);
});

/**
 * Districts api routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'districts'], function () {
    Route::get('/{division_id?}', [DistrictController::class, 'index']);
    Route::post('/create', [DistrictController::class, 'store']);
    Route::get('/{district}/show', [DistrictController::class, 'show']);
    Route::get('/{district}/edit', [DistrictController::class, 'edit']);
    Route::patch('/{district}/update', [DistrictController::class, 'update']);
    Route::get('/{district}/delete', [DistrictController::class, 'destroy']);
});

/**
 * Upazila api routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'upazilas'], function () {
    Route::get('/{district_id?}', [UpazilaController::class, 'index']);
    Route::post('/create', [UpazilaController::class, 'store']);
    Route::get('/{upazila}/show', [UpazilaController::class, 'show']);
    Route::get('/{upazila}/edit', [UpazilaController::class, 'edit']);
    Route::patch('/{upazila}/update', [UpazilaController::class, 'update']);
    Route::get('/{upazila}/delete', [UpazilaController::class, 'destroy']);
});

/**
 * Providers api routes
 */
Route::group(['middleware' => ['auth:soms', 'localization'], 'prefix' => 'providers'], function () {
    Route::get('/', [ProviderController::class, 'index']);
    Route::post('/create', [ProviderController::class, 'store']);
    Route::get('/{tmsProvider}/show', [ProviderController::class, 'show']);
    Route::get('/{tmsProvider}/edit', [ProviderController::class, 'edit']);
    Route::patch('/{tmsProvider}/update', [ProviderController::class, 'update']);
    Route::get('/{tmsProvider}/delete', [ProviderController::class, 'destroy']);
    Route::get('/all', [ProviderController::class, 'providerBatches']);
});

/**
 * committees api routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'committees'], function () {
    Route::get('/', [CommitteeController::class, 'index']);
    Route::post('/create', [CommitteeController::class, 'store']);
    Route::get('/{committee}/show', [CommitteeController::class, 'show']);
    Route::get('/{committee}/edit', [CommitteeController::class, 'edit']);
    Route::patch('/{committee}/update', [CommitteeController::class, 'update']);
    Route::get('/{committee}/delete', [CommitteeController::class, 'destroy']);
});

/**
 * Batches api routes
 */
Route::group(['middleware' => ['auth:soms', 'localization'], 'prefix' => 'batches'], function () {
    Route::get('/', [BatchController::class, 'index']);
    Route::post('/create', [BatchController::class, 'store']);
    Route::get('/all', [BatchController::class, 'allBatches']);
    Route::get('/{id}/show', [BatchController::class, 'batchShow']);
    Route::get('/provider', [ProviderBatchesController::class, 'index']);
    Route::get('/{provider}/edit', [BatchController::class, 'edit']);
    Route::patch('/{provider}/update', [BatchController::class, 'update']);
    Route::get('/{provider}/delete', [BatchController::class, 'destroy']);
});

/**
 * Provider Batches api routes   trainers
 */
Route::group(['middleware' => ['auth:soms', 'localization'], 'prefix' => 'provider-batches'], function () {
    Route::post('/create', [ProviderBatchesController::class, 'store']);
});

/**
 * attendance
 */
Route::group(['middleware' => ['auth:soms', 'localization'], 'prefix' => 'batch-schedule'], function () {
    Route::post('/create', [TrainingBatchScheduleController::class, 'store']);
    Route::get('/{id}/myClass', [TrainingBatchScheduleController::class, 'myClass']);
    Route::get('/{id}/checkAttendance', [TrainingBatchScheduleController::class, 'checkAttendance']);
    Route::post('/{id}/counterAttendance', [TrainingBatchScheduleController::class, 'counterAttendance']);
});

/**
 * Trainee Enroll With Batches api routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'trainee-enroll'], function () {
    Route::get('/', [TraineeEnrollController::class, 'index']);
    Route::get('/{trainingApplicant}/show', [TraineeEnrollController::class, 'show']);
});

/**
 * trainers api routes
 */
Route::group(['middleware' => ['auth:soms', 'localization'], 'prefix' => 'trainers'], function () {
    Route::get('/', [TrainerController::class, 'index']);
    Route::post('/enroll', [TrainerController::class, 'store']);
});

/**
 * Trainer Enroll With Batches api routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'trainer-enroll'], function () {
    Route::get('/', [TrainerEnrollController::class, 'index']);
    Route::get('/{tmsProvidersTrainer}/show', [TrainerEnrollController::class, 'show']);
});

/**
 * Dashboard routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'dashboard'], function () {
    Route::get('/summery', [DashboardController::class, 'summery']);
    Route::get('/courses', [DashboardController::class, 'courses']);
    Route::get('/batches', [DashboardController::class, 'batches']);
});

/**
 * Permission api routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'permissions'], function () {
    Route::get('/', [PermissionController::class, 'index']);
    Route::post('/create', [PermissionController::class, 'store']);
    Route::get('/role', [PermissionController::class, 'permissions']);
    Route::get('/edit-permission/{permission}', [PermissionController::class, 'editPermission']);
    Route::patch('/{permission}/update', [PermissionController::class, 'updatePermission']);
});

Route::group(['middleware' => 'auth:soms'], function () {
    /* ApI inspaction  */
    Route::apiResource('inspection', InspectionController::class);
    Route::get('/batchlist', [DashboardController::class, 'getAllbatches'])->name('batchlist');
    Route::get('/providerlist', [DashboardController::class, 'getAllProviders'])->name('providerlist');
    Route::get('/districtslist', [DashboardController::class, 'getAlldistricts'])->name('districts');
    Route::get('/upazilaslist', [DashboardController::class, 'getAllupazilas'])->name('upazilas');
    Route::get('/partnerslist', [DashboardController::class, 'getAllpartners'])->name('partners');
    Route::get('/trainerslist', [DashboardController::class, 'getAlltrainers'])->name('trainers');
    Route::get('/traineeslist', fn() => 'Waiting......')->name('trainees');
    Route::get('/allowancelist', fn() => 'Waiting......')->name('allowance');
});

Route::group(['middleware' => 'auth:soms', 'prefix' => 'dashboardtotal'], function () {
    Route::get('/superadmin', [DashboardController::class, 'dashboardTotalsuoeradmin'])->name('dash.super');
});

/* Refresh Token */
Route::get('/refresh-token', [LoginController::class, 'refreshToken']);

Route::group(['middleware' => ['auth:soms', 'trainer']], function () {
    Route::group(['prefix' => 'attendance'], function () {
        Route::get('/batch-list', [AttendanceController::class, 'batchList'])->name('attendance.batch-list');
        Route::post('/start-class', [AttendanceController::class, 'start'])->name('attendance.start');
        Route::post('/end-class', [AttendanceController::class, 'end'])->name('attendance.end');
        Route::post('/take', [AttendanceController::class, 'take'])->name('attendance.take');
    });
});

/**
 * Permission api routes
 */
Route::group(['middleware' => 'auth:soms', 'prefix' => 'coordinators'], function () {
    Route::get('/', [CoordinatorController::class, 'index']);
    Route::get('/linkBatch/{batch_id}', [CoordinatorController::class, 'linkBatch']);
});




Route::group(['middleware' => 'auth:soms', 'prefix' => 'provider'], function () {
    Route::get('/all-trainers', [ProviderController::class, 'allTrainer'])->name('provider.all-trainer');
});

Route::group(['middleware' => 'auth:soms'], function () {
    Route::get('/check-db', [CheckDbController::class, 'checkDb']);
});

require __DIR__ . '/attendance.php';
