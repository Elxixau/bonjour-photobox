@extends('layouts.app')

@section('content')
<h1 class="text-white text-center text-3xl mb-4">Preview Foto</h1>

<div class="flex justify-center mb-4">
    <div id="timer" class="w-16 h-16 text-white text-center text-2xl leading-16 rounded-full bg-black bg-opacity-50 hidden"></div>
</div>

<div class="flex justify-center mb-4">
    <button id="captureBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg">Ambil Foto</button>
</div>

<div id="previewContainer" class="grid grid-cols-2 gap-4 p-4 max-w-4xl mx-auto"></div>

<style>
.preview-img { position: relative; }
.download-btn {
    position: absolute;
    top: 2px;
    right: 2px;
    background: black;
    color: white;
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
    border-radius: 4px;
    cursor: pointer;
}
#timer { line-height: 64px; font-weight: bold; }
</style>

<script>
let fotoCount = 1;
const previewContainer = document.getElementById('previewContainer');
const captureBtn = document.getElementById('captureBtn');
const timerEl = document.getElementById('timer');

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
    const filename = `DSC_${String(fotoCount).padStart(4,'0')}.jpg`;

    try {
        await fetch('http://localhost:5513/?CMD=Capture');

        setTimeout(async () => {
            const res = await fetch('http://localhost:5513/preview.jpg');
            if(!res.ok) return alert('Preview gagal diambil');

            const blob = await res.blob();

            const wrapper = document.createElement('div');
            wrapper.className = 'preview-img';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(blob);
            img.className = 'w-full h-auto rounded-lg border border-white';
            wrapper.appendChild(img);

            const btn = document.createElement('button');
            btn.innerText = 'Download';
            btn.className = 'download-btn';
            btn.addEventListener('click', () => {
                const a = document.createElement('a');
                a.href = img.src;
                a.download = filename;
                a.click();
            });
            wrapper.appendChild(btn);

            previewContainer.appendChild(wrapper);
            fotoCount++;
        }, 500); // delay untuk menunggu file tersimpan
    } catch(err){
        console.error(err);
        alert('Gagal capture foto');
    }
}

captureBtn.addEventListener('click', () => {
    startCountdown(3, capturePhoto); // countdown 3 detik sebelum capture
});
</script>
@endsection
