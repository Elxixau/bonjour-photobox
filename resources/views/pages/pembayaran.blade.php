@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-black text-black font-serif mb-8">Pembayaran</h1>

    {{-- Card Kategori --}}
    <div class="bg-white shadow-lg rounded-xl p-6 mb- text-black">
        <h2 class="text-xl font-bold mb-2">{{ $kategori->nama }}</h2>
        <p class="text-gray-600 mb-4">Harga Paket: <span class="font-semibold">Rp {{ number_format($kategori->harga, 0, ',', '.') }}</span></p>
    </div>

    {{-- Card Addons --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($kategori->addons as $addon)
            <div class="bg-white shadow-lg rounded-xl p-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold">{{ $addon->nama }}</h3>
                    <p class="text-gray-500">Rp {{ number_format($addon->harga, 0, ',', '.') }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="decrement({{ $addon->id }})" class="px-3 py-1 bg-gray-200 rounded-full hover:bg-gray-300">-</button>
                    <span id="qty-{{ $addon->id }}" class="min-w-[20px] text-center">0</span>
                    <button onclick="increment({{ $addon->id }})" class="px-3 py-1 bg-gray-200 rounded-full hover:bg-gray-300">+</button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Script untuk tombol + dan - --}}
    <script>
        function increment(id) {
            let qtyEl = document.getElementById('qty-' + id);
            let qty = parseInt(qtyEl.innerText);
            qtyEl.innerText = qty + 1;
        }

        function decrement(id) {
            let qtyEl = document.getElementById('qty-' + id);
            let qty = parseInt(qtyEl.innerText);
            if (qty > 0) {
                qtyEl.innerText = qty - 1;
            }
        }
    </script>
@endsection
