@extends('layouts.app')

@section('content')
@php
    $orientasi = $kategori->orientasi ?? 'portrait';
@endphp

<div id="cameraContainer" class="fixed inset-0 bg-black">
    <!-- Live Preview -->
    <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>

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

<script>
  const video = document.getElementById('video');
  const camError = document.getElementById('camError');
  let stream, track;

  async function startCamera() {
    try {
      stream = await navigator.mediaDevices.getUserMedia({
        video: {
          facingMode: "user",
          width: { ideal: 1920 },
          height: { ideal: 1080 },
          frameRate: { ideal: 60 }
        },
        audio: false
      });

      video.srcObject = stream;
      track = stream.getVideoTracks()[0];

      @if($orientasi === 'landscape')
          video.style.transform = 'rotate(0deg)';
      @else
          video.style.transform = 'rotate(90deg)';
          video.style.objectFit = 'cover';
      @endif

    } catch (err) {
      console.error("Gagal mengakses kamera:", err);
      alert("Tidak bisa mengakses kamera");
    }
  }

  window.addEventListener('DOMContentLoaded', startCamera);
</script>
@endsection
