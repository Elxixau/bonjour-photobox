<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\DigicamController;
use App\Http\Controllers\Api\KategoriController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/callback', [PaymentController::class, 'callback']);

Route::post('/upload-photo', [DigicamController::class, 'uploadPhoto']);
Route::post('/upload-print', [DigicamController::class, 'uploadPrint']);
Route::post('/delete-photo', [DigicamController::class, 'deletePhoto']);

Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/kategori/{id}', [KategoriController::class, 'show']);

