@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4 text-center">DigiCam Capture via WebSocket</h1>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="">
            <!-- Left: Live preview -->
            <div class="relative w-full">
                <video id="liveVideo" autoplay playsinline class="w-full rounded-lg border border-gray-300"></video>

                <!-- Countdown overlay -->
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
        

        <!-- Right: Foto hasil -->
        <div>
            <h2 class="text-lg font-semibold mb-2">Preview Foto</h2>
            <div id="previewContainer" class="grid grid-cols-2 gap-4"></div>
        </div>
    </div>
</div>

<script>
    const orderCode = '{{ $order->order_code }}'; 
    const layoutCount = 4; // jumlah slot preview

    const previewContainer = document.getElementById("previewContainer");
    for (let i = 1; i <= layoutCount; i++) {
        const slot = document.createElement("div");
        slot.className = "w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 font-bold";
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

    ws.onopen = () => document.getElementById("status").innerText = "Connected to server";

    ws.onmessage = (event) => {
        try {
            const msg = JSON.parse(event.data);
            if (msg.url) {
                // replace dengan hasil HD dari server
                const imgEl = document.createElement('img');
                imgEl.src = msg.url + '?t=' + new Date().getTime();
                imgEl.className = "w-full h-full rounded-lg border border-gray-300 object-cover";

                const placeholders = document.querySelectorAll("#previewContainer div");
                for (let i = 0; i < placeholders.length; i++) {
                    if (!placeholders[i].dataset.filled) {
                        placeholders[i].innerHTML = "";
                        placeholders[i].appendChild(imgEl);
                        placeholders[i].dataset.filled = "true";
                        break;
                    }
                }
            }
        } catch {
            document.getElementById("status").innerText = event.data;
        }
    };

    ws.onclose = () => document.getElementById("status").innerText = "Disconnected from server";

    // -----------------------------
    // Capture + Canvas Preview
    // -----------------------------
    const countdownOverlay = document.getElementById("countdownOverlay");
    const captureBtn = document.getElementById("captureBtn");

    captureBtn.addEventListener("click", function() {
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
            if (counter > 0) {
                showCountdown();
            } else {
                clearInterval(interval);
                countdownOverlay.textContent = "";

                // --- Capture ke canvas ---
                const canvas = document.createElement("canvas");
                canvas.width = videoEl.videoWidth;
                canvas.height = videoEl.videoHeight;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);

                const imgEl = document.createElement("img");
                imgEl.src = canvas.toDataURL("image/jpeg", 0.9); // preview cepat
                imgEl.className = "w-full h-full rounded-lg border border-gray-300 object-cover";

                // masukkan ke slot kosong
                const placeholders = document.querySelectorAll("#previewContainer div");
                for (let i = 0; i < placeholders.length; i++) {
                    if (!placeholders[i].dataset.filled) {
                        placeholders[i].innerHTML = "";
                        placeholders[i].appendChild(imgEl);
                        placeholders[i].dataset.filled = "true";
                        break;
                    }
                }

                // --- kirim perintah capture ke server (hasil HD tetap dari WebSocket) ---
                ws.send(JSON.stringify({ action: "capture", order_code: orderCode }));
            }
        }, 1000);
    });
</script>

@endsection
