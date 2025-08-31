@extends('layouts.app')

@section('content')
@php
    $totalWaktu = isset($order->waktu) ? intval($order->waktu) * 60 : 300; // detik
@endphp

<div class="container mx-auto p-4">
    <h1 class="text-2xl text-white font-bold mb-4 text-center">
        Foto <span id="fotoCounter">0</span>/{{ $layout }}
    </h1>

    <!-- Global Timer -->
    <div class="absolute top-4 right-4 z-50 flex gap-2">
        <div class="pointer-events-none relative rounded-lg border-2 border-black bg-white px-3 py-1 shadow-black shadow-[4px_4px_0_0] flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
            </svg>
            <span id="globalTimer" class="text-lg font-extrabold text-gray-600 select-none">--:--</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="">
            <div class="relative w-full h-64 bg-black/80 rounded-lg flex items-center justify-center">
                <!-- <video id="liveVideo" autoplay playsinline class="w-full rounded-lg border border-gray-300"></video> -->
               
                <p id="poseText" class="absolute text-white p-2 text-lg font-bold">
                    Berpose dan menghadap ke kamera
                </p>

                <!-- Countdown Overlay -->
                <div id="countdownOverlay" 
                    class="absolute inset-0 flex items-center justify-center text-white text-6xl font-bold bg-black/60 opacity-0 transition-opacity duration-500 rounded-lg">
                </div>
            </div>

            <!-- Capture Button -->
            <button id="captureBtn" 
                    class="absolute bottom-4 right-1/2 transform translate-x-1/2 w-16 h-16 rounded-full border-4 border-blue-500 bg-white flex items-center justify-center shadow-lg hover:bg-blue-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h2l1-2h12l1 2h2v12H3V7z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11a3 3 0 100 6 3 3 0 000-6z" />
                </svg>
            </button>

            <p id="previewStatus" class="text-sm text-gray-500 mt-2 mt-24 text-center hidden"></p>
            <p id="status" class="mt-4 text-gray-700 text-center hidden"></p>
        </div>

        <!-- Preview Foto -->
        <div>
            <div id="previewContainer" class="grid grid-cols-2 gap-4 relative"></div>
        </div>
    </div>

    <!-- Tombol Next -->
    <div class="mt-8 flex justify-center">
         <button id="nextBtn" class="px-6 py-3 bg-white text-black font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 hidden">Selanjutnya</button>
    </div>
</div>

<script>
const orderCode = '{{ $order->order_code }}';
const layoutCount = {{ $layout }};
let currentIndex = 0;
let capturedImages = [];
let isTimeUp = false; // flag waktu habis

// -----------------------------
// Global Timer
// -----------------------------
let totalWaktu = {{ $totalWaktu }};
const globalTimerEl = document.getElementById("globalTimer");
let globalTimerInterval;

function formatTime(seconds) {
    let m = Math.floor(seconds / 60);
    let s = seconds % 60;
    return String(m).padStart(2,"0")+":"+String(s).padStart(2,"0");
}

function startGlobalTimer() {
    function updateGlobalTimer() {
        globalTimerEl.textContent = formatTime(totalWaktu);
        if (totalWaktu <= 0) {
            clearInterval(globalTimerInterval);
            globalTimerEl.textContent = "00:00";
            waktuHabis();
        }
        totalWaktu--;
    }
    updateGlobalTimer();
    globalTimerInterval = setInterval(updateGlobalTimer, 1000);
}

function waktuHabis(){
    isTimeUp = true;
    // disable capture button
    document.getElementById("captureBtn").disabled = true;
    document.getElementById("captureBtn").classList.add("opacity-50","cursor-not-allowed");
    // tampilkan tombol next
    nextBtn.classList.remove("hidden");
    alert("Waktu habis! Silakan lanjut ke tahap berikutnya.");
}

// -----------------------------
// Generate Placeholder + Recapture Button
// -----------------------------
const previewContainer = document.getElementById("previewContainer");
for (let i = 1; i <= layoutCount; i++) {
    const slot = document.createElement("div");
    slot.className = "relative w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 font-bold";
    slot.textContent = i;
    slot.dataset.index = i;

    const recBtn = document.createElement('button');
    recBtn.innerText = "Recapture";
    recBtn.className = "absolute top-1 right-1 bg-white/90 px-2 py-1 rounded text-xs font-semibold hover:bg-white transition";
    slot.appendChild(recBtn);
    recBtn.addEventListener("click", () => {
        if(isTimeUp){
            alert("Waktu sudah habis, tidak bisa recapture lagi.");
            return;
        }
        recapture(slot);
    });

    previewContainer.appendChild(slot);
}

