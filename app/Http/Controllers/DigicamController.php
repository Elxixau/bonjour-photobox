<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DigicamController extends Controller
{
    public function captureFromDigiCam(Request $request)
    {
        $orderCode = $request->input('order_code');

        // 1. Trigger capture
        $response = Http::get('http://localhost:5513/capture');

        if (!$response->ok()) {
            return response()->json(['error' => 'Failed to capture'], 500);
        }

        // 2. Ambil file terbaru dari folder digiCamControl
        $digicamFolder = 'C:/Users/Public/Pictures/digiCamControl';
        $latestFile = collect(glob($digicamFolder . '/*.jpg'))
                        ->sortByDesc(fn($file) => filemtime($file))
                        ->first();

        if (!$latestFile) {
            return response()->json(['error' => 'No file captured'], 500);
        }

        // 3. Copy ke Laravel storage
        $filename = $orderCode . '_' . Str::random(6) . '.jpg';
        $path = "photos/{$orderCode}/{$filename}";

        Storage::disk('public')->put($path, file_get_contents($latestFile));

        return response()->json([
            'success' => true,
            'file' => Storage::url($path),
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
