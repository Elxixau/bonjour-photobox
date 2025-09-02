@extends('layouts.app')

@section('content')
<div class="h-screen flex items-center justify-center bg-black text-white">
    <div id="overlay" class="text-center hidden">
        <h1 class="text-5xl font-bold mb-4">Waktu Tersisa</h1>
        <div id="timer" class="text-6xl">0</div>
    </div>
</div>

<script>
    let ws = new WebSocket("ws://localhost:8090");

    ws.onopen = function() {
        console.log("Terhubung ke Node.js server");
    };

    ws.onmessage = function(event) {
        let data = JSON.parse(event.data);

        if (data.type === "start") {
            // tampilkan overlay dan mulai hitung mundur
            document.getElementById("overlay").classList.remove("hidden");
            startCountdown(data.time);
        }

        if (data.type === "stop") {
            // sembunyikan overlay
            document.getElementById("overlay").classList.add("hidden");
        }
    };

    function startCountdown(seconds) {
        let timer = document.getElementById("timer");
        let remaining = seconds;

        timer.textContent = remaining;

        let interval = setInterval(() => {
            remaining--;
            timer.textContent = remaining;

            if (remaining <= 0) {
                clearInterval(interval);
                ws.send(JSON.stringify({ type: "done" }));
                document.getElementById("overlay").classList.add("hidden");
            }
        }, 1000);
    }
</script>
@endsection
