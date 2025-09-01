@extends('layouts.app')

@section('content')
<div class="container text-center mt-10">
    <h1>Photobooth Session</h1>
    <p>Sesi akan berjalan selama {{ $order->waktu ?? 5 }} menit</p>

    <button id="startSessionBtn">Mulai Sesi</button>
</div>

<script>
    const durationSeconds = {{ $order->waktu*60 ?? 300 }};
const orderCode = '{{ $order->order_code }}';

document.getElementById('startSessionBtn').addEventListener('click', ()=>{
    fetch('http://localhost:8091', {
        method:'POST',
        body: JSON.stringify({order_code: orderCode, duration: durationSeconds})
    }).then(()=>{
        alert('Perintah photobooth dikirim ke PC lokal');
    }).catch(()=>{
        alert('Pastikan Node.js Agent sudah berjalan di PC Photobooth');
    });
});
</script>
@endsection
