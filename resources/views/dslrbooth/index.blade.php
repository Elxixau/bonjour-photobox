@extends('layouts.app')

@section('content')
<div class="container text-center mt-10">
    <h1>Photobooth Session</h1>
    <p>Sesi akan berjalan selama {{ $order->waktu ?? 5 }} menit</p>

    <button id="startSessionBtn">Mulai Sesi</button>
</div>

<script>
    const durationSeconds = {{ $order->waktu * 60 ?? 300 }};

    document.getElementById('startSessionBtn').addEventListener('click', () => {
        if(window.electronAPI){
            window.electronAPI.sendStartSession(durationSeconds);
            window.electronAPI.startTimer('ws://localhost:8090');
            alert("Sesi photobooth dimulai!");
        } else {
            alert("Electron overlay tidak terdeteksi.");
        }
    });
</script>
@endsection
