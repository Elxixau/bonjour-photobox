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
    protected $igicamIP = '192.168.18.43:5513';

    // Trigger capture foto
    public function capture()
    {
        try {
            $res = Http::get("http://{$this->igicamIP}/?CMD=Capture");
            return response($res->body(), $res->status());
        } catch (\Exception $e) {
            return response('Gagal capture: '.$e->getMessage(), 500);
        }
    }

    // Ambil preview foto terakhir
    public function preview()
    {
        try {
            $res = Http::get("http://{$this->igicamIP}/preview.jpg");
            return response($res->body(), 200)
                ->header('Content-Type', 'image/jpeg');
        } catch (\Exception $e) {
            return response('Gagal ambil preview: '.$e->getMessage(), 500);
        }
    }
}
