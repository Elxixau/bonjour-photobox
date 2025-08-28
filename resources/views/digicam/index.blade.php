@extends('layouts.app')

@section('content')
<h1 class="text-white text-center text-3xl mb-4">Preview Foto</h1>

<div class="flex justify-center mb-4">
    <button id="captureBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg">Ambil Foto</button>
</div>

<div id="previewContainer" class="grid grid-cols-2 gap-4 p-4 max-w-4xl mx-auto"></div>

<style>
.preview-img { position: relative; }
.download-btn {
    position: absolute;
    top: 2px;
    right: 2px;
    background: black;
    color: white;
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
    border-radius: 4px;
    cursor: pointer;
}
</style>

<script>
let fotoCount = 1;
const previewContainer = document.getElementById('previewContainer');
const captureBtn = document.getElementById('captureBtn');

async function capturePhoto() {
    const filename = `DSC_${String(fotoCount).padStart(4,'0')}.jpg`;

    try {
        // Trigger capture via Laravel proxy
        await fetch('/proxy/capture');

        // Tunggu sebentar agar kamera menyimpan file
        setTimeout(async () => {
            const res = await fetch(`/proxy/preview/${filename}`);
            if(!res.ok) return alert('Preview gagal diambil');

            const blob = await res.blob();

            const wrapper = document.createElement('div');
            wrapper.className = 'preview-img';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(blob);
            img.className = 'w-full h-auto rounded-lg border border-white';
            wrapper.appendChild(img);

            // Tombol download
            const btn = document.createElement('button');
            btn.innerText = 'Download';
            btn.className = 'download-btn';
            btn.addEventListener('click', () => {
                const a = document.createElement('a');
                a.href = img.src;
                a.download = filename;
                a.click();
            });
            wrapper.appendChild(btn);

            previewContainer.appendChild(wrapper);

            fotoCount++;
        }, 500); // sesuaikan delay sesuai respon kamera
    } catch(err){
        console.error(err);
        alert('Gagal capture foto');
    }
}

captureBtn.addEventListener('click', capturePhoto);
</script>
@endsection
