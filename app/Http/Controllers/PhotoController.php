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
    }
    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);

        // hapus file di storage
        if ($photo->path && Storage::exists('public/' . $photo->path)) {
            Storage::delete('public/' . $photo->path);
        }

        // hapus data di DB
        $photo->delete();

        return response()->json(['success' => true]);
    }
  public function upload(Request $request)
    { $request->validate([
            'order_id' => 'required|exists:orders,id',
            'image' => 'required|string',
        ]);

        $order = Order::find($request->order_id);

        // Extract base64 image
        $imageData = $request->image;
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, etc
            $imageData = base64_decode($imageData);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid image data'], 400);
        }

        $folder = "photos/ORD-{$order->order_code}";
        Storage::makeDirectory($folder);

        $filename = uniqid() . ".jpeg";
        $path = "$folder/$filename";

        Storage::put($path, $imageData);

        // Simpan path ke DB jika perlu (misal order punya relasi photos)
        // $order->photos()->create(['path' => $path]);

        $url = Storage::url($path);

        return response()->json(['success' => true, 'url' => $url]);
    }

    /**
     * Delete single photo
     */
    public function deleteSingle(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'image_url' => 'required|string',
        ]);

        $imageUrl = $request->image_url;
        $orderId = $request->order_id;

        // Ambil path dari url
        $path = str_replace('/storage/', '', $imageUrl);

        if (Storage::exists("public/{$path}")) {
            Storage::delete("public/{$path}");
        }

        // Hapus dari database
        CloudGallery::where('order_id', $orderId)
            ->where('img_path', $path)
            ->delete();

        return response()->json(['success'=>true, 'message'=>'Foto berhasil dihapus']);
    }

    /**
     * Delete all photos of order
     */
    public function deleteAll(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer'
        ]);

        $orderId = $request->order_id;

        $photos = CloudGallery::where('order_id', $orderId)->get();

        foreach ($photos as $photo) {
            if (Storage::exists("public/{$photo->img_path}")) {
                Storage::delete("public/{$photo->img_path}");
            }
        }

        CloudGallery::where('order_id', $orderId)->delete();

        return response()->json(['success'=>true, 'message'=>'Semua foto berhasil dihapus']);
    }
}
