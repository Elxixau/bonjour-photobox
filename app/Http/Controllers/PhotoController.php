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

    
    public function download($photo)
    {
        // Ambil full path fisik file di storage
        $filePath = Storage::disk('public')->path($photo);

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, basename($filePath));
    }public function deletePhoto(Request $request)
{
    $photoId = $request->input('id'); // id CloudGallery
    $gallery = CloudGallery::find($photoId);

    if (!$gallery) {
        return response()->json(['error' => 'Foto tidak ditemukan'], 404);
    }

    $filePath = $gallery->img_path;
    $folder   = dirname($filePath);
    $fileName = basename($filePath);
    $thumbPath = $folder . '/thumb_' . $fileName;

    // Hapus file asli + thumbnail
    Storage::disk('public')->delete([$filePath, $thumbPath]);

    // Hapus data dari DB
    $gallery->delete();

    return response()->json([
        'message' => 'Foto berhasil dihapus',
        'id' => $photoId
    ]);
}

}
