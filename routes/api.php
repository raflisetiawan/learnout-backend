<?php

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
Route::apiResource('/jobs', App\Http\Controllers\JobListingController::class);
Route::apiResource('/universities', App\Http\Controllers\UniversityController::class);
Route::apiResource('/students', App\Http\Controllers\StudentController::class);
Route::get('/students/user-id/{id}', 'App\Http\Controllers\StudentController@getStudentByUserId');
Route::apiResource('/applications', App\Http\Controllers\ApplicationController::class);

Route::post('/signin', 'App\Http\Controllers\SignInController@index');
Route::get('/signout', 'App\Http\Controllers\SignInController@logout');
Route::post('/signup', 'App\Http\Controllers\SignUpController@store');
