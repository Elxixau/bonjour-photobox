<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\CloudGallery;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class DigicamController extends Controller
{  
      // Mapping kategori ke IP statis
    protected $kategoriIP = [
        'portrait' => '182.253.164.242:5513',
        'wedding'  => '192.168.1.102:5513',
        'event'    => '192.168.1.103:5513',
        // dst...
    ];

    protected function getIP($kategori)
    {
        return $this->kategoriIP[$kategori] ?? null;
    }

    // Capture berdasarkan kategori
    public function capture($kategori)
    {
        $ip = $this->getIP($kategori);
        if(!$ip) return response('Kategori tidak valid', 400);

        try {
            $res = Http::get("http://$ip/?CMD=Capture");
            return response($res->body(), $res->status());
        } catch (\Exception $e) {
            return response('Gagal konek ke kamera: '.$e->getMessage(), 500);
        }
    }

    // Set custom filename
    public function setFilename($kategori, $filename)
    {
        $ip = $this->getIP($kategori);
        if(!$ip) return response('Kategori tidak valid', 400);

        try {
            $res = Http::get("http://$ip/?slc=set&param1=session.filenametemplate&param2=$filename");
            return response($res->body(), $res->status());
        } catch (\Exception $e) {
            return response('Gagal set filename: '.$e->getMessage(), 500);
        }
    }

    // Preview foto terakhir
    public function preview($kategori)
    {
        $ip = $this->getIP($kategori);
        if(!$ip) return response('Kategori tidak valid', 400);

        try {
            $res = Http::get("http://$ip/preview.jpg");
            return response($res->body(), 200)
                    ->header('Content-Type', 'image/jpeg');
        } catch (\Exception $e) {
            return response('Gagal ambil preview: '.$e->getMessage(), 500);
        }
    }

    // Capture berdasarkan order ID
    public function captureByOrder($orderId)
    {
        $order = Order::find($orderId);
        if(!$order) return response('Order tidak ditemukan', 404);

        $kategori = $order->kategori;
        return $this->capture($kategori);
    }

    public function setFilenameByOrder($orderId, $filename)
    {
        $order = Order::find($orderId);
        if(!$order) return response('Order tidak ditemukan', 404);

        $kategori = $order->kategori;
        return $this->setFilename($kategori, $filename);
    }

    public function previewByOrder($orderId)
    {
        $order = Order::find($orderId);
        if(!$order) return response('Order tidak ditemukan', 404);

        $kategori = $order->kategori;
        return $this->preview($kategori);
    }
}
