<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\JobApplicationRequisiteController;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\JobtypeController;
use App\Http\Controllers\RoleController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'profile']);
});

Route::apiResource('/companies', App\Http\Controllers\CompanyController::class);
Route::get('/companies/user-id/{id}', [CompanyController::class, 'getCompanyByUserId']);
Route::get('/companies/getOneCompanyByUserId/{id}', [CompanyController::class, 'getOneCompanyByUserId']);
Route::get('/companies/closed-job-count/{id}', [CompanyController::class, 'getClosedJobCountByCompanyId']);
Route::get('/companies/open-job-count/{id}', [CompanyController::class, 'getOpenJobCountByCompanyId']);
Route::get('/companies/all-count/{id}', [CompanyController::class, 'getAllJobCountByCompanyId']);
Route::get('/companies/get-job-applications-per-day/{id}', [CompanyController::class, 'getJobApplicationsByCompanyId']);
Route::get('/companies/count/total', [CompanyController::class, 'getTotalCompanies']);



Route::get('/users/getUserAndStudentByUserId/{id}', [UserController::class, 'getUserAndStudentByUserId']);
Route::get('/users/getUserAndCompanyByUserId/{id}', [UserController::class, 'getUserAndCompanyByUserId']);
Route::apiResource('/users', UserController::class);
Route::patch('/users/updateImageAndName/{id}', [UserController::class, 'updateImageAndName']);
Route::get('/users/getUserWithStudentWithUniversityByUserId/{id}', [UserController::class, 'getUserWithStudentWithUniversityByUserId']);

Route::apiResource('/jobs', App\Http\Controllers\JobListingController::class);
Route::get('/jobs/showJobWithCompanyAndCategories/{id}', [JobListingController::class, 'showJobWithCompanyAndCategories']);
Route::get('/jobs/getJobByInterest/{id}', [JobListingController::class, 'getJobWithCompanyByCategoryIdFromStudentIdFromUserId']);
Route::get('/jobs/getJobByCompanyId/{id}', [JobListingController::class, 'getJobByCompanyId']);
Route::get('/jobs/getJobByUserId/{id}', [JobListingController::class, 'getJobByUserId']);
Route::patch('/jobs/close/{id}', [JobListingController::class, 'closeJob']);
Route::get('/jobs/count/is-closed-count', [JobListingController::class, 'getIsClosedJobCount']);

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
Route::get('/students/getOneStudentByStudentId/{id}', [StudentController::class, 'getOneStudentByStudentId']);
Route::get('/students/count/total', [StudentController::class, 'getTotalStudents']);
Route::get('/students/role/get', [StudentController::class, 'getStudentRoles']);

Route::apiResource('/applications', App\Http\Controllers\ApplicationController::class);
Route::get('/applications/getApplicationsHistoryByUserId/{id}', [ApplicationController::class, 'getApplicationsHistoryByUserId']);
Route::get('/applications/getApplicationsHistoryByStudentId/{id}', [ApplicationController::class, 'getApplicationsHistoryByStudentId']);
Route::patch('/applications/cancel/{id}', [ApplicationController::class, 'cancel']);
Route::get('/applications/getApplicationsHistoryByJobId/{id}', [ApplicationController::class, 'getApplicationsHistoryByJobId']);
Route::get('/applications/getApplicationByStudentId/{id}', [ApplicationController::class, 'getApplicationByStudentId']);
Route::patch('/applications/accept/{id}', [ApplicationController::class, 'acceptApplication']);
Route::patch('/applications/reject/{id}', [ApplicationController::class, 'rejectApplication']);
Route::get('/applications/count/{studentId}', [ApplicationController::class, 'getApplicationCountByStudentId']);
Route::get('/getApplicationCount', [ApplicationController::class, 'getApplicationCount']);
Route::get('/getApplicationsCountPerDay', [ApplicationController::class, 'getApplicationsCountPerDay']);
Route::get('/getApplicationsCountPerMonth', [ApplicationController::class, 'getApplicationCountPerMonth']);
Route::get('/getAcceptedApplicationsCountPerMonth', [ApplicationController::class, 'getAcceptedApplicationsCountPerMonth']);
Route::get('/getRejectedApplicationsCountPerMonth', [ApplicationController::class, 'getRejectedApplicationsCountPerMonth']);
Route::get('/getPendingApplicationsCountPerMonth', [ApplicationController::class, 'getPendingApplicationsCountPerMonth']);

Route::get('/report/jobListingsPerMonth', [JobListingController::class, 'jobListingsPerMonth']);
Route::get('/report/getOpenJobListingsCountPerMonth', [JobListingController::class, 'getOpenJobListingsCountPerMonth']);
Route::get('/report/getCloseJobListingsCountPerMonth', [JobListingController::class, 'getCloseJobListingsCountPerMonth']);
Route::get('/report/getJoblistingPerMonth', [JobListingController::class, 'getJoblistingPerMonth']);
Route::get('/report/getApplicationsPerMonth', [ApplicationController::class, 'getApplicationsPerMonth']);







Route::apiResource('/categories', App\Http\Controllers\CategoryController::class);
Route::apiResource('/categories', App\Http\Controllers\CategoryController::class);
Route::get('/categories/count/getJobByCategory', [CategoryController::class, 'getJobCountByCategory']);

Route::patch('/users/update_role/{id}', [UserController::class, 'addRole']);

Route::post('/signin', 'App\Http\Controllers\SignInController@index');
Route::get('/signout', 'App\Http\Controllers\SignInController@logout');
Route::post('/signup', 'App\Http\Controllers\SignUpController@store');
Route::get('/roles/except-admin', [RoleController::class, 'getRoleExceptAdmin']);
Route::get('/roles/company-id', [RoleController::class, 'getCompanyRoleId']);

Route::get('/jobtypes/all', [JobtypeController::class, 'index']);
Route::get('/jobtypes/{id}', [JobtypeController::class, 'show']);

Route::get('/job-application-requisites', [JobApplicationRequisiteController::class, 'index']);
Route::get('/job-application-requisites/{id}', [JobApplicationRequisiteController::class, 'show']);
Route::get('/job-application-requisites/getByJoblistingId/{id}', [JobApplicationRequisiteController::class, 'getByJoblistingId']);


Route::get('contact-us', [ContactUsController::class, 'index']);
Route::post('contact-us', [ContactUsController::class, 'store']);
