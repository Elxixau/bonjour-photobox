@extends('layouts.app')

@section('content')
@php
    $layout = $layout ?? 4;
    $orderId = $order->id ?? '';
    $orientasi = $orientasi ?? 'portrait'; // portrait / landscape
@endphp

<style>
@keyframes blink {0%,100%{opacity:1}50%{opacity:0}}
.blink{animation:blink 1s steps(1,start) infinite}
.preview-img {position:relative;}
.download-btn {position:absolute;top:2px;right:2px;background:black;color:white;padding:0.2rem 0.4rem;font-size:0.75rem;border-radius:4px;cursor:pointer;}
</style>

<h1 id="info" class="text-3xl text-white font-black font-serif mb-8 text-center">
   0/{{ $layout }} Foto
</h1>

<div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8 p-4">
    <div class="relative flex-1 max-w-md mx-auto">
        <div id="videoWrapper" class="bg-black rounded-lg overflow-hidden w-full max-w-[480px] mx-auto"
            style="aspect-ratio: {{ $orientasi == 'portrait' ? '3/4' : '4/3' }}">
            <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
            <div id="timer" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 
                w-20 h-20 rounded-full border-4 border-white text-white flex items-center 
                justify-center text-2xl font-bold select-none bg-black bg-opacity-50 hidden">
                3
            </div>
        </div>
    </div>

    <div class="flex-1 max-w-md mx-auto flex flex-col">
        <h2 class="text-xl font-semibold mb-4 text-white text-center">Preview Foto</h2>
        <div id="previewContainer" class="grid grid-cols-2 gap-4 p-2"></div>
    </div>
</div>

<div class="max-w-6xl mx-auto mt-6 text-center space-x-4">
    <button id="captureBtn" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300">
        Ambil Foto
    </button>
    <button id="reset" class="px-6 py-3 bg-gray-400 text-white font-semibold rounded-lg shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 hidden">Capture Ulang</button>
    <button id="nextBtn" class="px-6 py-3 bg-white text-black font-semibold rounded-lg shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 hidden">Selanjutnya</button>
</div>

<script>
const layout = {{ $layout }};
let fotoCount = 0;
const video = document.getElementById('video');
const timerEl = document.getElementById('timer');
const previewContainer = document.getElementById('previewContainer');
const info = document.getElementById('info');
const nextBtn = document.getElementById('nextBtn');
const resetBtn = document.getElementById('reset');
const captureBtn = document.getElementById('captureBtn');

// Start webcam preview
navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
    .then(stream => { video.srcObject = stream; })
    .catch(err => { console.error(err); alert('Tidak dapat mengakses kamera.'); });

// Countdown sebelum capture
function startCountdown(seconds, callback) {
    timerEl.textContent = seconds;
    timerEl.classList.remove('hidden');
    const interval = setInterval(() => {
        seconds--;
        timerEl.textContent = seconds;
        if(seconds <= 0) {
            clearInterval(interval);
            timerEl.classList.add('hidden');
            callback();
        }
    }, 1000);
}

// Capture foto dari iGicam dengan preview unik
async function capturePhoto() {
    try {
        await fetch('http://localhost:5513/?CMD=Capture');
        setTimeout(() => {
            fotoCount++;
            const img = document.createElement('div');
            img.className = 'preview-img';
            const imgEl = document.createElement('img');
            imgEl.src = `http://localhost:5513/preview.jpg?ts=${Date.now()}&id=${fotoCount}`;
            imgEl.className = 'w-full h-auto rounded-lg border border-white';
            img.appendChild(imgEl);

            // Tombol download
            const btn = document.createElement('button');
            btn.innerText = 'Download';
            btn.className = 'download-btn';
            btn.addEventListener('click', () => {
                const a = document.createElement('a');
                a.href = imgEl.src;
                a.download = `foto${fotoCount}.jpg`;
                a.click();
            });
            img.appendChild(btn);

            previewContainer.appendChild(img);
            info.textContent = `${fotoCount}/${layout} Foto`;

            if(fotoCount >= layout) {
                nextBtn.classList.remove('hidden');
                resetBtn.classList.remove('hidden');
                captureBtn.disabled = true;
            }
        }, 500);
    } catch (err) {
        console.error('Capture failed', err);
        alert('Gagal mengambil foto dari kamera.');
    }
}

captureBtn.addEventListener('click', () => {
    if(fotoCount >= layout) return;
    startCountdown(3, capturePhoto);
});

resetBtn.addEventListener('click', () => {
    previewContainer.innerHTML = '';
    fotoCount = 0;
    info.textContent = `0/${layout} Foto`;
    nextBtn.classList.add('hidden');
    resetBtn.classList.add('hidden');
    captureBtn.disabled = false;
});

nextBtn.addEventListener('click', () => {
    alert('Semua foto selesai! Bisa lanjut ke proses berikutnya.');
});
</script>
@endsection
