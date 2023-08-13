<?php

use App\Http\Controllers\UserController;
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


Route::group(['middleware' => ['auth:sanctum', 'web']], function () {
    Route::get('/ciao', function (Request $request) {
        return 'ciao';
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/survey/get/{id?}', [UserController::class, 'get_survey']);

    Route::post('/survey/save', [UserController::class, 'save_survey']);

    Route::post('/survey/delete', [UserController::class, 'delete_survey']);

    Route::post('/item/save', [UserController::class, 'save_item']);

    Route::post('/item/delete', [UserController::class, 'delete_item']);

    Route::post('/item/get/{id?}', [UserController::class, 'get_item']);

    Route::post('/result/save', [UserController::class, 'save_result']);

    Route::post('/result/get/{id?}', [UserController::class, 'get_result']);

    Route::post('/result/delete', [UserController::class, 'delete_result']);

});
