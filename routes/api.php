<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\DigicamController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/callback', [PaymentController::class, 'callback']);

Route::post('/upload-photo', [DigicamController::class, 'uploadPhoto']);

Route::post('/delete-photo', [DigicamController::class, 'deletePhoto']);

