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
        $photos = Photo::where('order_id', $order->id)->get();

        // Tentukan startDate dan endDate, misal dari kolom order
        $startDate = $order->start_at ?? now();
        $endDate = $order->end_at ?? now()->addDays(7);

        return view('gallery.show', compact('order', 'photos', 'startDate', 'endDate'));
    }

    public function download($orderCode, $filename)
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();
        $photo = Photo::where('order_id', $order->id)
                      ->where('img_path', 'like', "%{$filename}")
                      ->firstOrFail();

        return response()->download(storage_path('app/public/' . $photo->img_path));
    }
}
