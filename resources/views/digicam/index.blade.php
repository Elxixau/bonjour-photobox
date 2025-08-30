@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4 text-center">DigiCam Capture via WebSocket</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <!-- Live Preview -->
            <div class="relative w-full">
                <video id="liveVideo" autoplay playsinline class="w-full rounded-lg border border-gray-300"></video>
                <div id="countdownOverlay" 
                    class="absolute inset-0 flex items-center justify-center text-white text-6xl font-bold bg-black/40 opacity-0 transition-opacity duration-500 pointer-events-none">
                </div>
            </div>
            <p id="previewStatus" class="text-sm text-gray-500 mt-2"></p>

            <div class="mt-4 text-center">
                <button id="captureBtn" class="px-6 py-3 bg-blue-500 text-white rounded-lg">Capture</button>
                <p id="status" class="mt-4 text-gray-700"></p>
            </div>
        </div>

        <!-- Preview Foto -->
        <div>
            <h2 class="text-lg font-semibold mb-2">Preview Foto</h2>
            <div id="previewContainer" class="grid grid-cols-2 gap-4"></div>
        </div>
    </div>
</div>

<script>
const orderCode = '{{ $order->order_code }}';
const layoutCount = 4;
let currentIndex = 0;
const captureInterval = 3000;

// -----------------------------
// Generate Placeholder
// -----------------------------
const previewContainer = document.getElementById("previewContainer");
for (let i = 1; i <= layoutCount; i++) {
    const slot = document.createElement("div");
    slot.className = "relative w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 font-bold";
    slot.textContent = i;
    slot.dataset.index = i;
    previewContainer.appendChild(slot);
}

// -----------------------------
// Live Preview
// -----------------------------
const videoEl = document.getElementById("liveVideo");
navigator.mediaDevices.getUserMedia({ video: true, audio: false })
    .then(stream => {
        videoEl.srcObject = stream;
        document.getElementById("previewStatus").innerText = "Live preview aktif";
    })
    .catch(err => {
        console.error("Cannot access camera:", err);
        document.getElementById("previewStatus").innerText = "Tidak bisa akses kamera";
    });

// -----------------------------
// WebSocket
// -----------------------------
const ws = new WebSocket("ws://localhost:3000");

ws.onopen = () => {
    document.getElementById("status").innerText = "Connected to server";
    startAutoCapture();
};

ws.onmessage = function(event) {
    try {
        const msg = JSON.parse(event.data);
        if (msg.thumb_url && msg.id) {
            const slot = previewContainer.children[currentIndex];
            slot.innerHTML = "";

            // Thumbnail
            const imgEl = document.createElement('img');
            imgEl.src = msg.thumb_url;
            imgEl.className = "w-full h-full rounded-lg border border-gray-300 object-cover";
            slot.appendChild(imgEl);

            // Tombol Recapture pojok kanan atas
            const recBtn = document.createElement('button');
            recBtn.innerHTML = "🔄";
            recBtn.className = "absolute top-1 right-1 bg-white/80 px-1 rounded text-sm hover:bg-white/100";
            slot.appendChild(recBtn);

            slot.dataset.filled = "true";

            recBtn.addEventListener("click", () => recapture(slot, msg.id));

            currentIndex++;
        } else {
            document.getElementById("status").innerText = msg.message || event.data;
        }
    } catch {
        document.getElementById("status").innerText = event.data;
    }
};

ws.onclose = () => {
    document.getElementById("status").innerText = "Disconnected from server";
};

// -----------------------------
// Auto Capture sesuai layoutCount
// -----------------------------
function startAutoCapture() {
    const interval = setInterval(() => {
        if (currentIndex < layoutCount) {
            ws.send(JSON.stringify({ action: "capture", order_code: orderCode }));
        } else {
            clearInterval(interval);
        }
    }, captureInterval);
}

// -----------------------------
// Recapture
// -----------------------------
function recapture(slot, photoId) {
    if (!confirm("Apakah ingin capture ulang foto ini?")) return;

    fetch("{{ route('photos.destroy') }}", {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ id: photoId })
    })
    .then(res => res.json())
    .then(res => {
        if (res.message) {
            // Kosongkan slot
            slot.innerHTML = slot.dataset.index;
            slot.dataset.filled = "";
            currentIndex = parseInt(slot.dataset.index) - 1;

            // Capture ulang slot ini
            ws.send(JSON.stringify({ action: "capture", order_code: orderCode }));
        }
    });
}

// -----------------------------
// Tombol Capture manual
// -----------------------------
const captureBtn = document.getElementById("captureBtn");
const countdownOverlay = document.getElementById("countdownOverlay");

captureBtn.addEventListener("click", function() {
    startCountdown(() => ws.send(JSON.stringify({ action: "capture", order_code: orderCode })));
});

function startCountdown(callback) {
    let counter = 3;
    const showCountdown = () => {
        countdownOverlay.textContent = counter;
        countdownOverlay.classList.remove("opacity-0");
        countdownOverlay.classList.add("opacity-100");
        setTimeout(() => {
            countdownOverlay.classList.remove("opacity-100");
            countdownOverlay.classList.add("opacity-0");
        }, 500);
    };
    showCountdown();
    const interval = setInterval(() => {
        counter--;
        if (counter > 0) showCountdown();
        else {
            clearInterval(interval);
            countdownOverlay.textContent = "";
            callback();
        }
    }, 1000);
}
</script>
@endsection
