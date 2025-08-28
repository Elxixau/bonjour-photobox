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
{   // IP komputer box iGicam
    protected $kategoriIP = '192.168.18.43:5513';

    protected function getIP($orderId)
    {
        // Ambil kategori dari orderId (dummy contoh)
        $kategori = 'portrait'; // nanti sesuaikan relasi order
        return $this->kategoriIP[$kategori] ?? null;
    }

    public function capture($orderId)
    {
        $ip = $this->getIP($orderId);
        if(!$ip) return response('Kategori tidak valid', 400);

        try {
            $res = Http::get("http://$ip/?CMD=Capture");
            return response($res->body(), $res->status());
        } catch (\Exception $e) {
            return response('Gagal konek ke kamera: '.$e->getMessage(), 500);
        }
    }

    public function setFilename($orderId, $filename)
    {
        $ip = $this->getIP($orderId);
        if(!$ip) return response('Kategori tidak valid', 400);

        try {
            $res = Http::get("http://$ip/?slc=set&param1=session.filenametemplate&param2=$filename");
            return response($res->body(), $res->status());
        } catch (\Exception $e) {
            return response('Gagal set filename: '.$e->getMessage(), 500);
        }
    }

    public function preview($orderId)
    {
        $ip = $this->getIP($orderId);
        if(!$ip) return response('Kategori tidak valid', 400);

        try {
            $res = Http::get("http://$ip/preview.jpg");
            return response($res->body(), 200)
                    ->header('Content-Type', 'image/jpeg');
        } catch (\Exception $e) {
            return response('Gagal ambil preview: '.$e->getMessage(), 500);
        }
    }
}
