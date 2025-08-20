<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QrAccess;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Order;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

   /*  public function callback(Request $request)
{
    $serverKey = config('midtrans.server_key');
    $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

    if ($hashed == $request->signature_key) {
        if (in_array($request->transaction_status, ['capture', 'settlement'])) {
            $order = Order::where('order_code', $request->order_id)->first();

            if ($order && $order->status !== 'success') {
                $order->update(['status' => 'success']); // update jadi success

                try {
                    Mail::to($order->email)->send(new \App\Mail\CloudGalleryMail($order));
                } catch (\Exception $e) {
                    \Log::error('Gagal kirim email galeri: ' . $e->getMessage());
                }
            }
        }
    }

    return response()->json(['message' => 'OK'], 200);
}
 */

public function callback(Request $request)
{
    $serverKey = config('midtrans.server_key');
    $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

    if ($hashed === $request->signature_key) {
        if (in_array($request->transaction_status, ['capture', 'settlement'])) {

            $order = Order::where('order_code', $request->order_id)->first();

            if ($order) {
                // Update status order jadi success
                $order->update(['status' => 'success']);

                // Kalau sudah ada QR, hapus dulu file lama dan record-nya
                if ($order->qrAccess) {
                    Storage::disk('public')->delete($order->qrAccess->img_path);
                    $order->qrAccess->delete();
                }

                // Generate URL gallery untuk QR
                $url = route('gallery.show', ['order_code' => $order->order_code]);

                // Generate QR code PNG
                $qrPng = QrCode::format('png')->size(300)->generate($url);

                // Simpan file di storage
                $fileName = "qr_codes/{$order->order_code}.png";
                Storage::disk('public')->put($fileName, $qrPng);

                // Simpan data QR ke tabel qr_access
                $qrAccess = QrAccess::create([
                    'url_cloud' => $url,
                    'img_path'  => $fileName,
                ]);

                // Update order dengan qr_id
                $order->update(['qr_id' => $qrAccess->id]);
            }
        }
    }

    return response()->json(['message' => 'OK'], 200);
}

}
