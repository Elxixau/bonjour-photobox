@extends('layouts.app')

@section('content')
<div class="text-center mt-8">
    <h1 class="text-2xl font-bold mb-4">Capture Foto</h1>
    <button id="captureBtn" class="px-6 py-3 bg-blue-600 text-white rounded">Ambil Foto</button>
</div>

<div class="mt-6 max-w-xl mx-auto">
    <h2 class="text-xl font-semibold mb-2">Preview Foto</h2>
    <div id="previewContainer" class="border p-2 rounded text-center">
        <span>Belum ada foto</span>
    </div>
</div>

<script>
const captureBtn = document.getElementById('captureBtn');
const previewContainer = document.getElementById('previewContainer');

captureBtn.addEventListener('click', async () => {
    captureBtn.disabled = true;
    captureBtn.innerText = 'Mengambil foto...';

    try {
        // Trigger capture via Laravel proxy
        const captureRes = await fetch('/proxy/capture');
        if(!captureRes.ok) throw new Error('Gagal capture');

        // Tunggu sebentar supaya file tersedia
        setTimeout(async () => {
            const previewRes = await fetch('/proxy/preview');
            if(!previewRes.ok) throw new Error('Gagal ambil preview');

            const blob = await previewRes.blob();
            const imgURL = URL.createObjectURL(blob);

            previewContainer.innerHTML = '';
            const imgEl = document.createElement('img');
            imgEl.src = imgURL;
            imgEl.className = 'w-full h-auto rounded';
            previewContainer.appendChild(imgEl);

            // Tombol download
            const btn = document.createElement('a');
            btn.href = imgURL;
            btn.download = 'DSC_capture.jpg';
            btn.innerText = 'Download';
            btn.className = 'inline-block mt-2 px-4 py-2 bg-green-600 text-white rounded';
            previewContainer.appendChild(btn);

            captureBtn.disabled = false;
            captureBtn.innerText = 'Ambil Foto';
        }, 500);

    } catch (err) {
        alert(err.message);
        captureBtn.disabled = false;
        captureBtn.innerText = 'Ambil Foto';
    }
});
</script>
@endsection
