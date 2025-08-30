<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function pilihKategori()
    {
        $kategoris = Kategori::all();
        return view('kategori.select', compact('kategoris'));
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        // Simpan di session
        session(['kategori_id' => $request->kategori_id]);

        // Setelah pilih kategori, langsung ke welcome
        return redirect()->route('welcome');
    }

    public function welcome()
    {
        if (!session()->has('kategori_id')) {
            return redirect()->route('kategori.select')
                ->with('error', 'Silakan pilih kategori dulu.');
        }

        // Ambil kategori dari session
        $kategori = Kategori::find(session('kategori_id'));

        if (!$kategori) {
            return redirect()->route('kategori.select')
                ->with('error', 'Kategori tidak ditemukan.');
        }

        // Kirim kategori ke view
        return view('pages.home', compact('kategori'));
    }

    public function panduan()
    {
        if (!session()->has('kategori_id')) {
            return redirect()->route('kategori.select')
                ->with('error', 'Silakan pilih kategori dulu.');
        }

        return view('pages.panduan');
    }

    public function layout($orderCode)
    {
        // Ambil data order berdasarkan order_code saja
        $order = Order::where('order_code', $orderCode)->firstOrFail();

        return view('pages.pilih_layout', compact('order'));
    }

    public function sesiFoto($orderCode, $layout = 4)
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();
        
        $orientasi = $order->kategori->orientasi ?? 'portrait';
        
        $allowedLayouts = [4, 6, 7, 8];
        if (!in_array($layout, $allowedLayouts)) {
            $layout = 4;
        }

        // waktu disimpan dalam menit, konversi ke detik
        $durasi = ((int) $order->waktu) * 60;
        if ($durasi <= 0) {
            $durasi = 600; // default 10 menit  
        }

        return view('pages.sesi_foto', compact('order', 'orientasi', 'layout', 'durasi'));
    }

    public function getQrData($order_code)
    {
        $order = Order::where('order_code', $order_code)->firstOrFail();
        $qr = $order->qrAccess;

        if (!$qr) {
            return response()->json(['error' => 'QR code tidak ditemukan'], 404);
        }

        return response()->json([
            'order_code' => $order->order_code,
            'qr_image'   => asset('storage/' . $qr->img_path),
            'qr_url'     => $qr->url_cloud,
        ]);
    }

    public function preview($orderCode)
    {
        $file = session('exported_file'); // file foto terakhir yang diexport
        if (!$file) {
            return redirect()->route('sticker.select', $orderCode)
                            ->with('error', 'Foto belum diexport');
        }

        $order = Order::where('order_code', $orderCode)->firstOrFail();
        $qr = $order->qrAccess;

        return view('pages.preview', [
            'order'      => $order,       // tambahkan variabel $order
            'order_code' => $orderCode,
            'file'       => $file,
            'qr'         => $qr ? [
                'image' => asset('storage/' . $qr->img_path),
                'url'   => $qr->url_cloud,
            ] : null
        ]);
    }

    

}
