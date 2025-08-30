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
 let isPortrait = "{{ $orientasi }}"?.trim() === 'portrait';
 

    console.log("Orientasi dari DB:", "{{ $orientasi }}", "isPortrait:", isPortrait);

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
        box.className = `relative flex items-center justify-center bg-gray-100 rounded-xl shadow-lg aspect-[3/4] border-2 border-dashed border-gray-300`;

        if (capturedImages[i]) {
            const img = document.createElement('img');
            img.src = capturedImages[i];
            img.className = "w-full h-full object-cover rounded-xl shadow-lg";
            box.appendChild(img);

            // Tombol retake per foto
            const retakeBtn = document.createElement('button');
            retakeBtn.textContent = "↺ Retake";
            retakeBtn.className = "absolute top-2 right-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded";
            retakeBtn.addEventListener('click', async () => {
                const { isConfirmed } = await Swal.fire({
                    title: 'lLakukan Retake?',
                    text: "Foto akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, retake!',
                    cancelButtonText: 'Batal',
                });
                if(!isConfirmed) return;

                // Hapus dari server
                try {
                    const res = await fetch("{{ route('delete.single.photo') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            order_id: orderId,
                            image_url: capturedImages[i]
                        })
                    });

                    const data = await res.json();
                    if(data.success){
                        capturedImages[i] = null;
                        renderPreview();

                        // langsung ulangi capture untuk slot ini
                        countdown = durasi;
                        timerEl.textContent = countdown;
                        clearInterval(timerInterval);
                        timerInterval = setInterval(() => {
                            countdown--;
                            timerEl.textContent = countdown;
                            if (countdown <= 0) {
                                clearInterval(timerInterval);
                                takeSnapshot().then(() => {
                                    renderPreview();
                                });
                            }
                        }, 1000);
                    } else {
                        alert("Gagal menghapus foto di server!");
                    }
                } catch (err) {
                    console.error(err);
                    alert("Terjadi error saat retake foto.");
                }
            });

            box.appendChild(retakeBtn);
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
        stream = await navigator.mediaDevices.getUserMedia({
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
        if (video.readyState < 2) {
            await new Promise(resolve => video.onloadedmetadata = resolve);
        }

        let vidWidth = video.videoWidth;
        let vidHeight = video.videoHeight;

        const scaleFactor = 3; // scale up 3x
        const outputCanvas = document.createElement('canvas');

        if (isPortrait) {
            outputCanvas.width = vidHeight * scaleFactor;
            outputCanvas.height = vidWidth * scaleFactor;
        } else {
            outputCanvas.width = vidWidth * scaleFactor;
            outputCanvas.height = vidHeight * scaleFactor;
        }

        const ctx = outputCanvas.getContext('2d');
        ctx.scale(scaleFactor, scaleFactor);

        ctx.save();

        if (isPortrait) {
            ctx.translate(outputCanvas.width / (2*scaleFactor), outputCanvas.height / (2*scaleFactor));
            ctx.rotate(90 * Math.PI / 180);
            if (isMirror) ctx.scale(1, -1);
            ctx.drawImage(video, -vidWidth / 2, -vidHeight / 2, vidWidth, vidHeight);
        } else {
            if (isMirror) {
                ctx.translate(vidWidth, 0);
                ctx.scale(-1, 1);
            }
            ctx.drawImage(video, 0, 0, vidWidth, vidHeight);
        }

        ctx.restore();

        // Konversi ke Blob JPEG kualitas maksimal
        const blob = await new Promise(resolve => outputCanvas.toBlob(resolve, 'image/jpeg', 1.0));

        // Convert ke base64 dan upload
        const reader = new FileReader();
        reader.readAsDataURL(blob);
        reader.onloadend = async () => {
            const base64data = reader.result;
            const res = await fetch("{{ route('upload.photo') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order_id: orderId, image: base64data })
            });
            const data = await res.json();
            if (data.success && data.url) {
                const emptyIndex = capturedImages.findIndex(i => !i);
                if (emptyIndex !== -1) capturedImages[emptyIndex] = data.url;
                renderPreview();
            }
        };

    } catch (err) {
        console.error(err);
    }
}



async function startAutoCaptureWithReminder() {
    await Swal.fire({
        title: 'Siap untuk sesi foto?',
        html: `<p>Foto akan diambil otomatis dalam <strong>${durasi}</strong> detik, setelah mulai.</p>`,
        showCancelButton: true,
        confirmButtonText: 'Mulai',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusConfirm: false,
        customClass: {
            popup: 'rounded-xl border-3 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 p-6',
            title: 'text-xl font-serif font-semibold text-gray-900',
            htmlContainer: 'text-gray-700 text-sm mt-2',
            confirmButton: 'bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800 transition ml-2',
            cancelButton: 'bg-gray-200 text-gray-800 px-6 py-2 rounded-md hover:bg-gray-300 transition'
        },
        buttonsStyling: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    });

    

// update ikon sesuai orientasi awal
    orientationIcon.style.transform = isPortrait ? 'rotate(0deg)' : 'rotate(90deg)';

    startAutoCapture();
    startCamera();
    renderPreview();

    startGlobalTimer();
}



    function startAutoCapture() {
        let photoIndex = 0;
        function captureLoop() {
            if (photoIndex >= totalPhotos) return;
            countdown = durasi;
            timerEl.textContent = countdown;
                  timerEl.classList.add("blink"); 
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                countdown--;
                timerEl.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(timerInterval);
                     timerEl.classList.remove("blink");
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
         const { isConfirmed } = await Swal.fire({
        title: 'Reset semua foto?',
        text: "Semua foto akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, reset!',
        cancelButtonText: 'Batal',
    });
    if(!isConfirmed) return;

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
            await Swal.fire('Bersiap untuk capture ulang!', '', 'success');
            startAutoCapture(); // langsung mulai auto capture lagi
        } else {
            await Swal.fire('Gagal reset foto!', '', 'error');
        }
    } catch (err) {
        console.error(err);
         await Swal.fire('erjadi error saat reset', '', 'error');
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
        window.location.href = `/filter/{{ $order->order_code }}`;
    });

    
    startAutoCaptureWithReminder();
   
})();
</script>
@endsection
