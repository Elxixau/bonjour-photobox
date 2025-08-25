@extends('layouts.app')

@section('content')

@php
    $orientasi = $kategori->orientasi ?? 'portrait';
@endphp

<div class="max-w-6xl mx-auto flex justify-center p-4">
    <!-- Video Wrapper -->
    <div id="videoWrapper"
         class="relative rounded-lg overflow-hidden
            {{ $orientasi === 'portrait' ? 'max-w-[480px] aspect-[3/4]' : 'max-w-[800px] aspect-[4/3]' }}">
        
        <!-- Video -->
        <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
        
        <!-- Watermark -->
        <div class="absolute inset-x-0 top-0 z-20 pointer-events-none flex flex-col items-center mt-2">
            <div class="font-serif uppercase font-extrabold tracking-widest text-white text-opacity-90 text-xl drop-shadow-lg">
                bonjour
            </div>
            <div class="font-serif text-white text-opacity-85 text-sm md:text-base -mt-1 drop-shadow-md">
                studiospace
            </div>
        </div>

        <!-- Next Button -->
        <a href="{{ route('panduan') }}"
           class="absolute bottom-4 inset-x-0 z-30 mx-auto w-fit bg-white p-2
                  flex items-center justify-center rounded-lg border-2 border-black
                  shadow-lg hover:shadow-xl transition duration-300">
            Next
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25"/>
            </svg>
        </a>
    </div>

    <div id="camError" class="hidden text-center text-sm text-red-600 mt-2">
        Gagal mengakses kamera. Pastikan izin kamera aktif.
    </div>
</div>

<script>
const video = document.getElementById('video');
const camError = document.getElementById('camError');
let stream, track;

const orientasi = "{{ $orientasi }}";

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

        // Atur orientasi video
        if (orientasi === 'portrait') {
            video.style.transform = 'rotate(90deg)';
            video.style.objectFit = 'cover';
            video.style.width = 'auto';
            video.style.height = '100%';
        } else {
            video.style.transform = 'rotate(0deg)';
            video.style.width = '100%';
            video.style.height = '100%';
        }

    } catch (err) {
        console.error("Gagal mengakses kamera:", err);
        camError.classList.remove('hidden');
    }
}

window.addEventListener('DOMContentLoaded', startCamera);
</script>

@endsection
