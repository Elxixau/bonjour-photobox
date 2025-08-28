<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\CloudGallery;
use Illuminate\Support\Facades\File;

class DigicamController extends Controller
{
    public function captureFromDigiCam(Request $request)
    {
        $orderId = $request->input('order_id');

        if (!$orderId) {
            return response()->json(['error' => 'Order ID is required'], 422);
        }

        // 1. Trigger capture di DigiCamControl
        $response = Http::get('http://127.0.0.1:5513/?CMD=Capture');
        if (!$response->ok()) {
            return response()->json(['error' => 'Failed to capture'], 500);
        }

        // 2. Tunggu sebentar biar foto tersimpan
        sleep(2); // delay 2 detik

        // 3. Ambil file terbaru dari folder default DigiCamControl
        $digicamFolder = 'C:/Users/Public/Pictures/digiCamControl';
        $latestFile = collect(glob($digicamFolder . '/*.jpg'))
                        ->sortByDesc(fn($file) => filemtime($file))
                        ->first();

        if (!$latestFile) {
            return response()->json(['error' => 'No file captured'], 500);
        }

        // 4. Simpan ke storage Laravel / cloud_gallery folder
        $order = \App\Models\Order::find($orderId);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $filename = $order->order_code . '_' . Str::random(6) . '.jpg';
        $storagePath = "cloud_gallery/{$order->order_code}/{$filename}";

        Storage::disk('public')->put($storagePath, file_get_contents($latestFile));

        // 5. Simpan ke database
        $gallery = CloudGallery::create([
            'order_id' => $orderId,
            'img_path' => $storagePath
        ]);

        return response()->json([
            'success' => true,
            'id' => $gallery->id,
            'url' => asset("storage/{$storagePath}")
        ]);
    }


        public function deleteSinglePhoto(Request $request)
    {
        $filePath = $request->input('file'); // contoh: photos/ORDER123/file.jpg

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'File not found'], 404);
    }
}
