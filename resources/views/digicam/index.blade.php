@extends('layouts.app')

@section('content')
@php
    $durasi = 10; // detik countdown sebelum jepret
    $layout = $layout ?? 4;
    $orderId = $order->id ?? '';
    $orderCode = $order->order_code ?? '';
@endphp

<div class="text-center">
    <h1 id="info" class="text-3xl text-white font-black font-serif mb-8">
        0/{{ $layout }} Foto
    </h1>
</div>

<div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8 p-4">
    <!-- Video Section -->
    <div class="relative flex-1 max-w-md mx-auto">
        <div id="videoWrapper" class="bg-black rounded-lg overflow-hidden w-full max-w-[480px] mx-auto"
             style="aspect-ratio: 4/3">
            <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
            <div id="timer"
                 class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 
                 w-20 h-20 rounded-full border-4 border-white text-white flex items-center 
                 justify-center text-2xl font-bold select-none bg-black bg-opacity-50">
                {{ $durasi }}
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="flex-1 max-w-md mx-auto flex flex-col">
        <h2 class="text-xl font-semibold mb-4 text-white text-center">Preview Foto</h2>
        <div id="previewContainer" class="grid grid-cols-2 gap-4 p-2"></div>
    </div>
</div>

<div class="max-w-6xl mx-auto mt-6 text-center space-x-4">
    <button id="reset" class="px-6 py-3 bg-gray-400 text-white rounded-lg hidden">Capture Ulang</button>
    <button id="nextBtn" class="px-6 py-3 bg-white text-black rounded-lg hidden">Selanjutnya</button>
</div>

<script>
    const orderId = "{{ $orderId }}";
    const orderCode = "{{ $orderCode }}";
    const durasi = {{ $durasi }};
    let fotoCount = 0;
    let layout = {{ $layout }};

    const video = document.getElementById("video");
    const timer = document.getElementById("timer");
    const previewContainer = document.getElementById("previewContainer");

    // Ambil kamera (webcam / OBS virtual cam)
    navigator.mediaDevices.getUserMedia({ video: true, audio: false })
        .then(stream => {
            video.srcObject = stream;
        })
        .catch(err => {
            console.error("Tidak bisa akses kamera:", err);
        });

    function startCountdown() {
        let count = durasi;
        timer.innerText = count;

        const interval = setInterval(() => {
            count--;
            timer.innerText = count;

            if (count <= 0) {
                clearInterval(interval);
                capturePhoto();
            }
        }, 1000);
    }

    async function capturePhoto() {
        // Trigger DSLR capture lewat DigiCamControl API
        await fetch("http://127.0.0.1:5513/?CMD=Capture");

        // Ambil hasil foto terakhir
        const response = await fetch("http://127.0.0.1:5513/preview.jpg?cache=" + new Date().getTime());
        const blob = await response.blob();

        // Convert ke base64 untuk kirim ke Laravel
        const reader = new FileReader();
        reader.onloadend = () => {
            const base64data = reader.result;

            // Kirim ke Laravel
            fetch("{{ route('camera.upload') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    order_id: orderId,
                    image: base64data,
                }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    fotoCount++;
                    document.getElementById("info").innerText = `${fotoCount}/${layout} Foto`;

                    // Tambahkan ke preview
                    const img = document.createElement("img");
                    img.src = data.url;
                    img.className = "rounded-lg shadow-lg";
                    previewContainer.appendChild(img);
                }
            });
        };
        reader.readAsDataURL(blob);
    }

    // Jalankan countdown otomatis
    startCountdown();
</script>
@endsection
