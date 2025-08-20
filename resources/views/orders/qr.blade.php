@extends('layouts.app')

@section('content')
<div class=" flex flex-col items-center justify-center bg-gray-50 p-6">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full text-center">
        <h1 class="text-2xl font-bold mb-6">Cloude Gallery QR <br> <span class="text-indigo-600">{{ $order->order_code }}</span></h1>

        <img src="{{ $qrImage }}" alt="QR Code" class="mx-auto mb-6 w-64 h-64 object-contain rounded-md shadow"/>

        <p class="text-gray-700 mb-4">Scan QR code ini untuk mengakses galeri foto Anda.</p>

    </div>
</div>
@endsection
