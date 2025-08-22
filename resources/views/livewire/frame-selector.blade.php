<div class="max-w-7xl mx-auto grid grid-cols-2 gap-8">
    {{-- List frame kiri --}}
   <div class="overflow-y-auto max-h-[80vh] p-2 bg-white rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] flex flex-wrap gap-2">
    @foreach($frames as $frame)
        <div 
            wire:click="selectFrame({{ $frame->id }})"
            class="cursor-pointer rounded-lg transition duration-300
                {{ $selectedFrame == $frame->id ? 'border-blue-500 shadow-[2px_2px_0_0] shadow-black' : ' shadow-[2px_2px_0_0] shadow-black' }}"
            style="width: 135px; height: 168px;"
        >
            <img src="{{ asset('storage/' . $frame->img_path) }}" alt="{{ $frame->name }}" class="w-full h-full object-cover rounded-md">
        </div>
    @endforeach
</div>


    {{-- Preview kanan (sederhana, tanpa elevated) --}}
    <div class="p-4 flex flex-col items-center justify-between">
        <div class="flex items-center justify-center border-2 border-gray-300 rounded-lg overflow-hidden mb-4 bg-white">
            @if($selectedFrame)
                @php
                    $frame = $frames->firstWhere('id', $selectedFrame);
                @endphp
                @if($frame)
                    <div style="width: 260px; height: 346px;"> {{-- preview kanan --}}
                        <img src="{{ asset('storage/' . $frame->img_path) }}" alt="{{ $frame->name }}" class="w-full h-full object-contain">
                    </div>
                @endif
            @else
                <div style="width: 260px; height: 346px;" class="flex items-center justify-center">
                    <p class="text-gray-400">Pilih frame untuk preview</p>
                </div>
            @endif
        </div>

        {{-- Button Elevated --}}
        <form method="POST" action="{{ route('frame.select', ['orderCode' => $orderCode, 'layout' => $layout]) }}" class="w-full">
            @csrf
            @method('PATCH')
            <input type="hidden" name="frame_id" value="{{ $selectedFrame }}">
            <button type="submit"
                class="w-full bg-white text-black font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 text-sm
                {{ $selectedFrame ? '' : 'opacity-50 cursor-not-allowed' }}"
                {{ $selectedFrame ? '' : 'disabled' }}>
                Pilih Frame & Lanjut
            </button>
        </form>
    </div>
</div>
