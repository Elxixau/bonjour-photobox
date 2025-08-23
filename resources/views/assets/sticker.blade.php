@extends('layouts.app')

@section('content')
@php
    $orderCode = $order->order_code;
    $slots = $frame->layouts->map(fn($l) => [
        'id' => (int)$l->id,
        'slot_number' => (int)$l->slot_number,
        'x' => (int)$l->x,
        'y' => (int)$l->y,
        'width' => (int)$l->width,
        'height' => (int)$l->height,
    ])->values();
    $photos = \App\Models\CloudGallery::where('order_id', $order->id)->take(8)->get();
@endphp

<h1 class="text-3xl font-serif font-bold text-center mb-6">Pilih Filter</h1>

<div class="max-w-8xl mx-auto grid grid-cols-2 gap-8">
    {{-- Sidebar: Filter --}}
    <div class="overflow-y-auto max-h-[80vh] p-2 bg-white rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0]">
       
<div class="grid grid-cols-2 gap-2 mb-4">
    @foreach($filters as $filter)
    <div 
        class="cursor-pointer rounded-lg flex flex-col items-center justify-center bg-gray-50 hover:shadow-lg transition duration-200 text-xs p-1"
        onclick="applyFilter('{{ $filter->css_filter }}')"
    >
        {{-- Preview Mini Foto dengan filter --}}
        <div class="w-20 h-20 mb-1 overflow-hidden rounded border border-gray-300">
            <img src="{{ asset('storage/' . $photos[0]->img_path) }}" 
                 class="w-full h-full object-cover"
                 style="filter: {{ $filter->css_filter }};">
        </div>
        {{-- Nama Filter --}}
        <span>{{ $filter->name }}</span>
    </div>
    @endforeach
</div>

    </div>

    {{-- Preview Canvas --}}
    <div class="p-4 flex flex-col items-center">
        <div class="flex gap-4 mb-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" id="toggleFrame" checked>
                Tampilkan Frame
            </label>
        </div>

        <div class="border-2 border-gray-300 rounded-lg shadow-lg overflow-hidden" style="width: 300px; height: 500px;">
            <canvas id="previewCanvas" style="width:300px; height:450px; display:block;"></canvas>
        </div>

        <form method="POST" id="exportForm" action="{{ route('sticker.export', ['orderCode' => $orderCode]) }}" class="w-full mt-4">
            @csrf
            <input type="hidden" name="final_image" id="final_image">
            <div class="flex justify-center">
                <button type="submit"
                    class="w-xl bg-white text-black font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 text-sm"
                >
                    Simpan & Lanjut
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const canvas = document.getElementById('previewCanvas');
    const ctx = canvas.getContext('2d');
    const finalImageInput = document.getElementById('final_image');
    const toggleFrame = document.getElementById('toggleFrame');

    // Canvas HD
    canvas.width = 1200;
    canvas.height = 1800;

    const slots = @json($slots);
    const photos = await Promise.all([
        @foreach($photos as $photo)
            loadImage("{{ asset('storage/' . $photo->img_path) }}"),
        @endforeach
    ]);

    // Frame
    const framePath = "{{ $order->frame ? asset('storage/' . $order->frame->img_path) : '' }}";
    const frameImg = framePath ? await loadImage(framePath) : null;

    // Filter overlay
    let currentFilter = 'none';

    window.applyFilter = function(filter){
        currentFilter = filter;
        renderCanvas();
    }

    toggleFrame.addEventListener('change', renderCanvas);

    function renderCanvas(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    ctx.fillStyle="#ffffff";
    ctx.fillRect(0,0,canvas.width,canvas.height);

    // --- Layer 1: Foto asli dengan filter diterapkan
    slots.forEach((slot, index) => {
        if(photos[index]){
            const img = photos[index];
            const scale = slot.width / img.width;
            const newWidth = slot.width;
            const newHeight = img.height * scale;
            const x = slot.x;
            const y = slot.y + (slot.height - newHeight)/2;

            ctx.save();
            ctx.beginPath();
            ctx.rect(slot.x, slot.y, slot.width, slot.height);
            ctx.clip();
            
            // Terapkan filter seperti preview
            ctx.filter = currentFilter !== 'none' ? currentFilter : 'none';
            ctx.drawImage(img, x, y, newWidth, newHeight);
            ctx.restore();
        }
    });

    // --- Layer 2: Frame
    if(frameImg && toggleFrame.checked){
        const scaleX = canvas.width / frameImg.width;
        const scaleY = canvas.height / frameImg.height;
        const frameScale = Math.min(scaleX, scaleY);
        const frameX = (canvas.width - frameImg.width*frameScale)/2;
        const frameY = (canvas.height - frameImg.height*frameScale)/2;
        ctx.drawImage(frameImg, frameX, frameY, frameImg.width*frameScale, frameImg.height*frameScale);
    }

    finalImageInput.value = canvas.toDataURL('image/jpeg',0.9);
}


    renderCanvas();

    function loadImage(src){
        return new Promise(resolve => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.src = src;
        });
    }
});
</script>
@endsection
