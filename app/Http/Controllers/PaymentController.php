<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Order;
use App\Models\Addon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        // pastikan kategori dipilih di session
        if (!session()->has('kategori_id')) {
            return redirect()->route('kategori.select')->with('error', 'Silakan pilih kategori dulu.');
        }

        $kategoriId = session('kategori_id');

        // ambil kategori termasuk addons lewat relasi (jika relasi sudah ada di model)
        $kategori = Kategori::with('addons')->findOrFail($kategoriId);

        // ambil addons dari relasi (pasti ada variabel $addons untuk view)
        $addons = $kategori->addons ?? collect();

        return view('pages.payment.index', compact('kategori', 'addons'));
    }

   public function store(Request $request)
{
    // Validasi tanpa email karena kamu tidak input email dari user
    $request->validate([
        'kategori_id' => 'required|exists:kategori,id',
        'addons' => 'nullable|array',
        'addons.*.id' => 'required|exists:addons,id',
        'addons.*.qty' => 'required|integer|min:0',
    ]);

    $kategori = Kategori::findOrFail($request->kategori_id);

    $totalWaktu = $kategori->waktu;
    $totalHarga = $kategori->harga;

    $addonsInput = $request->input('addons', []);
    $addonsDetails = [];

    foreach ($addonsInput as $addonInput) {
        $addon = Addon::find($addonInput['id']);
        $qty = (int) $addonInput['qty'];
        if ($qty > 0) {
            $waktuPerQty = 2;
            if (stripos($addon->nama, 'cetak') !== false || stripos($addon->nama, 'gantungan') !== false) {
                $waktuPerQty = 0;
            }
            $totalWaktu += $waktuPerQty * $qty;
            $totalHarga += $addon->harga * $qty;

            $addonsDetails[] = [
                'addons_id' => $addon->id,
                'qty' => $qty,
                'harga' => $addon->harga,
            ];
        }
    }

    $orderCode = 'ORD-' . strtoupper(uniqid());

    $order = Order::create([
        'order_code' => $orderCode,
        'kategori_id' => $kategori->id,
        'harga_paket' => $kategori->harga,
        'total_harga' => $totalHarga,
        'status' => 'pending',
        'waktu' => $totalWaktu,
    ]);

    foreach ($addonsDetails as $detail) {
        $order->orderAddons()->create($detail);
    }

    // Generate email unik otomatis
    $email = 'order_' . strtolower($orderCode) . '@yourdomain.com';

    // Midtrans config
    \Midtrans\Config::$serverKey = config('midtrans.server_key');
    \Midtrans\Config::$clientKey = config('midtrans.client_key'); // penting kalau pakai Snap.js
    \Midtrans\Config::$isProduction = false;  // false untuk sandbox
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    $params = [
        'transaction_details' => [
            'order_id' => $orderCode,
            'gross_amount' => $totalHarga,
        ],
        'customer_details' => [
            'email' => $email,
        ],
    ];

    $snapToken = \Midtrans\Snap::getSnapToken($params);

    return view('pages.payment.receipt', compact('snapToken', 'order'));
}

public function invoice($id){
    $order = Order::find($id);
    return view('pages.payment.invoice', compact('order'));
}
 



}
