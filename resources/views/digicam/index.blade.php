@extends('layouts.app')

@section('content')
@php
    $layout = $layout ?? 4;
@endphp

<style>
.preview-img { position: relative; }
.download-btn { position: absolute; top: 2px; right: 2px; background: black; color: white; padding: 0.2rem 0.4rem; font-size: 0.75rem; border-radius: 4px; cursor: pointer; }
#timer { width: 60px; height: 60px; font-size: 1.5rem; line-height: 60px; text-align: center; border-radius: 50%; border: 3px solid white; background: rgba(0,0,0,0.5); color: white; margin: auto; display: none;}
</style>

<h1 id="info" class="text-3xl text-white font-black font-serif mb-8 text-center">
   0/{{ $layout }} Foto
</h1>

<div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8 p-4">
    <div class="flex-1 max-w-md mx-auto text-center">
        <div id="timer">3</div>
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

const timerEl = document.getElementById('timer');
const previewContainer = document.getElementById('previewContainer');
const info = document.getElementById('info');
const nextBtn = document.getElementById('nextBtn');
const resetBtn = document.getElementById('reset');
const captureBtn = document.getElementById('captureBtn');

function startCountdown(seconds, callback) {
    timerEl.textContent = seconds;
    timerEl.style.display = 'block';
    const interval = setInterval(() => {
        seconds--;
        timerEl.textContent = seconds;
        if(seconds <= 0){
            clearInterval(interval);
            timerEl.style.display = 'none';
            callback();
        }
    }, 1000);
}

async function capturePhoto() {
    try {
        // Capture
        await fetch('http://127.0.0.1:5513/?CMD=Capture');

        // Tunggu sebentar
        setTimeout(async ()=>{
            const res = await fetch('http://127.0.0.1:5513/DSC0011.jpg');
            if(!res.ok) return alert('Gagal ambil preview');
            const blob = await res.blob();

            const filename = `DSC_${String(fotoCount+1).padStart(4,'0')}.jpg`;

            const img = document.createElement('div');
            img.className = 'preview-img';

            const imgEl = document.createElement('img');
            imgEl.src = URL.createObjectURL(blob);
            imgEl.className = 'w-full h-auto rounded-lg border border-white';
            img.appendChild(imgEl);

            const btn = document.createElement('button');
            btn.innerText = 'Download';
            btn.className = 'download-btn';
            btn.addEventListener('click', ()=> {
                const a = document.createElement('a');
                a.href = imgEl.src;
                a.download = filename;
                a.click();
            });
            img.appendChild(btn);

            previewContainer.appendChild(img);
            fotoCount++;
            info.textContent = `${fotoCount}/${layout} Foto`;

            if(fotoCount >= layout){
                nextBtn.classList.remove('hidden');
                resetBtn.classList.remove('hidden');
                captureBtn.disabled = true;
            }
        }, 500);

    } catch(err){
        console.error(err);
        alert('Gagal capture foto.');
    }
}

captureBtn.addEventListener('click', ()=>{
    if(fotoCount >= layout) return;
    startCountdown(3, capturePhoto);
});

resetBtn.addEventListener('click', ()=>{
    previewContainer.innerHTML = '';
    fotoCount = 0;
    info.textContent = `0/${layout} Foto`;
    nextBtn.classList.add('hidden');
    resetBtn.classList.add('hidden');
    captureBtn.disabled = false;
});

nextBtn.addEventListener('click', ()=>{
    alert('Semua foto selesai! Bisa lanjut ke proses berikutnya.');
});
</script>
@endsection
