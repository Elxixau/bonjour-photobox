<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CloudGalleryController;
/*
|--------------------------------------------------------------------------
| Halaman Awal & Kategori
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('kategori.select'))->name('home');
Route::get('/kategori', [PageController::class, 'pilihKategori'])->name('kategori.select');
Route::post('/kategori', [PageController::class, 'storeKategori'])->name('kategori.set');

/*
|--------------------------------------------------------------------------
| Halaman Umum
|--------------------------------------------------------------------------
*/
Route::get('/welcome', [PageController::class, 'welcome'])->name('welcome');
Route::get('/panduan', [PageController::class, 'panduan'])->name('panduan');

Route::get('/pilih-layout/{order}', [PageController::class, 'layout'])->name('layout');
/*
|--------------------------------------------------------------------------
| Pilih Frame / Stiker / Filter
|--------------------------------------------------------------------------
*/

// Halaman pilih frame (layout dikirim lewat URL)
Route::get('/pilih-frame/{orderCode}/{layout}', [AssetController::class, 'pilihFrame'])
    ->name('frame.choose');

// Pilih frame → simpan frame_id → lanjut ke sesi foto
Route::patch('/pilih-frame/{orderCode}/{layout}', [AssetController::class, 'selectFrame'])
    ->name('frame.select');
// nanti di sini bisa tambahin route untuk stiker & filter
// Route::get('/{order}/sticker', [PageController::class, 'sticker'])->name('sticker');
// Route::get('/{order}/filter', [PageController::class, 'filter'])->name('filter');

/*
|--------------------------------------------------------------------------
| Payment
|--------------------------------------------------------------------------
*/
Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
Route::get('/receipt/{order}', [PaymentController::class, 'receipt'])->name('payment.receipt');
Route::get('/invoice/{order}', [PaymentController::class, 'invoice'])->name('payment.invoice');

/*
|--------------------------------------------------------------------------
| Foto & Gallery
|--------------------------------------------------------------------------
*/
Route::get('/sesi-foto/{orderCode}/{layout?}', [PageController::class, 'sesiFoto'])->name('sesi-foto.show');
Route::post('/upload-photo', [PhotoController::class, 'uploadPhoto'])->name('upload.photo');
Route::post('/photo/upload-all', [PhotoController::class, 'uploadAll'])->name('photo.upload.all');// web.php
Route::post('/upload-final/{orderCode}', [PhotoController::class, 'uploadFinal'])->name('upload.final');
Route::get('/filter/{orderCode}', [AssetController::class, 'filter'])->name('filter.index');
Route::post('/sticker/export/{orderCode}', [AssetController::class, 'export'])->name('sticker.export');
Route::get('/order/{order_code}/qr-data', [PageController::class, 'getQrData'])->name('orders.qr-data');
Route::get('/preview/{orderCode}', [PageController::class, 'preview'])->name('preview.show');

// routes/web.php
Route::delete('/photos/{id}', [PhotoController::class, 'destroy'])->name('photos.destroy');


Route::prefix('gallery')->group(function () {
    Route::get('{order_code}', [PhotoController::class, 'show'])->name('gallery.show');
Route::get('/gallery/download/{photo}', [PhotoController::class, 'download'])
    ->where('photo', '.*') // <-- menangkap semua path termasuk folder
    ->name('photo.download');

});
