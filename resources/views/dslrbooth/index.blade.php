@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Buka/Tutup Notepad via WebSocket</h1>

    <button id="openNotepad" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Buka Notepad</button>
    <button id="closeNotepad" class="bg-red-500 text-white px-4 py-2 rounded">Tutup Notepad</button>

    <p class="mt-4">Status koneksi: <span id="status">Disconnected</span></p>
</div>
@endsection

@section('scripts')
<script>
const ws = new WebSocket('ws://localhost:8080');
const statusEl = document.getElementById('status');

ws.onopen = () => statusEl.textContent = 'Connected';
ws.onclose = () => statusEl.textContent = 'Disconnected';
ws.onmessage = (event) => console.log('Message from server:', event.data);

document.getElementById('openNotepad').addEventListener('click', () => {
    ws.send('open_notepad');
});

document.getElementById('closeNotepad').addEventListener('click', () => {
    ws.send('close_notepad');
});
</script>
@endsection
