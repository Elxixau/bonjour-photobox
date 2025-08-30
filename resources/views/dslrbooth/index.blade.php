@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Kontrol Aplikasi via WebSocket</h1>

    <div>
        <p>Status koneksi: <span id="status">Disconnected</span></p>
    </div>

    <button id="openNotepad" class="bg-blue-500 text-white px-4 py-2 rounded">Buka Notepad</button>
    <button id="closeNotepad" class="bg-red-500 text-white px-4 py-2 rounded">Tutup Notepad</button>
</div>
@endsection

@section('scripts')
<script>
    // Ganti dengan alamat server WebSocket kamu
    const ws = new WebSocket('ws://localhost:8080');

    const statusEl = document.getElementById('status');

    ws.onopen = () => {
        statusEl.textContent = 'Connected';
        statusEl.classList.add('text-green-500');
    };

    ws.onclose = () => {
        statusEl.textContent = 'Disconnected';
        statusEl.classList.remove('text-green-500');
        statusEl.classList.add('text-red-500');
    };

    ws.onmessage = (event) => {
        console.log('Message from server:', event.data);
    };

    document.getElementById('openNotepad').addEventListener('click', () => {
        ws.send(JSON.stringify({ action: 'open_app', app: 'notepad.exe' }));
    });

    document.getElementById('closeNotepad').addEventListener('click', () => {
        ws.send(JSON.stringify({ action: 'close_app', app: 'notepad.exe' }));
    });
</script>
@endsection
