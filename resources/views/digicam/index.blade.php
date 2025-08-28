@extends('layouts.app')

@section('content')
@php
    $durasi = 10; // detik countdown sebelum jepret
    $layout = $layout ?? 4;
    $orderId = $order->id ?? '';
    $totalWaktu = isset($order->waktu) ? intval($order->waktu) * 60 : 300; // detik
    
    $orientasi = $orientasi ?? 'portrait'; // dari kategori
@endphp
<style>
@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}
.blink {
  animation: blink 1s steps(1, start) infinite;
}
</style>

<h1 id="info" class="text-3xl text-white font-black font-serif mb-8 text-center">
   0/{{ $layout }} Foto
</h1>

<div class="absolute top-4 right-4 z-50 flex gap-2">
    <div class="pointer-events-none relative rounded-lg border-2 border-black bg-white px-3 py-1 shadow-black shadow-[4px_4px_0_0] flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
        </svg>
        <span id="globalTimer" class="text-lg font-extrabold text-gray-600 select-none">--:--</span>
    </div>

    <button id="orientationBtn" class=" pointer-events-none px-3 py-1 bg-gray-900 text-white rounded-md hover:bg-gray-700 transition flex items-center gap-1">
        <svg id="orientationIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
            <rect x="6" y="4" width="12" height="16" rx="2" ry="2" stroke="currentColor" stroke-width="2" fill="none"/>
        </svg>
    </button>
</div>

<div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8 p-4">
    <!-- Video Section -->
    <div class="relative flex-1 max-w-md mx-auto">
<div id="videoWrapper" class="bg-black rounded-lg overflow-hidden w-full max-w-[480px] mx-auto"
     style="aspect-ratio: {{ $orientasi == 'portrait' ? '3/4' : '4/3' }}">
    <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
            <div id="timer" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 
                w-20 h-20 rounded-full border-4 border-white text-white flex items-center 
                justify-center text-2xl font-bold select-none bg-black bg-opacity-50">
                {{ $durasi }}
            </div>
    </div>

    </div>

    <!-- Preview Section -->
    <div class="flex-1 max-w-md mx-auto flex flex-col">
        <h2 class="text-xl font-semibold mb-4 text-white text-center">Preview Foto</h2>
        <div id="previewContainer" class="grid grid-cols-2 gap-4 p-2"></div>
    </div>
</div>

<div class="max-w-6xl mx-auto mt-6 text-center space-x-4">
    <button id="reset" class="px-6 py-3  bg-gray-400 text-white font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 hidden">Capture Ulang</button>
    <button id="nextBtn" class="px-6 py-3  bg-white text-black font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 hidden">Selanjutnya</button>
</div>

<script>
const durasi = {{ $durasi }};
const layout = {{ $layout }};
let fotoCount = 0;

// Element references
const video = document.getElementById('video');
const timerEl = document.getElementById('timer');
const previewContainer = document.getElementById('previewContainer');
const info = document.getElementById('info');
const nextBtn = document.getElementById('nextBtn');
const resetBtn = document.getElementById('reset');

// Start countdown for capture
function startCountdown(seconds, callback) {
    timerEl.textContent = seconds;
    timerEl.classList.remove('hidden');

    const interval = setInterval(() => {
        seconds--;
        timerEl.textContent = seconds;
        if(seconds <= 0) {
            clearInterval(interval);
            timerEl.textContent = '';
            callback();
        }
    }, 1000);
}

// Capture from iGicam
async function capturePhoto() {
    try {
        // Trigger capture
        await fetch('http://localhost:5513/?CMD=Capture');
        
        // Small delay to allow camera to save photo
        setTimeout(() => {
            // Download / preview
            const img = document.createElement('img');
            img.src = `http://localhost:5513/preview.jpg?ts=${Date.now()}`; // cache-buster
            img.className = "w-full h-auto rounded-lg border border-white";
            previewContainer.appendChild(img);

            fotoCount++;
            info.textContent = `${fotoCount}/${layout} Foto`;

            // Show buttons if done
            if(fotoCount >= layout) {
                nextBtn.classList.remove('hidden');
                resetBtn.classList.remove('hidden');
            }
        }, 500); // 0.5s delay
    } catch (err) {
        console.error('Capture failed', err);
        alert('Gagal mengambil foto dari kamera.');
    }
}

// Button actions
nextBtn.addEventListener('click', () => {
    alert('Semua foto selesai! Bisa lanjut ke proses berikutnya.');
});

resetBtn.addEventListener('click', () => {
    previewContainer.innerHTML = '';
    fotoCount = 0;
    info.textContent = `0/${layout} Foto`;
    nextBtn.classList.add('hidden');
    resetBtn.classList.add('hidden');
});

// Optional: start first countdown automatically
startCountdown(durasi, capturePhoto);
</script>


@endsection
