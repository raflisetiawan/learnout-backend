<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobListingController;
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
Route::apiResource('/users', UserController::class);
Route::apiResource('/jobs', App\Http\Controllers\JobListingController::class);
Route::get('/jobs/getCategoriesByJobId/{id}', [JobListingController::class, 'getCategoriesByJobId']);
Route::apiResource('/universities', App\Http\Controllers\UniversityController::class);
Route::apiResource('/students', App\Http\Controllers\StudentController::class);
Route::get('/students/user-id/{id}', 'App\Http\Controllers\StudentController@getStudentByUserId');
Route::get('/students/getOneStudentByUserId/{id}', 'App\Http\Controllers\StudentController@getOneStudentByUserId');
Route::apiResource('/applications', App\Http\Controllers\ApplicationController::class);
Route::apiResource('/categories', App\Http\Controllers\CategoryController::class);
Route::patch('/users/update_role/{id}', [UserController::class, 'addRole']);

Route::get('/joblistings/searchByRegency/{regency}', [JobListingController::class, 'searchByRegency']);

Route::post('/signin', 'App\Http\Controllers\SignInController@index');
Route::get('/signout', 'App\Http\Controllers\SignInController@logout');
Route::post('/signup', 'App\Http\Controllers\SignUpController@store');
