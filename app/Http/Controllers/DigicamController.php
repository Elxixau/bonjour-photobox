<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\CloudGallery;
use App\Models\Order;
use Intervention\Image\Facades\Image;

class DigicamController extends Controller
{ 
        
    public function uploadPhoto(Request $request)
    {
          $orderCode = $request->input('order_code');
        $file = $request->file('file'); // hasil capture dikirim dari websocket / API

        if (!$orderCode || !$file) {
            return response()->json(['error' => 'Order code atau file tidak ada'], 400);
        }

        // Cari order berdasarkan order_code
        $order = Order::where('order_code', $orderCode)->first();
        if (!$order) {
            return response()->json(['error' => 'Order tidak ditemukan'], 404);
        }

        // Simpan file ke folder storage/app/public/{order_code}
        $folderPath = $orderCode;
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($folderPath, $fileName, 'public');

        // Simpan ke database
        $gallery = CloudGallery::create([
            'order_id' => $order->id,
            'img_path' => $filePath
        ]);

        return response()->json([
            'message' => 'File berhasil disimpan',
            'data' => $gallery,
            'url' => Storage::url($filePath) // -> /storage/{order_code}/{nama_file}.jpg
        ]);
    }

    public function deletePhoto(Request $request)
{
    $orderCode = $request->input('order_code');
    $fileName = $request->input('file_name'); // nama file yang akan dihapus

    if (!$orderCode || !$fileName) {
        return response()->json(['error' => 'Order code atau file_name tidak ada'], 400);
    }

    // Cari order berdasarkan order_code
    $order = Order::where('order_code', $orderCode)->first();
    if (!$order) {
        return response()->json(['error' => 'Order tidak ditemukan'], 404);
    }

    // Cari file di database
    $gallery = CloudGallery::where('order_id', $order->id)
        ->where('img_path', 'like', "%{$fileName}%")
        ->first();

    if (!$gallery) {
        return response()->json(['error' => 'File tidak ditemukan di database'], 404);
    }

    // Hapus file dari storage
    if (\Storage::disk('public')->exists($gallery->img_path)) {
        \Storage::disk('public')->delete($gallery->img_path);
    }

    // Hapus record dari database
    $gallery->delete();

    return response()->json([
        'message' => 'File berhasil dihapus',
        'file_name' => $fileName
    ]);
}


}
