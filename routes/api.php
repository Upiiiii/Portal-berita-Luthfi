<?php

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

//login
Route::post('login', [App\Http\Controllers\API\AuthController::class, 'login']);
//login
Route::post('register', [App\Http\Controllers\API\AuthController::class, 'register']);
//logout
Route::post('logout', [App\Http\Controllers\API\AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('getAllUser', [App\Http\Controllers\API\UserController::class, 'getAllUser']);
Route::get('getUserById/{id}', [App\Http\Controllers\API\UserController::class, 'getUserById']);

Route::get('category', [App\Http\Controllers\API\CategoryController::class, 'index']);
Route::get('category/{id}', [App\Http\Controllers\API\CategoryController::class, 'show']);
Route::post('create-category', [App\Http\Controllers\API\CategoryController::class, 'create'])->middleware('auth:sanctum');
Route::delete('delete-category/{id}', [App\Http\Controllers\API\CategoryController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('slider', [App\Http\Controllers\API\SliderController::class, 'index']);
Route::post('slider', [App\Http\Controllers\API\SliderController::class, 'create'])->middleware('auth:sanctum');

Route::post('update-password', [App\Http\Controllers\API\AuthController::class, 'updatePassword'])->middleware('auth:sanctum');

Route::get('news', [App\Http\Controllers\API\NewsController::class, 'index']);
Route::get('news/{id}', [App\Http\Controllers\API\NewsController::class, 'show']);

