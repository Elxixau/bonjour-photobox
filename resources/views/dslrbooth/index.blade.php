@extends('layouts.app')

@section('content')
<div class="container text-center mt-10">
    <h1>Photobooth Session</h1>
    <p>Sesi akan berjalan selama {{ $order->waktu ?? 5 }} menit</p>
<button id="startBtn">Mulai Photobooth</button>
</div>


 <script>
        const PHOTBOOTH_WS = 'ws://localhost:8090'; // IP PC Photobooth
        const orderCode = "{{ $order->order_code }}";
        const durationSeconds =" {{ $order->waktu }}"; // ambil dari DB

        document.getElementById('startBtn').addEventListener('click', () => {
            const ws = new WebSocket(PHOTBOOTH_WS);

            ws.onopen = () => {
                ws.send(JSON.stringify({
                    type:'startSession',
                    order_code: orderCode,
                    duration: durationSeconds
                }));
            };

            ws.onmessage = e => {
                const data = JSON.parse(e.data);
                if(data.type==='timer'){
                    console.log('Remaining:', data.remaining);
                }
                if(data.type==='sessionEnd'){
                    alert('Sesi photobooth selesai!');
                }
            };
        });
    </script>
@endsection
