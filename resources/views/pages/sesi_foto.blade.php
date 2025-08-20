@extends('layouts.app')

@section('content')
@php
    $durasi = 10; // detik countdown sebelum jepret
    $layout = $layout ?? 4;
    $orderId = $order->id ?? '';
    $totalWaktu = isset($order->waktu) ? intval($order->waktu) * 60 : 300; // detik
@endphp

<h1 id="info" class="text-3xl font-black font-serif mb-8 text-center">
    Foto: 0/{{ $layout }} Foto
</h1>

<div class="absolute top-4 right-4 z-50 flex gap-2">
    <div class="relative rounded-lg border-2 border-black bg-white px-3 py-1 shadow-black shadow-[4px_4px_0_0] flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
        </svg>
        <span id="globalTimer" class="text-lg font-extrabold text-gray-600 select-none">--:--</span>
    </div>

    <button id="orientationBtn" class="px-3 py-1 bg-gray-900 text-white rounded-md hover:bg-gray-700 transition flex items-center gap-1">
        <svg id="orientationIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
            <rect x="6" y="4" width="12" height="16" rx="2" ry="2" stroke="currentColor" stroke-width="2" fill="none"/>
        </svg>
    </button>
</div>

<div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8 p-4">
    <!-- Video Section -->
    <div class="relative flex-1 max-w-md mx-auto">
        <div id="videoWrapper" class="bg-black rounded-lg overflow-hidden w-full max-h-[480px] relative">
            <video id="video" autoplay playsinline muted class="rounded-lg object-cover w-full h-full"></video>
            <div id="timer" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-20 h-20 rounded-full border-4 border-white text-white flex items-center justify-center text-2xl font-bold select-none bg-black bg-opacity-50">
                {{ $durasi }}
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="flex-1 max-w-md mx-auto flex flex-col">
        <h2 class="text-xl font-semibold mb-4 text-center">Preview Foto</h2>
        <div id="previewContainer" class="grid grid-cols-2 gap-4 p-2"></div>
    </div>
</div>

<div class="max-w-6xl mx-auto mt-6 text-center space-x-4">
    <button id="reset" class="px-6 py-3  bg-gray-400 text-white font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 hidden">Capture Ulang</button>
    <button id="nextBtn" class="px-6 py-3  bg-white text-black font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 hidden">Selanjutnya</button>
</div>

