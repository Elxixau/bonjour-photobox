@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4 text-center">DigiCam Capture via WebSocket</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left: Live preview -->
        <div>
            <div class="relative w-full">
                <video id="liveVideo" autoplay playsinline class="w-full rounded-lg border border-gray-300"></video>
                <!-- Countdown -->
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
    const layoutCount = 4; // bisa 4,6,7,8
    let capturedCount = 0;

    const previewContainer = document.getElementById("previewContainer");

    // --- generate placeholder skeleton
    for (let i = 1; i <= layoutCount; i++) {
        const slot = document.createElement("div");
        slot.className =
            "w-full h-40 bg-gray-200 rounded-lg animate-pulse flex items-center justify-center text-gray-400 font-bold relative";
        slot.textContent = i;
        slot.dataset.index = i;
        previewContainer.appendChild(slot);
    }

    // --- Live Preview
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

    // --- WebSocket
    const ws = new WebSocket("ws://localhost:3000");

    ws.onopen = () => document.getElementById("status").innerText = "Connected to server";
    ws.onclose = () => document.getElementById("status").innerText = "Disconnected from server";

    ws.onmessage = function(event) {
        try {
            const msg = JSON.parse(event.data);
            if (msg.url) {
                const img = new Image();
                img.crossOrigin = "anonymous";
                img.src = msg.url + '?t=' + new Date().getTime();
                img.onload = () => {
                    // compress preview
                    const canvas = document.createElement("canvas");
                    const ctx = canvas.getContext("2d");
                    const maxWidth = 400;
                    const scale = maxWidth / img.width;
                    canvas.width = maxWidth;
                    canvas.height = img.height * scale;
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    const compressedDataUrl = canvas.toDataURL("image/jpeg", 0.5);

                    // cari slot pertama kosong
                    const placeholders = document.querySelectorAll("#previewContainer div");
                    for (let i = 0; i < placeholders.length; i++) {
                        const slot = placeholders[i];
                        if (!slot.dataset.filled) {
                            slot.innerHTML = "";

                            // img preview
                            const imgEl = document.createElement("img");
                            imgEl.src = compressedDataUrl;
                            imgEl.className = "w-full h-full rounded-lg border border-gray-300 object-cover";

                            // tombol recapture
                            const btn = document.createElement("button");
                            btn.innerText = "Recapture";
                            btn.className = "absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600";
                            btn.onclick = async () => {
                                await fetch(`/photos/delete/${msg.filename}`, {
                                    method: "DELETE",
                                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                                });
                                slot.innerHTML = slot.dataset.index;
                                delete slot.dataset.filled;
                                capturedCount--;
                                ws.send(JSON.stringify({ action: "capture", order_code: orderCode, replace: slot.dataset.index }));
                            };

                            slot.appendChild(imgEl);
                            slot.appendChild(btn);
                            slot.classList.remove("animate-pulse");
                            slot.dataset.filled = "true";
                            capturedCount++;

                            // auto stop jika sudah penuh
                            if (capturedCount >= layoutCount) {
                                document.getElementById("status").innerText = "Semua foto berhasil di-capture ✅";
                            }
                            break;
                        }
                    }
                };
            } else {
                document.getElementById("status").innerText = msg.message || event.data;
            }
        } catch {
            document.getElementById("status").innerText = event.data;
        }
    };

    // --- Capture + Countdown
    const countdownOverlay = document.getElementById("countdownOverlay");
    const captureBtn = document.getElementById("captureBtn");

    captureBtn.addEventListener("click", function() {
        let counter = 3; // countdown lebih singkat
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
                if (capturedCount < layoutCount) {
                    ws.send(JSON.stringify({ action: "capture", order_code: orderCode }));
                }
            }
        }, 1000);
    });
</script>
@endsection
