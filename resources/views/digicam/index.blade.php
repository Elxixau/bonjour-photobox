@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 text-center">
    <h1 class="text-2xl font-bold mb-4">DigiCam Capture via WebSocket</h1>
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

        // Jika server mengirim URL foto dari Laravel
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
        // Kirim capture request beserta order_code
        ws.send(JSON.stringify({ action: "capture", order_code: orderCode }));
    });
</script>
@endsection
