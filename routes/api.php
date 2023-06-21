<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('/companies', App\Http\Controllers\CompanyController::class);
Route::get('/companies/user-id/{id}', [CompanyController::class, 'getCompanyByUserId']);
Route::get('/companies/getOneCompanyByUserId/{id}', [CompanyController::class, 'getOneCompanyByUserId']);

Route::get('/users/getUserAndStudentByUserId/{id}', [UserController::class, 'getUserAndStudentByUserId']);
Route::get('/users/getUserAndCompanyByUserId/{id}', [UserController::class, 'getUserAndCompanyByUserId']);
Route::apiResource('/users', UserController::class);
Route::patch('/users/updateImageAndName/{id}', [UserController::class, 'updateImageAndName']);
Route::get('/users/getUserWithStudentWithUniversityByUserId/{id}', [UserController::class, 'getUserWithStudentWithUniversityByUserId']);

Route::apiResource('/jobs', App\Http\Controllers\JobListingController::class);
Route::get('/jobs/showJobWithCompanyAndCategories/{id}', [JobListingController::class, 'showJobWithCompanyAndCategories']);
Route::get('/jobs/getJobByInterest/{id}', [JobListingController::class, 'getJobWithCompanyByCategoryIdFromStudentIdFromUserId']);
Route::get('/jobs/getJobByCompanyId/{id}', [JobListingController::class, 'getJobByCompanyId']);


Route::apiResource('/universities', App\Http\Controllers\UniversityController::class);

Route::apiResource('/students', App\Http\Controllers\StudentController::class);
Route::get('/students/user-id/{id}', 'App\Http\Controllers\StudentController@getStudentByUserId');
Route::get('/students/getStudentIdByUserId/{id}', 'App\Http\Controllers\StudentController@getStudentIdByUserId');
Route::get('/students/getJobAround/{id}', [StudentController::class, 'jobAround']);
Route::get('/students/getApplicationHistoryByUserId/{id}', [StudentController::class, 'getApplicationHistoryByUserId']);
Route::get('/students/getStudentsByJobId/{id}', [StudentController::class, 'getStudentsByJobId']);
Route::patch('/students/updateResumeStudent/{id}', [StudentController::class, 'updateResumeStudent']);
Route::get('/students/getOneStudentByUserId/{id}', [StudentController::class, 'getOneStudentByUserId']);
Route::get('/students/getStudentWithResume/{id}', [StudentController::class, 'getStudentWithResume']);

Route::apiResource('/applications', App\Http\Controllers\ApplicationController::class);
Route::get('/applications/getApplicationsHistoryByUserId/{id}', [ApplicationController::class, 'getApplicationsHistoryByUserId']);
Route::get('/applications/getApplicationsHistoryByStudentId/{id}', [ApplicationController::class, 'getApplicationsHistoryByStudentId']);
Route::patch('/applications/cancel/{id}', [ApplicationController::class, 'cancel']);
Route::get('/applications/getApplicationsHistoryByJobId/{id}', [ApplicationController::class, 'getApplicationsHistoryByJobId']);


Route::apiResource('/categories', App\Http\Controllers\CategoryController::class);
Route::apiResource('/categories', App\Http\Controllers\CategoryController::class);

Route::patch('/users/update_role/{id}', [UserController::class, 'addRole']);

Route::post('/signin', 'App\Http\Controllers\SignInController@index');
Route::get('/signout', 'App\Http\Controllers\SignInController@logout');
Route::post('/signup', 'App\Http\Controllers\SignUpController@store');
