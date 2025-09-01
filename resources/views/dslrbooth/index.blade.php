@extends('layouts.app')

@section('content')
 <h1>Photobooth WebApp</h1>
  <button onclick="startPhoto()">Mulai Foto</button>
  <button onclick="showBrowser()">Tampilkan Browser</button>

  <script>
    const ws = new WebSocket("ws://localhost:8080"); // arahkan ke PC photobooth

    function startPhoto() {
      ws.send("startPhoto");
    }

    function showBrowser() {
      ws.send("showBrowser");
    }
  </script>
@endsection
