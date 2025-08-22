<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Frame;
use App\Models\CloudGallery;
use App\Models\Sticker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AssetController extends Controller
{
    
    // Halaman pilih frame
    public function pilihFrame($orderCode, $layout)
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();

        // Ambil semua frame aktif
        $frames = Frame::where('active', true)->get();

        return view('assets.frame', compact('order', 'frames', 'layout'));
    }

    // Simpan frame_id ke order dan lanjut ke sesi foto
    public function selectFrame(Request $request, $orderCode, $layout)
    {
        $request->validate([
            'frame_id' => 'required|exists:frames,id',
        ]);

        $order = Order::where('order_code', $orderCode)->firstOrFail();
        $order->frame_id = $request->input('frame_id');
        $order->save();

        return redirect()->route('sesi-foto.show', [
            'orderCode' => $order->order_code,
            'layout' => $layout
        ]);
    }
    
    public function sticker($orderCode)
    {
        $order = Order::with('cloudGallery', 'frame') // photos = CloudGallery
                    ->where('order_code', $orderCode)
                    ->firstOrFail();

        $stickers = Sticker::all();

        // Ambil frame dari relasi order
        $frame = $order->frame;

        return view('assets.sticker', compact('order', 'stickers', 'frame'));
    }


   public function export(Request $request, $orderCode)
{
    $request->validate([
        'final_image' => 'required',
    ]);

    $dataUrl = $request->input('final_image');

    // pisahkan base64 prefix
    if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
        $imageData = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $imageData = base64_decode($imageData);
        $ext = strtolower($type[1]); // jpg/png
    } else {
        return back()->with('error', 'Format gambar tidak valid');
    }

    // buat folder jika belum ada
    $folderPath = "cloud_gallery/{$orderCode}";
    if (!Storage::disk('public')->exists($folderPath)) {
        Storage::disk('public')->makeDirectory($folderPath);
    }

    // nama file unik
    $filename = $folderPath . '/' . uniqid() . '.' . $ext;

    // simpan ke storage/public
    Storage::disk('public')->put($filename, $imageData);
   $order = Order::where('order_code', $orderCode)->firstOrFail();

$gallery = CloudGallery::create([
    'order_id' => $order->id, // gunakan ID numerik
    'img_path' => $filename
]);

    // simpan path ke session untuk preview
    session(['exported_file' => $filename]);

    // redirect ke halaman preview
    return redirect()->route('preview.show', $orderCode);
}


}