<script>
(() => {
    const video = document.getElementById('video');
    const timerEl = document.getElementById('timer');
    const info = document.getElementById('info');
    const resetBtn = document.getElementById('reset');
    const nextBtn = document.getElementById('nextBtn');
    const previewContainer = document.getElementById('previewContainer');
    const globalTimerEl = document.getElementById('globalTimer');
    const orientationBtn = document.getElementById('orientationBtn');
    const orientationIcon = document.getElementById('orientationIcon');

    const durasi = {{ $durasi }};
    const totalPhotos = {{ $layout }};
    const orderId = "{{ $order->id }}";
    let totalWaktu = {{ $totalWaktu }};

    let countdown = durasi;
    let timerInterval;
    let globalTimerInterval;
    let stream, track, imageCapture;
    let isPortrait = true;
    let isMirror = true; // langsung mirror dari awal
    let capturedImages = Array(totalPhotos).fill(null);

    function formatTime(sec) {
        const m = String(Math.floor(sec / 60)).padStart(2, '0');
        const s = String(sec % 60).padStart(2, '0');
        return `${m}:${s}`;
    }

    function startGlobalTimer() {
        function updateGlobalTimer() {
            globalTimerEl.textContent = formatTime(totalWaktu);
            if (totalWaktu <= 0) {
                clearInterval(globalTimerInterval);
                globalTimerEl.textContent = "00:00";
            }
            totalWaktu--;
        }
        updateGlobalTimer();
        globalTimerInterval = setInterval(updateGlobalTimer, 1000);
    }

    function updateCounter() {
        const taken = capturedImages.filter(i => i).length;
        info.textContent = `Foto: ${taken}/${totalPhotos} Foto`;
    }

    function updateVideoTransform() {
        video.style.transformOrigin = 'center center';
        let rotateDeg = isPortrait ? 90 : 0;
        let scaleX = (isMirror && !isPortrait) ? -1 : 1;
        let scaleY = (isMirror && isPortrait) ? -1 : 1;
        video.style.transform = `rotate(${rotateDeg}deg) scale(${scaleX}, ${scaleY})`;
    }

    function renderPreview() {
        previewContainer.innerHTML = '';
        for (let i = 0; i < totalPhotos; i++) {
            const box = document.createElement('div');
            box.className = `relative flex items-center justify-center bg-gray-100 rounded-xl shadow-lg ${isPortrait ? 'aspect-[3/4]' : 'aspect-[4/3]'} border-2 border-dashed border-gray-300`;

            if (capturedImages[i]) {
                const img = document.createElement('img');
                img.src = capturedImages[i];
                img.className = "w-full h-full object-cover rounded-xl shadow-lg";
                box.appendChild(img);
            } else {
                const numberCircle = document.createElement('div');
                numberCircle.className = "w-16 h-16 flex items-center justify-center rounded-full border-4 border-gray-400 text-gray-500 text-2xl font-bold";
                numberCircle.textContent = i + 1;
                box.appendChild(numberCircle);
            }

            previewContainer.appendChild(box);
        }

        updateCounter();
        resetBtn.classList.toggle('hidden', !capturedImages.some(i => i));
        nextBtn.classList.toggle('hidden', !capturedImages.some(i => i));
    }
async function startCamera() {
    try {
        // Request video tanpa memaksakan resolusi
        stream = awaitnavigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 3840 },   // minta 4K
                height: { ideal: 2160 },
                frameRate: { ideal: 60 }
            },
            audio: false
            });


        video.srcObject = stream;
        track = stream.getVideoTracks()[0];

        // Ambil resolusi asli dari kamera
        const settings = track.getSettings();
        console.log("Camera settings:", settings);

        try {
            imageCapture = new ImageCapture(track);
        } catch (e) {
            imageCapture = null;
        }

        updateVideoTransform();
        startAutoCapture();

    } catch (err) {
        console.error(err);
        info.textContent = "Gagal mengakses kamera";
    }
}


    function loadImageFromBlob(blob) {
        return new Promise((resolve, reject) => {
            const url = URL.createObjectURL(blob);
            const img = new Image();
            img.onload = () => { URL.revokeObjectURL(url); resolve(img); };
            img.onerror = reject;
            img.src = url;
        });
    }

  async function takeSnapshot() {
     try {
        // Pastikan video siap
        await new Promise(resolve => {
            if(video.readyState >= 2) resolve();
            else video.onloadedmetadata = () => resolve();
        });

        // Ambil frame dari video
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Canvas output menyesuaikan orientasi
        const outputCanvas = document.createElement('canvas');
        if(isPortrait){
            outputCanvas.width = canvas.height; // swap untuk portrait
            outputCanvas.height = canvas.width;
        } else {
            outputCanvas.width = canvas.width;
            outputCanvas.height = canvas.height;
        }
        const ctxOut = outputCanvas.getContext('2d');

        ctxOut.save();
        if(isPortrait){
            // Translate ke tengah canvas baru
            ctxOut.translate(outputCanvas.width/2, outputCanvas.height/2);
            ctxOut.rotate(90 * Math.PI / 180);
            if(isMirror) ctxOut.scale(1, -1); // mirror horizontal
            ctxOut.drawImage(canvas, -canvas.width/2, -canvas.height/2);
        } else {
            if(isMirror) ctxOut.scale(-1, -1);
            ctxOut.drawImage(canvas, isMirror ? -canvas.width : 0, 0);
        }
        ctxOut.restore();

        // Konversi ke base64
        const base64data = outputCanvas.toDataURL('image/jpeg', 1.0);

        // Upload ke server
        const res = await fetch("{{ route('upload.photo') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_id: orderId, image: base64data })
        });

        const data = await res.json();
        if(data.success && data.url){
            const emptyIndex = capturedImages.findIndex(i => !i);
            if(emptyIndex !== -1) capturedImages[emptyIndex] = data.url;
            renderPreview();
        }

    } catch (err) {
        console.error(err);
    }
}


    function startAutoCapture() {
        let photoIndex = 0;
        function captureLoop() {
            if (photoIndex >= totalPhotos) return;
            countdown = durasi;
            timerEl.textContent = countdown;
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                countdown--;
                timerEl.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(timerInterval);
                    takeSnapshot().then(() => {
                        photoIndex++;
                        captureLoop();
                    });
                }
            }, 1000);
        }
        captureLoop();
    }

    resetBtn.addEventListener('click', async ()=>{
    if(!confirm("Yakin reset semua foto?")) return;

    try {
        const res = await fetch("{{ route('delete.all.photos') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ order_id: orderId })
        });

        const data = await res.json();
        if(data.success){
            capturedImages = Array(totalPhotos).fill(null);
            renderPreview();
            startAutoCapture(); // langsung mulai auto capture lagi
        } else {
            alert("Gagal reset foto!");
        }
    } catch (err) {
        console.error(err);
        alert("Terjadi error saat reset");
    }
});


    orientationBtn.addEventListener('click', ()=>{
        isPortrait = !isPortrait;
        updateVideoTransform();
        orientationIcon.style.transform = isPortrait ? 'rotate(0deg)' : 'rotate(90deg)';
        renderPreview();
    });

    nextBtn.addEventListener('click', ()=>{
        if(!capturedImages.some(i => i)){
            alert("Belum ada foto yang diambil!");
            return;
        }
        window.location.href = `/sticker/{{ $order->order_code }}`;
    });

    startCamera();
    startGlobalTimer();
    renderPreview();
})();
</script>
@endsection
