@extends('layouts.app')

@section('content')
<div class="container text-center mt-10">
    <h1>Photobooth Session</h1>
    <p>Sesi akan berjalan selama {{ $order->waktu ?? 5 }} menit</p>
    <button id="startBtn">Mulai Photobooth</button>
</div>

<script>
    const WS_URL = 'ws://localhost:8090'; // ganti dengan IP PC Photobooth
    const orderCode = "{{ $order->order_code }}";
    const durationMinutes = {{ $order->waktu ?? 5 }};

    let ws;

    document.getElementById('startBtn').addEventListener('click', () => {
        if(!ws || ws.readyState !== WebSocket.OPEN){
            ws = new WebSocket(WS_URL);

            ws.onopen = () => {
                console.log('Connected to Node.js Agent');
                sendStartSession();
            };

            ws.onmessage = e => {
                const data = JSON.parse(e.data);
                if(data.type === 'timer'){
                    console.log('Remaining:', data.remaining);
                }
                if(data.type === 'sessionEnd'){
                    alert('Sesi photobooth selesai!');
                }
            };

            ws.onerror = e => console.error('WebSocket error:', e);
            ws.onclose = () => console.log('WebSocket closed');
        } else {
            sendStartSession();
        }
    });

    function sendStartSession(){
        ws.send(JSON.stringify({
            type: 'startSession',
            order_code: orderCode,
            duration: durationMinutes
        }));
    }
</script>
@endsection
