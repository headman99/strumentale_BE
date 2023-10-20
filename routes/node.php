<?php

use App\Http\Controllers\ScraperController;
use Illuminate\Support\Facades\Route;



Route::group(['middleware' => ['crawler']], function () {
    Route::post('/save_scrape_result', [ScraperController::class, 'save_scrape_result']);

    Route::get('/get_surveys', [ScraperController::class, 'get_surveys']);

    Route::get('/delete_old_results', [ScraperController::class, 'delete_old_results']);
});
