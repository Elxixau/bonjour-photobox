<!DOCTYPE html>
<html>
<head>
    <title>Photobooth Timer</title>
    <style>
        #timer {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 10px 20px;
            font-size: 24px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
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
</body>
</html>
