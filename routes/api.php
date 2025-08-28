<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\DigicamController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/api/callback', [PaymentController::class, 'callback']);
Route::get('/api/proxy/capture', [DigicamController::class, 'capture']);
Route::get('/api/proxy/preview', [DigicamController::class, 'preview']);
