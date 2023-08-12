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

    Route::post('/survey', [UserController::class, 'get_survey']);

    Route::post('/save_survey', [UserController::class, 'save_survey']);

    Route::post('/delete_survey', [UserController::class, 'delete_survey']);

    Route::post('/survey/save_item', [UserController::class, 'save_item']);

    Route::post('/survey/delete_item', [UserController::class, 'delete_item']);

    Route::post('/survey/item', [UserController::class, 'get_item']);

    Route::post('/item/save_result', [UserController::class, 'save_result']);

    Route::post('/item/result', [UserController::class, 'get_result']);

    Route::post('/item/delete_result', [UserController::class, 'delete_result_by_item']);

});
