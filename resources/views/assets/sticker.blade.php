@extends('layouts.app')

@section('content')
@php
    $orderCode = $order->order_code;
    $photos = \App\Models\CloudGallery::where('order_id', $order->id)->take(8)->get();
@endphp

<h1 class="text-3xl font-bold text-center mb-6">Pilih Sticker</h1>

<div class="max-w-8xl mx-auto grid grid-cols-2 gap-8">
    {{-- List sticker kiri --}}
    <div class="overflow-y-auto max-h-[80vh] p-2 bg-white rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] grid grid-cols-2 gap-1">
        @foreach($stickers as $sticker)
            <div 
                class="cursor-pointer rounded-lg transition duration-300 shadow-[2px_2px_0_0] shadow-black flex items-center justify-center bg-gray-50"
                style="width: 100px; height: 100px;"
                draggable="true"
                ondragstart="dragSticker(event, '{{ $sticker->img_path }}')"
            >
                <img src="{{ asset('storage/' . $sticker->img_path) }}" class="max-w-full max-h-full object-contain p-0">
            </div>
        @endforeach
    </div>

    {{-- Preview kanan --}}
    <div class="p-4 flex flex-col items-center justify-between">
        {{-- Toggle frame/sticker --}}
        <div class="flex gap-4 mb-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" id="toggleFrame" checked>
                Tampilkan Frame
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" id="toggleSticker" checked>
                Tampilkan Sticker
            </label>
        </div>

        {{-- Container preview canvas --}}
        <div class="border-2 border-gray-300 rounded-lg shadow-lg overflow-hidden" style="width: 300px; height: 500px;">
            <canvas id="previewCanvas" style="width:300px; height:450px; display:block;"></canvas>
        </div>

        {{-- Form pilih sticker --}}
        <form method="POST" id="stickerForm" action="{{ route('sticker.export', ['orderCode' => $orderCode]) }}" class="w-full mt-4">
            @csrf
            <input type="hidden" name="sticker_id" id="sticker_id">
            <input type="hidden" name="final_image" id="final_image">
            <div class="flex justify-center">
                <button type="submit"
                    class="w-xl bg-white text-black font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 text-sm"
                >
                    Pilih Sticker & Lanjut
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
    const toggleSticker = document.getElementById('toggleSticker');

    let stickersOnCanvas = [];
    let tempDraggingSticker = null;
    let photoPositions = [];
    let draggingPhoto = null;
    let draggingSticker = null;
    let offsetX = 0, offsetY = 0;
    const layout = {{ $layout ?? 4 }};

    async function loadImage(src){
        return new Promise(resolve=>{
            const img = new Image();
            img.onload = ()=>resolve(img);
            img.src = src;
        });
    }

    // HD canvas
    canvas.width = 1200;
    canvas.height = 1800;

    // Load photos
    const photos = await Promise.all([
        @foreach($photos as $photo)
            loadImage("{{ asset('storage/' . $photo->img_path) }}"),
        @endforeach
    ]);

    // Load frame
    const framePath = "{{ $order->frame ? asset('storage/' . $order->frame->img_path) : '' }}";
    const frameImg = framePath ? await loadImage(framePath) : null;

    // Hitung posisi frame agar proporsional
    let frameScale = 1, frameX = 0, frameY = 0;
    if(frameImg){
        const scaleX = canvas.width / frameImg.width;
        const scaleY = canvas.height / frameImg.height;
        frameScale = Math.min(scaleX, scaleY);
        frameX = (canvas.width - frameImg.width * frameScale) / 2;
        frameY = (canvas.height - frameImg.height * frameScale) / 2;
    }

    // Setup grid layout
    let cols = 2;
    if(layout === 4) cols = 2;
    else if(layout === 6) cols = 3;
    else if(layout === 7 || layout === 8) cols = 4;
    const rows = Math.ceil(photos.length / cols);
    const gap = 40;
    const cellWidth = (canvas.width - gap*(cols+1)) / cols;
    const cellHeight = (canvas.height - gap*(rows+1)) / rows;

    // Susunan foto terbaru di belakang
    photos.forEach((img, index) => {
        const row = Math.floor(index / cols);
        const col = index % cols;
        const x = gap + col * (cellWidth + gap);
        const y = gap + row * (cellHeight + gap);
        const scale = Math.min(cellWidth / img.width, cellHeight / img.height);

        photoPositions.unshift({
            img,
            x: x + (cellWidth - img.width*scale)/2 + frameX,
            y: y + (cellHeight - img.height*scale)/2 + frameY,
            width: img.width*scale,
            height: img.height*scale
        });
    });

    // Drag & drop sticker
    window.dragSticker = function(event, path){
        const img = new Image();
        img.onload = () => {
            const maxSize = 300;
            const scale = Math.min(maxSize/img.width, maxSize/img.height);
            tempDraggingSticker = { img, x:0, y:0, width: img.width*scale, height: img.height*scale };
        };
        img.src = `/storage/${path}`;
    }

    canvas.addEventListener('dragover', e=> e.preventDefault());
    canvas.addEventListener('drop', e=>{
        e.preventDefault();
        if(tempDraggingSticker){
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            tempDraggingSticker.x = (e.clientX - rect.left)*scaleX - tempDraggingSticker.width/2;
            tempDraggingSticker.y = (e.clientY - rect.top)*scaleY - tempDraggingSticker.height/2;
            stickersOnCanvas.push({...tempDraggingSticker});
            tempDraggingSticker = null;
            renderCanvas();
        }
    });

    // Drag foto & sticker
    canvas.addEventListener('mousedown', e=>{
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        const mouseX = (e.clientX - rect.left)*scaleX;
        const mouseY = (e.clientY - rect.top)*scaleY;

        for(let i=stickersOnCanvas.length-1;i>=0;i--){
            const s = stickersOnCanvas[i];
            if(mouseX>=s.x && mouseX<=s.x+s.width && mouseY>=s.y && mouseY<=s.y+s.height){
                draggingSticker = s; offsetX = mouseX - s.x; offsetY = mouseY - s.y; return;
            }
        }
        for(let i=photoPositions.length-1;i>=0;i--){
            const p = photoPositions[i];
            if(mouseX>=p.x && mouseX<=p.x+p.width && mouseY>=p.y && mouseY<=p.y+p.height){
                draggingPhoto = p; offsetX = mouseX - p.x; offsetY = mouseY - p.y; break;
            }
        }
    });

    canvas.addEventListener('mousemove', e=>{
        if(draggingPhoto || draggingSticker){
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            const mouseX = (e.clientX - rect.left)*scaleX;
            const mouseY = (e.clientY - rect.top)*scaleY;

            if(draggingPhoto){ draggingPhoto.x = mouseX - offsetX; draggingPhoto.y = mouseY - offsetY; }
            else if(draggingSticker){ draggingSticker.x = mouseX - offsetX; draggingSticker.y = mouseY - offsetY; }

            renderCanvas();
        }
    });

    canvas.addEventListener('mouseup', e=>{ draggingPhoto=null; draggingSticker=null; });
    canvas.addEventListener('mouseleave', e=>{ draggingPhoto=null; draggingSticker=null; });

    // Render
    function renderCanvas(){
        ctx.clearRect(0,0,canvas.width,canvas.height);
        ctx.fillStyle="#ffffff";
        ctx.fillRect(0,0,canvas.width,canvas.height);

        photoPositions.forEach(p=> ctx.drawImage(p.img,p.x,p.y,p.width,p.height));

        if(frameImg && toggleFrame.checked)
            ctx.drawImage(frameImg, frameX, frameY, frameImg.width*frameScale, frameImg.height*frameScale);

        if(toggleSticker.checked){
            stickersOnCanvas.forEach(s=> ctx.drawImage(s.img,s.x,s.y,s.width,s.height));
            if(tempDraggingSticker) ctx.drawImage(tempDraggingSticker.img,tempDraggingSticker.x,tempDraggingSticker.y,tempDraggingSticker.width,tempDraggingSticker.height);
        }

        finalImageInput.value = canvas.toDataURL('image/jpeg',0.90);
    }

    toggleFrame.addEventListener('change', renderCanvas);
    toggleSticker.addEventListener('change', renderCanvas);
    renderCanvas();
});
</script>

@endsection
