<?php

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

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    Route::post('rates', [App\Http\Controllers\Api\V1\RateController::class, 'index']);
    Route::apiResource('shipments', App\Http\Controllers\Api\V1\ShipmentController::class, ['except' => ['update','destroy']]);
    Route::get('/shipments/{shipment}/track', [App\Http\Controllers\Api\V1\ShipmentController::class, 'track']);
});
