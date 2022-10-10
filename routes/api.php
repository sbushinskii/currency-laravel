<?php

use Illuminate\Support\Facades\Route;

use \App\Http\Controllers\API\RegisterController;
use \App\Http\Controllers\API\BitcounController;
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
Route::prefix('v1')->group(function(){
    Route::controller(RegisterController::class)->group(function(){
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::get('ping', 'ping');
    });

    Route::middleware('auth:sanctum')->group( function () {
        Route::controller(BitcounController::class)->group(function(){
            // you can pass currency params in format: ?currency=USD, RUB, EUR
            Route::get('rates', 'get_rates');

            // POST params:
            // currency_from; currency_to; value;
            Route::post('convert', 'convert_rates');
        });
    });
});
