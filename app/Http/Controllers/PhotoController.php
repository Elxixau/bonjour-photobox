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
    public function capture()
{
    $res = Http::get('http://localhost:5513/?CMD=Capture');
    return response($res->body(), $res->status());
}

public function preview()
{
    $res = Http::get('http://localhost:5513/preview.jpg');
    return response($res->body(), 200)->header('Content-Type', 'image/jpeg');
}



}
