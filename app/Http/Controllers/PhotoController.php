<?php

namespace App\Http\Controllers;


use App\Models\Order;
use App\Models\CloudGallery;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{

public function show($orderCode)
    {
        // Cari order berdasarkan order_code
        $order = Order::where('order_code', $orderCode)->firstOrFail();

        // Ambil foto-foto terkait order ini
        $photos = CloudGallery::where('order_id', $order->id)->get();

        // Tentukan startDate dan endDate, misal dari kolom order
        $startDate = $order->start_at ?? now();
        $endDate = $order->end_at ?? now()->addDays(7);

        return view('gallery.show', compact('order', 'photos', 'startDate', 'endDate'));
    }

    
// GalleryController.php
public function download( $photo)
{
    $filePath = storage_path('app/public/' . $photo);
    
    if (!file_exists($filePath)) {
        abort(404);
    }
    
 // Memaksa browser untuk mendownload
    return response()->download($filePath, basename($photo));
}
}
