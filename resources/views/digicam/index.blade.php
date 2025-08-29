@extends('layouts.app')

@section('content')
@php
    $layout = $layout ?? 4;
    $orderId = $order->id ?? '';
@endphp

<div class="max-w-6xl mx-auto mt-6 text-center space-x-4">
    <button id="captureBtn" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300">
        Ambil Foto
    </button>
    <button id="reset" class="px-6 py-3 bg-gray-400 text-white font-semibold rounded-lg shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 hidden">Capture Ulang</button>
    <button id="nextBtn" class="px-6 py-3 bg-white text-black font-semibold rounded-lg shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 hidden">Selanjutnya</button>

    {{-- 🔹 Tombol test WebSocket --}}
    <button id="wsTestBtn" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300">
        Tes WebSocket
    </button>
</div>

<script>
    // 🔹 koneksi ke WebSocket server (ganti URL sesuai server kamu)
    const ws = new WebSocket("ws://localhost:8080");

    ws.onopen = function() {
        console.log("✅ Terhubung ke WebSocket");
    };

    ws.onmessage = function(event) {
        console.log("📩 Pesan dari server:", event.data);
        alert("Pesan dari server: " + event.data);
    };

    ws.onerror = function(err) {
        console.error("⚠️ WebSocket error:", err);
    };

    ws.onclose = function() {
        console.warn("❌ WebSocket ditutup");
    };

    // 🔹 button Tes WebSocket
    document.getElementById("wsTestBtn").addEventListener("click", () => {
        if(ws.readyState === WebSocket.OPEN){
            ws.send("hi"); // contoh kirim pesan "hi"
            console.log("📝 Kirim: hi");
        } else {
            alert("WebSocket belum terhubung");
        }
    });
</script>
@endsection
