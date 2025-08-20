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
    
    
   
public function uploadPhoto(Request $request)
{
    $request->validate([
        'order_id' => 'required',
        'image' => 'required',
    ]);

    // Ambil order untuk mendapatkan order_code
    $order = Order::findOrFail($request->order_id);
    $orderCode = $order->order_code;

    // Ambil base64 dan convert ke file
    $imageData = $request->input('image');

    // Deteksi tipe: png / jpeg
    if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
        $imageData = substr($imageData, strpos($imageData, ',') + 1);
        $type = strtolower($type[1]);
    } else {
        $type = 'png';
    }

    $imageData = str_replace(' ', '+', $imageData);
    $imageName = uniqid() . '.' . $type;

    Storage::disk('public')->put("cloud_gallery/{$orderCode}/{$imageName}", base64_decode($imageData));

    // Simpan ke database
    $gallery = CloudGallery::create([
        'order_id' => $request->order_id,
        'img_path' => "cloud_gallery/{$orderCode}/{$imageName}"
    ]);

    return response()->json([
        'success' => true,
        'url' => asset("storage/cloud_gallery/{$orderCode}/{$imageName}"), // frontend pakai URL
        'id' => $gallery->id
    ]);
}


public function delete(Request $request)
{
    $request->validate([
        'order_id' => 'required',
        'image_url' => 'required',
    ]);

    $photo = CloudGallery::where('order_id', $request->order_id)
                ->where('img_path', str_replace(asset('storage') . '/', '', $request->image_url))
                ->first();

    if ($photo) {
        if (Storage::disk('public')->exists($photo->img_path)) {
            Storage::disk('public')->delete($photo->img_path);
        }
        $photo->delete();

        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false]);
}
public function deleteAll(Request $request)
{
    $request->validate([
        'order_id' => 'required',
    ]);

    $photos = CloudGallery::where('order_id', $request->order_id)->get();

    foreach ($photos as $photo) {
        if (Storage::disk('public')->exists($photo->img_path)) {
            Storage::disk('public')->delete($photo->img_path);
        }
        $photo->delete();
    }

    return response()->json(['success' => true]);
}


public function show($order_code)
{
    $order = Order::where('order_code', $order_code)->firstOrFail();
    $photos = $order->cloudGallery()->get(); // relasi ke photos

    // Tanggal mulai dan berakhir
    $startDate = $order->created_at;
    $endDate = $order->created_at->copy()->addDays(7);

    return view('gallery.show', compact('order', 'photos', 'startDate', 'endDate'));
}




}
