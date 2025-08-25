@extends('layouts.app')

@section('content')


<form action="{{ route('payment.store') }}" method="POST">
    @csrf

    {{-- Card Kategori --}}
    <div class="mb-6 bg-white border-2 font-serif border-black rounded-xl shadow-black shadow-[8px_8px_0_0] p-4 space-y-2">
        <h2 class="text-2xl font-semibold ">Pembayaran Paket Photobox - {{ $kategori->nama }}</h2>
         <p class="text-md  ">Paket Photobox, unlimited retake, dengan waktu {{ $kategori->waktu }} Menit</p>
        <p class="text-xl font-medium ">Rp {{ number_format($kategori->harga, 0, ',', '.') }}</p>
        <input type="hidden" name="kategori_id" value="{{ $kategori->id }}">
    </div>

    <div class="text-2xl font-bold text-center text-white font-serif pt-4 p-4">Add-ons</div>
    {{-- Card Addons --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach ($addons as $addon)
        <div class="bg-white border-2 border-black rounded-xl shadow-black shadow-[8px_8px_0_0] 
                    p-4 flex flex-col items-center justify-between h-36">
            <div class="flex flex-col items-center">
                <h3 class="font-semibold">{{ $addon->nama }}</h3>

                {{-- Harga + waktu/item --}}
                <p class="text-sm text-gray-600 mb-2">
                    Rp {{ number_format($addon->harga, 0, ',', '.') }}
                    @if (stripos($addon->nama, 'cetak') !== false)
                        / 1 item
                    @elseif (stripos($addon->nama, 'gantungan') !== false)
                        / 1 item
                    @else
                        / 2 menit
                    @endif
                </p>
            </div>

            <div class="flex items-center space-x-2">
                <button type="button" 
                        class="px-3 py-1 bg-gray-300 border-2 border-black rounded hover:bg-gray-400" 
                        onclick="changeQty({{ $addon->id }}, -1)">-</button>
                <input 
                    type="number" 
                    name="addons[{{ $addon->id }}][qty]" 
                    id="addon-qty-{{ $addon->id }}" 
                    value="0" 
                    min="0" 
                    class="w-12 text-center border-2 border-black rounded"
                >
                <button type="button" 
                        class="px-3 py-1 bg-gray-300 border-2 border-black rounded hover:bg-gray-400" 
                        onclick="changeQty({{ $addon->id }}, 1)">+</button>
            </div>

            <input type="hidden" name="addons[{{ $addon->id }}][id]" value="{{ $addon->id }}">
        </div>
    @endforeach
</div>

    <div class="mt-6">
        <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
            Bayar Sekarang
        </button>
    </div>
</form>

<script>
    function changeQty(addonId, delta) {
        const input = document.getElementById(`addon-qty-${addonId}`);
        let value = parseInt(input.value) || 0;
        value = Math.max(0, value + delta);
        input.value = value;
    }
</script>
@endsection
