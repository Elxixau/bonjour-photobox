@extends('layouts.app')

@section('content')
@php
    $orientasi = $kategori->orientasi ?? 'portrait';
@endphp

<div id="cameraContainer" class="fixed inset-0 bg-black flex items-center justify-center overflow-hidden">
    <!-- Live Preview -->
    <video id="video" autoplay playsinline muted class="absolute"></video>

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

<style>
#video {
    transform-origin: center center;
}
</style>

<script>
const video = document.getElementById('video');
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

        const settings = track.getSettings();
        console.log("Camera settings:", settings);

        const isPortrait = "{{ $orientasi }}" === 'portrait';

        if (isPortrait) {
            // Rotasi 90deg, object-fit contain berbasis height
            video.style.transform = 'rotate(90deg)';
            fitPortraitByHeight(settings);
            window.addEventListener('resize', () => fitPortraitByHeight(settings));
        } else {
            // Landscape: tetap cover fullscreen
            video.style.transform = 'rotate(0deg)';
            video.style.width = '100%';
            video.style.height = '100%';
            video.style.objectFit = 'cover';
        }

    } catch (err) {
        console.error("Gagal mengakses kamera:", err);
        alert("Tidak bisa mengakses kamera");
    }
}

/**
 * Sesuaikan portrait agar object-fit berdasarkan height
 */
function fitPortraitByHeight(settings) {
    const container = document.getElementById('cameraContainer');
    const containerWidth = container.clientWidth;
    const containerHeight = container.clientHeight;

    const vidWidth = settings.width;
    const vidHeight = settings.height;

    // Swap karena sudah rotate 90deg
    const scale = containerHeight / vidHeight;
    video.style.height = containerHeight + 'px';
    video.style.width = vidWidth * scale + 'px';
    video.style.objectFit = 'contain';
}

window.addEventListener('DOMContentLoaded', startCamera);
</script>
@endsection
