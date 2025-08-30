@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4 text-center">DigiCam Capture via WebSocket</h1>

    <!-- Live Preview -->
    <video id="liveVideo" autoplay playsinline 
        class="w-full max-w-md mx-auto rounded-lg border border-gray-300 mb-4"></video>

    <button id="captureBtn" 
        class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Capture</button>

    <p id="status" class="mt-4 text-center text-gray-600"></p>

    <!-- Preview hasil capture -->
    <div id="previewContainer" class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
</div>

<script>
    const video = document.getElementById('liveVideo');
    const captureBtn = document.getElementById('captureBtn');
    const status = document.getElementById('status');
    const previewContainer = document.getElementById('previewContainer');

    // === WebSocket Setup ===
    const socket = new WebSocket("ws://localhost:3000");

    socket.onopen = () => {
        status.textContent = "Connected to camera...";
    };

    socket.onerror = (err) => {
        status.textContent = "WebSocket Error: " + err;
    };

    // === Menerima hasil capture dari WebSocket ===
    socket.onmessage = async (event) => {
        const data = JSON.parse(event.data);

        if (data.type === "capture") {
            const imageUrl = data.url; // URL foto asli (HD)
            
            // Buat preview compress
            const previewUrl = await createCompressedPreview(imageUrl, 0.4); 
            // 0.4 artinya kualitas 40%

            // Tambahkan ke container
            const img = document.createElement("img");
            img.src = previewUrl;
            img.className = "w-full rounded-lg border border-gray-300";
            previewContainer.appendChild(img);
        }
    };

    // === Request capture ke server ===
    captureBtn.addEventListener("click", () => {
        socket.send(JSON.stringify({ action: "capture" }));
        status.textContent = "Capturing...";
    });

    // === Fungsi bikin preview compress ===
    async function createCompressedPreview(imageUrl, quality = 0.5) {
        return new Promise((resolve) => {
            const img = new Image();
            img.crossOrigin = "anonymous"; 
            img.onload = () => {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                // Ukuran kecil biar preview lebih ringan
                const MAX_WIDTH = 400;
                const scale = MAX_WIDTH / img.width;
                canvas.width = MAX_WIDTH;
                canvas.height = img.height * scale;

                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                // Export ke jpeg dengan kualitas rendah
                const compressedDataUrl = canvas.toDataURL("image/jpeg", quality);
                resolve(compressedDataUrl);
            };
            img.src = imageUrl;
        });
    }
</script>
@endsection
