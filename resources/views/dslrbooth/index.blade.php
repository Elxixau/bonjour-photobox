@extends('layouts.app')

@section('content')
<div class="container text-center mt-10">
    <h1>Photobooth Session</h1>
    <p>Sesi akan berjalan selama {{ $order->waktu ?? 5 }} menit</p>

    <button id="startSessionBtn">Mulai Sesi</button>
    <div id="statusMessage" class="mt-4 text-red-500"></div>
</div>

<script>
    const durationSeconds = {{ ($order->waktu ?? 5) * 60 }};
    const orderCode = '{{ $order->order_code }}';

    const statusDiv = document.getElementById('statusMessage');

    document.getElementById('startSessionBtn').addEventListener('click', async ()=>{
        statusDiv.innerText = ''; // reset

        try {
            const response = await fetch('http://localhost:8091', {
                method: 'POST',
                headers: { 'Content-Type':'application/json' },
                body: JSON.stringify({ order_code: orderCode, duration: durationSeconds })
            });

            if(!response.ok){
                const text = await response.text();
                statusDiv.innerText = `Gagal mengirim perintah: ${response.status} ${text}`;
                return;
            }

            statusDiv.innerText = 'Perintah photobooth berhasil dikirim ke PC lokal';
        } catch (err) {
            statusDiv.innerText = `Error: ${err.message}. Pastikan Node.js Agent sedang berjalan dan port 8091 terbuka.`;
            console.error(err);
        }
    });
</script>
@endsection
