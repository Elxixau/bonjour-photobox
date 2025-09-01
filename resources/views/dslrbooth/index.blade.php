@extends('layouts.app')

@section('content')
 <h1>Photobooth WebApp</h1>
  <button onclick="startSession()">Mulai Sesi</button>
    <div id="timer">60</div>

    <script>
        const ws = new WebSocket('ws://localhost:8080');

        ws.onopen = () => console.log('Connected to PC agent');

        function startSession() {
            ws.send(JSON.stringify({ type: 'startSession', duration: 60 }));
        }

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);

            if (data.type === 'timer') {
                document.getElementById('timer').innerText = data.remaining;
            }

            if (data.type === 'sessionEnd') {
                alert('Sesi photobooth selesai!');
                // window.location.href = "/next-page"; // redirect halaman berikutnya
            }
        };
    </script>
@endsection
