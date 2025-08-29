@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">DigiCam Capture via WebSocket</h1>

    <!-- Tombol Capture -->
    <button id="captureBtn" class="px-4 py-2 bg-blue-500 text-white rounded">Capture</button>

    <!-- Status koneksi dan pesan WebSocket -->
    <p id="status" class="mt-4 text-gray-700"></p>

    <!-- Container preview foto -->
    <h2 class="text-lg font-semibold mt-6 mb-2">Preview Foto:</h2>
    <div id="previewContainer" class="grid grid-cols-3 gap-4"></div>
</div>

<script>
    let ws = new WebSocket("ws://localhost:3000");

    ws.onopen = function() {
        console.log("Connected to WebSocket");
        document.getElementById("status").innerText = "Connected to server";
    };

    ws.onmessage = function(event) {
        console.log("Message from server:", event.data);
        document.getElementById("status").innerText = event.data;

        // Jika pesan berisi nama file baru, tampilkan preview
        if(event.data.startsWith("New file captured:")) {
            const fileName = event.data.replace("New file captured: ", "");
            
            // URL file harus mengarah ke Laravel storage/public
            // Contoh: /storage/{order_code}/{fileName}
            const imgUrl = `/storage/${fileName}`; 

            const imgEl = document.createElement("img");
            imgEl.src = imgUrl;
            imgEl.className = "w-full h-auto rounded-lg border";

            document.getElementById("previewContainer").prepend(imgEl);
        }
    };

    ws.onclose = function() {
        document.getElementById("status").innerText = "Disconnected from server";
    };

    document.getElementById("captureBtn").addEventListener("click", function() {
        ws.send("capture");
    });
</script>
@endsection
