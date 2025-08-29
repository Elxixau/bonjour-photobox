@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">DigiCam Capture via WebSocket</h1>
    <button id="captureBtn" class="px-4 py-2 bg-blue-500 text-white rounded">Capture</button>
    <p id="status" class="mt-4 text-gray-700"></p>
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
    };

    ws.onclose = function() {
        document.getElementById("status").innerText = "Disconnected from server";
    };

    document.getElementById("captureBtn").addEventListener("click", function() {
        ws.send("capture");
    });
</script>
@endsection
