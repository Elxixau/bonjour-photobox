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
        $file = $request->file('file');

        if (!$orderCode || !$file) {
            return response()->json(['error' => 'Order code atau file tidak ada'], 400);
        }

        // Cari order
        $order = Order::where('order_code', $orderCode)->first();
        if (!$order) {
            return response()->json(['error' => 'Order tidak ditemukan'], 404);
        }

        // Simpan file asli (HD)
        $folderPath = $orderCode;
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($folderPath, $fileName, 'public');

        // Buat thumbnail kualitas 40%
        $thumbPath = $folderPath . '/thumb_' . $fileName;
        $image = Image::make($file->getRealPath())
            ->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->encode('jpg', 40); // kualitas 40%

        Storage::disk('public')->put($thumbPath, (string) $image);

        // Simpan ke DB
        $gallery = CloudGallery::create([
            'order_id' => $order->id,
            'img_path' => $filePath
        ]);

        return response()->json([
            'message'   => 'File berhasil disimpan',
            'data'      => $gallery,
            'url'       => Storage::url($filePath),   // HD asli
            'thumb_url' => Storage::url($thumbPath)   // Thumbnail 40%
        ]);
    }

}
