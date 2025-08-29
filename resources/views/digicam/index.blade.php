@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Sesi Foto</h1>

    <button id="captureBtn" class="px-4 py-2 bg-blue-500 text-white rounded">Capture</button>
    <p id="status" class="mt-2 text-gray-700"></p>

    <h2 class="text-xl mt-4 mb-2">Preview Foto:</h2>
    <div id="previewContainer" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
</div>

<script>
let ws = new WebSocket("ws://localhost:3000");
const orderCode = '{{ $order->order_code }}';
const statusEl = document.getElementById('status');
const previewContainer = document.getElementById('previewContainer');

ws.onopen = () => {
    console.log("Connected to WebSocket");
    statusEl.innerText = "Connected to server";
};

ws.onmessage = (event) => {
    console.log("Message from server:", event.data);
    statusEl.innerText = event.data;

    // Jika server mengirim file baru
    if(event.data.startsWith("New file captured:")) {
        const fileName = event.data.replace("New file captured: ", "");
        const url = `/storage/${orderCode}/${fileName}`; // path dari Laravel storage:link
        const img = document.createElement('img');
        img.src = url;
        img.className = "w-full h-auto rounded shadow";
        previewContainer.appendChild(img);
    }
};

ws.onclose = () => {
    statusEl.innerText = "Disconnected from server";
};

// Tombol capture
document.getElementById("captureBtn").addEventListener("click", () => {
    ws.send(JSON.stringify({ action: "capture", order_code: orderCode }));
});
</script>
@endsection
