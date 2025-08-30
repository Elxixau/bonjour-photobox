@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4 text-center">DigiCam Capture via WebSocket</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="">
            <div class="relative">
                <!-- Live Preview -->
                <video id="liveVideo" autoplay playsinline class="w-full rounded-lg border border-gray-300"></video>
                
                <!-- Countdown Overlay (untuk live video saja) -->
                <div id="countdownOverlay" 
                    class="absolute inset-0 flex items-center justify-center text-white text-6xl font-bold bg-black/40 opacity-0 transition-opacity duration-500 pointer-events-none rounded-lg">
                </div>

            </div>
            <!-- Capture Button (Floating Icon) -->
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
            <h2 class="text-lg font-semibold mb-2">Preview Foto</h2>
            <div id="previewContainer" class="grid grid-cols-2 gap-4 relative"></div>
        </div>
    </div>
</div>

<script>
const orderCode = '{{ $order->order_code }}';
const layoutCount = 4;
let currentIndex = 0;

// -----------------------------
// Generate Placeholder + Recapture Button
// -----------------------------
const previewContainer = document.getElementById("previewContainer");
for (let i = 1; i <= layoutCount; i++) {
    const slot = document.createElement("div");
    slot.className = "relative w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 font-bold";
    slot.textContent = i;
    slot.dataset.index = i;

    // Tombol recapture (tulisan)
    const recBtn = document.createElement('button');
    recBtn.innerText = "Recapture";
    recBtn.className = "absolute top-1 right-1 bg-white/90 px-2 py-1 rounded text-xs font-semibold hover:bg-white transition";
    slot.appendChild(recBtn);

    recBtn.addEventListener("click", () => recapture(slot));

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
};
ws.onmessage = function(event) {
    let msg;
    try {
        msg = JSON.parse(event.data); // coba parse
    } catch (e) {
        document.getElementById("status").innerText = event.data;
        console.log("Plain text message:", event.data);
        return;
    }

    if (msg.url) {
        const slot = previewContainer.children[currentIndex];
        slot.innerHTML = "";

        const imgEl = document.createElement('img');
        imgEl.src = msg.url + '?t=' + new Date().getTime();
        imgEl.className = "w-full h-full rounded-lg border border-gray-300 object-cover";
        slot.appendChild(imgEl);

        if (msg.id) slot.dataset.photoId = msg.id;

        // tombol recapture
        const recBtn = document.createElement('button');
        recBtn.innerText = "Recapture";
        recBtn.className = "absolute top-1 right-1 bg-white/90 px-2 py-1 rounded text-xs font-semibold hover:bg-white transition";
        slot.appendChild(recBtn);
        recBtn.addEventListener("click", () => recapture(slot));

        slot.dataset.filled = "true";
        if (currentIndex < layoutCount - 1) currentIndex++;
    } else if (msg.message) {
        document.getElementById("status").innerText = msg.message;
    }
};

ws.onclose = () => {
    document.getElementById("status").innerText = "Disconnected from server";
};

// -----------------------------
// Recapture Function
// -----------------------------
function recapture(slot) {
    const photoId = slot.dataset.photoId;
    const slotIndex = parseInt(slot.dataset.index) - 1;

    startCountdown(() => {
        if (!photoId) {
            // Kosong → langsung capture
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
                // kosongkan slot
                slot.innerHTML = slot.dataset.index;
                slot.dataset.filled = "";
                slot.dataset.photoId = "";
                currentIndex = slotIndex;

                // tambahkan tombol recapture lagi
                const recBtn = document.createElement('button');
                recBtn.innerText = "Recapture";
                recBtn.className = "absolute top-1 right-1 bg-white/90 px-2 py-1 rounded text-xs font-semibold hover:bg-white transition";
                slot.appendChild(recBtn);
                recBtn.addEventListener("click", () => recapture(slot));

                // langsung capture ulang
                ws.send(JSON.stringify({ action: "capture", order_code: orderCode }));
            }
        });
    });
}

// -----------------------------
// Tombol Capture Manual
// -----------------------------
const captureBtn = document.getElementById("captureBtn");
const countdownOverlay = document.getElementById("countdownOverlay");

captureBtn.addEventListener("click", function() {
    startCountdown(() => ws.send(JSON.stringify({ action: "capture", order_code: orderCode })));
});

// -----------------------------
// Countdown Function
// -----------------------------
function startCountdown(callback) {
    let counter = 3;
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
            callback();
        }
    }, 1000);
}
</script>
@endsection
