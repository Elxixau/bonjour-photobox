@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 text-center">
    <h1 class="text-2xl font-bold mb-4">DigiCam Capture via WebSocket</h1>

    <!-- Live preview video -->
    <video id="liveVideo" autoplay playsinline class="w-full max-w-md mx-auto rounded-lg border border-gray-300 mb-4"></video>

    <button id="captureBtn" class="px-6 py-3 bg-blue-500 text-white rounded-lg">Capture</button>
    <p id="status" class="mt-4 text-gray-700"></p>
    <div id="previewContainer" class="mt-4 grid grid-cols-2 gap-4"></div>
</div>

<script>
    const orderCode = '{{ $order->order_code }}'; // ambil dari controller
    const ws = new WebSocket("ws://localhost:3000");

    ws.onopen = function() {
        console.log("Connected to WebSocket");
        document.getElementById("status").innerText = "Connected to server";
    };

    ws.onmessage = function(event) {
        console.log("Message from server:", event.data);

        try {
            const msg = JSON.parse(event.data);
            if (msg.url) {
                const imgEl = document.createElement('img');
                imgEl.src = msg.url;
                imgEl.className = "w-full h-auto rounded-lg border border-gray-300";
                document.getElementById("previewContainer").appendChild(imgEl);
            } else {
                document.getElementById("status").innerText = msg.message || event.data;
            }
        } catch {
            document.getElementById("status").innerText = event.data;
        }
    };

    ws.onclose = function() {
        document.getElementById("status").innerText = "Disconnected from server";
    };

    document.getElementById("captureBtn").addEventListener("click", function() {
        ws.send(JSON.stringify({ action: "capture", order_code: orderCode }));
    });

    // Tambahan: Live preview pakai getUserMedia
    const videoEl = document.getElementById("liveVideo");
    navigator.mediaDevices.getUserMedia({ video: true, audio: false })
        .then(stream => {
            videoEl.srcObject = stream;
        })
        .catch(err => {
            console.error("Cannot access camera:", err);
            document.getElementById("status").innerText = "Cannot access camera";
        });
</script>
@endsection
