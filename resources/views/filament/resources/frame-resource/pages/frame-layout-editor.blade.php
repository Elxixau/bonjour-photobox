<x-filament-panels::page>
@php
    $slots = $frame->layouts->map(fn($l) => [
        'id' => (int)$l->id,
        'slot_number' => (int)$l->slot_number,
        'x' => (int)$l->x,
        'y' => (int)$l->y,
        'width' => (int)$l->width,
        'height' => (int)$l->height,
    ])->values();
@endphp

<div
    x-data="layoutEditor({
        slots: @js($slots),
        imgUrl: '{{ asset('storage/'.$frame->img_path) }}',
        frameId: {{ $frame->id }},
        editorWidth: 300,
        editorHeight: 450,
        masterWidth: 1200,
        masterHeight: 1800
    })"
    x-init="init()"
    class="space-y-4"
>
    {{-- Header --}}
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-xl font-bold">Editor Layout: {{ $frame->name }}</h2>
        <div class="flex items-center gap-2">
            <x-filament::button color="primary" x-on:click="addSlot()">Tambah Slot</x-filament::button>
            <x-filament::button color="gray" x-on:click="toggleNumbers()" x-text="showNumbers ? 'Sembunyikan Nomor' : 'Tampilkan Nomor'"></x-filament::button>
            <x-filament::button color="danger" x-on:click="resetSlots()">Reset Slot</x-filament::button>
        </div>
    </div>

    <div class="flex gap-6">
        {{-- Canvas Editor --}}
        <div class="relative border rounded-lg overflow-hidden bg-white flex-shrink-0">
            <canvas id="editorCanvas" width="300" height="450" class="cursor-crosshair"></canvas>
        </div>

        {{-- Daftar Slot --}}
        <div class="flex-1 overflow-auto max-h-[450px]">
            <h3 class="font-bold mb-2">Daftar Slot</h3>
            <template x-for="s in slots" :key="s.id">
                <div class="flex items-center justify-between bg-red-100 mb-2 px-3 py-2 rounded shadow-sm">
                    <div class="flex-1">
                        <span class="font-bold">Slot </span><span x-text="s.slot_number"></span>:
                        <span x-text="`(${s.width}x${s.height}) @ (${s.x},${s.y})`"></span>
                    </div>
                    <div class="flex gap-2">
                        <x-filament::button size="sm" color="danger" x-on:click="removeSlot(s)">Hapus</x-filament::button>
                        <x-filament::button size="sm" color="success" x-on:click="saveSlot(s)">Simpan</x-filament::button>
                    </div>
                </div>
            </template>
            <x-filament::button color="success" x-on:click="saveAll()">Simpan Semua</x-filament::button>

        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('layoutEditor', ({ slots, imgUrl, frameId, editorWidth, editorHeight, masterWidth, masterHeight }) => ({
        slots,
        imgUrl,
        frameId,
        editorWidth,
        editorHeight,
        masterWidth,
        masterHeight,
        showNumbers: true,
        dragging: null,
        resizing: null,
        dragOffset: { x:0, y:0 },
        resizeHandle: null,
        startX:0, startY:0,
        canvas:null,
        ctx:null,
        frameImg:null,
        HANDLE_SIZE: 12,

        init() {
            // Setup canvas
            this.canvas = document.getElementById('editorCanvas');
            this.ctx = this.canvas.getContext('2d');

            this.frameImg = new Image();
            this.frameImg.src = this.imgUrl;
            this.frameImg.onload = () => this.renderCanvas();

            // Mouse events
            this.canvas.addEventListener('mousedown', e => this.onMouseDown(e));
            window.addEventListener('mousemove', e => this.onMouseMove(e));
            window.addEventListener('mouseup', e => this.onMouseUp(e));

            // Save all with Ctrl+S
            window.addEventListener('keydown', e => {
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    this.saveAll();
                }
            });
        },

        toggleNumbers(){ this.showNumbers = !this.showNumbers },

        getMousePos(e){
            const rect = this.canvas.getBoundingClientRect();
            const scaleX = masterWidth / rect.width;
            const scaleY = masterHeight / rect.height;
            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top) * scaleY
            };
        },

        onMouseDown(e){
            const pos = this.getMousePos(e);

            // Check resize first (corners)
            for(const s of this.slots){
                const corners = [
                    {x:s.x, y:s.y, cursor:'nwse-resize', handle:'tl'},
                    {x:s.x+s.width, y:s.y, cursor:'nesw-resize', handle:'tr'},
                    {x:s.x, y:s.y+s.height, cursor:'nesw-resize', handle:'bl'},
                    {x:s.x+s.width, y:s.y+s.height, cursor:'nwse-resize', handle:'br'},
                ];
                for(const c of corners){
                    if(Math.abs(pos.x - c.x)<this.HANDLE_SIZE && Math.abs(pos.y - c.y)<this.HANDLE_SIZE){
                        this.resizing = s;
                        this.resizeHandle = c.handle;
                        this.startX = pos.x;
                        this.startY = pos.y;
                        return;
                    }
                }
            }

            // Check drag
            for(const s of [...this.slots].reverse()){
                if(pos.x >= s.x && pos.x <= s.x+s.width && pos.y >= s.y && pos.y <= s.y+s.height){
                    this.dragging = s;
                    this.dragOffset.x = pos.x - s.x;
                    this.dragOffset.y = pos.y - s.y;
                    return;
                }
            }
        },

        onMouseMove(e){
            const pos = this.getMousePos(e);
            let hoveringHandle = false;

            if(!this.dragging && !this.resizing){
                // Check handles for cursor
                for(const s of this.slots){
                    const corners = [
                        {x:s.x, y:s.y, cursor:'nwse-resize', handle:'tl'},
                        {x:s.x+s.width, y:s.y, cursor:'nesw-resize', handle:'tr'},
                        {x:s.x, y:s.y+s.height, cursor:'nesw-resize', handle:'bl'},
                        {x:s.x+s.width, y:s.y+s.height, cursor:'nwse-resize', handle:'br'},
                    ];
                    for(const h of corners){
                        const scaleX = this.canvas.width/masterWidth;
                        const scaleY = this.canvas.height/masterHeight;
                        const hx = h.x*scaleX;
                        const hy = h.y*scaleY;
                        if(Math.abs(e.offsetX - hx) < this.HANDLE_SIZE && Math.abs(e.offsetY - hy) < this.HANDLE_SIZE){
                            this.canvas.style.cursor = h.cursor;
                            hoveringHandle = true;
                            break;
                        }
                    }
                    if(hoveringHandle) break;
                }

                if(!hoveringHandle){
                    let hoveringSlot = this.slots.some(s=>{
                        const scaleX = this.canvas.width/masterWidth;
                        const scaleY = this.canvas.height/masterHeight;
                        return e.offsetX >= s.x*scaleX && e.offsetX <= (s.x+s.width)*scaleX
                            && e.offsetY >= s.y*scaleY && e.offsetY <= (s.y+s.height)*scaleY;
                    });
                    this.canvas.style.cursor = hoveringSlot ? 'move' : 'crosshair';
                }
            }

            // Drag & Resize
            if(this.dragging){
                this.dragging.x = pos.x - this.dragOffset.x;
                this.dragging.y = pos.y - this.dragOffset.y;
            }
            else if(this.resizing){
                const s = this.resizing;
                const dx = pos.x - this.startX;
                const dy = pos.y - this.startY;
                if(this.resizeHandle.includes('t')){ s.y += dy; s.height -= dy; }
                if(this.resizeHandle.includes('b')){ s.height += dy; }
                if(this.resizeHandle.includes('l')){ s.x += dx; s.width -= dx; }
                if(this.resizeHandle.includes('r')){ s.width += dx; }
                this.startX = pos.x;
                this.startY = pos.y;
            }

            this.renderCanvas();
        },

        onMouseUp(e){
    if(this.dragging || this.resizing){
        const s = this.dragging ?? this.resizing;
        // Auto save begitu mouse dilepas
        this.$wire.updateLayout(s.id, {
            x: Math.round(s.x),
            y: Math.round(s.y),
            width: Math.round(s.width),
            height: Math.round(s.height),
        });
    }

    this.dragging = null;
    this.resizing = null;
},


        renderCanvas(){
            const ctx = this.ctx;
            ctx.clearRect(0,0,this.canvas.width,this.canvas.height);

            // Draw frame
            ctx.drawImage(this.frameImg,0,0,this.canvas.width,this.canvas.height);

            // Draw slots & handles
            this.slots.forEach(s=>{
                const scaleX = this.canvas.width/masterWidth;
                const scaleY = this.canvas.height/masterHeight;

                // Slot rectangle
                ctx.strokeStyle = 'red';
                ctx.lineWidth = 2;
                ctx.strokeRect(s.x*scaleX, s.y*scaleY, s.width*scaleX, s.height*scaleY);

                // Handles
                const corners = [
                    {x:s.x, y:s.y},
                    {x:s.x+s.width, y:s.y},
                    {x:s.x, y:s.y+s.height},
                    {x:s.x+s.width, y:s.y+s.height},
                ];
                ctx.fillStyle = 'red';
                corners.forEach(c=>{
                    ctx.fillRect(c.x*scaleX - this.HANDLE_SIZE/2, c.y*scaleY - this.HANDLE_SIZE/2, this.HANDLE_SIZE, this.HANDLE_SIZE);
                });

                // Slot number
                if(this.showNumbers){
                    ctx.fillStyle = 'red';
                    ctx.font = '16px Arial';
                    ctx.fillText(s.slot_number, s.x*scaleX + 4, s.y*scaleY - 4);
                }
            });
        },

       async saveSlot(s) {
    const updated = {
        x: Math.round(s.x),
        y: Math.round(s.y),
        width: Math.round(s.width),
        height: Math.round(s.height)
    };

    // Simpan ke server (nilai master)
    await this.$wire.call('updateLayout', s.id, updated);

    // ❌ Jangan replace dengan data hasil render (sudah sama-sama master)
    // ✅ Biarkan tetap konsisten, cukup update properti lokal
    const idx = this.slots.findIndex(slot => slot.id === s.id);
    if (idx !== -1) {
        Object.assign(this.slots[idx], updated);
    }

    this.renderCanvas();
},

async saveAll() {
    await this.$wire.set('slots', this.slots);
    await this.$wire.call('saveAll');
    this.renderCanvas();
},





       


        async addSlot(){
    const newSlot = await this.$wire.createSlot();
    this.slots.push(newSlot);
    this.renderCanvas();
},

        async removeSlot(s){
            await this.$wire.call('deleteSlot', s.id);
            this.slots = this.slots.filter(x => x.id !== s.id);
            this.renderCanvas();
        },

        async resetSlots(){
            if(!confirm('Apakah Anda yakin ingin mereset semua slot?')) return;
            for(const s of [...this.slots]) await this.removeSlot(s);
        }
    }))
});
</script>
</x-filament-panels::page>