// -----------------------------
// WebSocket
// -----------------------------
const ws = new WebSocket("ws://localhost:3000");
ws.onmessage = function(event) {
    let msg;
    try { msg = JSON.parse(event.data); } catch (e) { return; }

    if (msg.url) {
        const slot = previewContainer.children[currentIndex];
        slot.innerHTML = "";

        const imgEl = document.createElement('img');
        imgEl.src = msg.url + '?t=' + new Date().getTime();
        imgEl.className = "w-full h-full rounded-lg border border-gray-300 object-cover";
        slot.appendChild(imgEl);

        if (msg.id) slot.dataset.photoId = msg.id;

        const recBtn = document.createElement('button');
        recBtn.innerText = "Recapture";
        recBtn.className = "absolute top-1 right-1 bg-white/90 px-2 py-1 rounded text-xs font-semibold hover:bg-white transition";
        slot.appendChild(recBtn);
        recBtn.addEventListener("click", () => {
            if(isTimeUp){
                alert("Waktu sudah habis, tidak bisa recapture lagi.");
                return;
            }
            recapture(slot);
        });

        slot.dataset.filled = "true";

        // update counter
        document.getElementById("fotoCounter").innerText = 
            document.querySelectorAll("#previewContainer [data-filled='true']").length;

        capturedImages[currentIndex] = true;

        if (currentIndex < layoutCount - 1) currentIndex++;
    }
};

// -----------------------------
// Recapture Function
// -----------------------------
function recapture(slot) {
    const photoId = slot.dataset.photoId;
    const slotIndex = parseInt(slot.dataset.index) - 1;

    if (!photoId) {
        currentIndex = slotIndex;
        ws.send(JSON.stringify({ action: "capture", order_code: orderCode }));
        return;
    }

    if (!confirm("Apakah ingin capture ulang foto ini?")) return;

    fetch("{{ route('photos.destroy', ':id') }}".replace(':id', photoId), {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
    })
    .then(res => res.json())
    .then(res => {
        if (res.message) {
            slot.innerHTML = slot.dataset.index;
            slot.dataset.filled = "";
            slot.dataset.photoId = "";
            currentIndex = slotIndex;
            capturedImages[slotIndex] = false;

            const recBtn = document.createElement('button');
            recBtn.innerText = "Recapture";
            recBtn.className = "absolute top-1 right-1 bg-white/90 px-2 py-1 rounded text-xs font-semibold hover:bg-white transition";
            slot.appendChild(recBtn);
            recBtn.addEventListener("click", () => {
                if(isTimeUp){
                    alert("Waktu sudah habis, tidak bisa recapture lagi.");
                    return;
                }
                recapture(slot);
            });

            ws.send(JSON.stringify({ action: "capture", order_code: orderCode }));
        }
    });
}

// -----------------------------
// Tombol Capture
// -----------------------------
document.getElementById("captureBtn").addEventListener("click", function() {
    if(isTimeUp){
        alert("Waktu sudah habis, tidak bisa mengambil foto lagi.");
        return;
    }
    if (currentIndex >= layoutCount) {
        alert("Foto sudah mencapai batas maksimal (" + layoutCount + ")");
        return;
    }
    startCountdown(() => ws.send(JSON.stringify({ action: "capture", order_code: orderCode })));
});

// -----------------------------
// Countdown
// -----------------------------
function startCountdown(callback) {
    let counter = 3;
    const poseText = document.getElementById("poseText");
    const countdownOverlay = document.getElementById("countdownOverlay");

    poseText.classList.add("hidden");
    countdownOverlay.textContent = counter;
    countdownOverlay.classList.remove("opacity-0");
    countdownOverlay.classList.add("opacity-100");

    const interval = setInterval(() => {
        counter--;
        if (counter > 0) {
            countdownOverlay.textContent = counter;
        } else {
            clearInterval(interval);
            countdownOverlay.textContent = "";
            countdownOverlay.classList.remove("opacity-100");
            countdownOverlay.classList.add("opacity-0");
            poseText.classList.remove("hidden");
            callback();
        }
    }, 1000);
}

// -----------------------------
// Tombol Next
// -----------------------------
const nextBtn = document.getElementById("nextBtn");
nextBtn.addEventListener('click', ()=>{
    if(!capturedImages.some(i => i)){
        alert("Belum ada foto yang diambil!");
        return;
    }
    window.location.href = `/filter/${orderCode}`;
});

// mulai timer
startGlobalTimer();
</script>
@endsection
