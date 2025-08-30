@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Buka Notepad via WebSocket</h1>

    <button id="openNotepad" class="bg-blue-500 text-white px-4 py-2 rounded">Buka Notepad</button>
</div>
@endsection

@section('scripts')
<script>
const ws = new WebSocket('ws://localhost:8080');

ws.onopen = () => console.log('Connected to WebSocket server');

ws.onmessage = (event) => console.log('Message from server:', event.data);

document.getElementById('openNotepad').addEventListener('click', () => {
    ws.send('trigger_notepad'); // server akan broadcast open_app
});
</script>
@endsection
