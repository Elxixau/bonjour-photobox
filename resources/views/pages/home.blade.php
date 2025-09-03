@extends('layouts.app')

@section('content')
@php
    $orientasi = $kategori->orientasi ?? 'portrait';
@endphp

<div id="cameraContainer" class="fixed inset-0 bg-black flex items-center justify-center overflow-hidden">
    <!-- Watermark -->
    <div class="absolute inset-x-0 top-4 z-20 pointer-events-none flex justify-center">
        <div class="text-center select-none">
            <div class="font-serif uppercase font-extrabold tracking-widest text-white text-xl drop-shadow-[0_1px_3px_rgba(0,0,0,0.8)]">
                bonjour
            </div>
            <div class="font-serif text-white/85 text-sm md:text-base -mt-1 drop-shadow-[0_1px_3px_rgba(0,0,0,0.8)]">
                studiospace
            </div>
        </div>
    </div>

    <!-- Tap layar untuk melanjutkan -->
    <a href="{{ route('panduan') }}"
       class="absolute inset-0 z-30 flex items-end justify-center pb-10 text-xs text-white/80 tracking-wide select-none">
        Tap layar untuk melanjutkan
    </a>
</div>
@endsection
