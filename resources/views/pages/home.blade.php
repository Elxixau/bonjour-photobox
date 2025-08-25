@extends('layouts.app')

@section('content')


@php
    // Misalnya orientasi dikirim dari controller setelah pilih kategori
    $orientasi = $kategori->orientasi ?? 'portrait';
@endphp

<div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8 p-4">
  <!-- Video Section -->
  <div class="relative flex-1 max-w-6l mx-auto">
    <div id="videoWrapper"
     class="bg-black rounded-lg overflow-hidden w-full
            {{ $orientasi === 'portrait' ? 'max-w-[480px] aspect-[3/4]' : 'max-w-[800px] aspect-[4/3]' }}
            relative mx-auto">


      <!-- Live Preview -->
      <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>

      <!-- Watermark -->
      <div class="absolute inset-x-0 top-0 z-20 pointer-events-none flex justify-center">
        <div class="mt-2 text-center select-none">
          <div class="font-serif uppercase font-extrabold tracking-widest text-white/90 text-xl
                      drop-shadow-[0_2px_6px_rgba(0,0,0,0.8)]">
            bonjour
          </div>
          <div class="font-serif text-white/85 text-sm md:text-base -mt-1
                      drop-shadow-[0_1px_3px_rgba(0,0,0,0.8)]">
            studiospace
          </div>
        </div>
      </div>

        <!-- Tombol Next di depan live preview bawah -->
  <a href="{{ route('panduan') }}" 
     class="absolute bottom-4 inset-x-0 z-30 mx-auto w-fit bg-white p-2
            flex items-center rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300">
      Next
      <div class=" ml-2 p-1">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
          </svg>
      </div>
  </a>

    </div>

    <div id="camError" class="hidden text-center text-sm text-red-600 mt-2">
      Gagal mengakses kamera. Pastikan izin kamera aktif.
    </div>
  </div>
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
      console.log("Camera settings:", track.getSettings());

      // Atur sesuai kategori
      @if($orientasi === 'landscape')
          // Landscape = default, jangan diubah
          video.style.transform = 'rotate(0deg)';
          video.style.width = '100%';
          video.style.height = '100%';
      @else
          // Portrait = rotate 90 agar tegak
          video.style.transform = 'rotate(90deg)';
          video.style.objectFit = 'cover';
          video.style.height = '100%';
          video.style.width = 'auto';
      @endif

    } catch (err) {
      console.error("Gagal mengakses kamera:", err);
      camError?.classList.remove('hidden');
    }
  }

  window.addEventListener('DOMContentLoaded', startCamera);
</script>

@endsection
