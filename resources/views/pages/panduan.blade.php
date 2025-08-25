@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-black text-black font-serif mb-8">Panduan Penggunaan </h1>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 justify-items-center">
    @php
        $steps = [
            'Pembayaran',
            'Pilih Frame',
            'Sesi Foto',
            'Edit Foto',
            'Cetak & Scan'
        ];

        $images = [
            asset('chara/money.png'),
            asset('chara/strip.png'),
            asset('chara/capture.png'),
            asset('chara/edit.png'),
            asset('chara/qr.png')
        ];
    @endphp

    @foreach($steps as $index => $step)
        <div class="bg-white border-2 border-black rounded-lg shadow-black shadow-[6px_6px_0_0] overflow-hidden flex flex-col w-[220px]">
            
            {{-- Gambar + Angka --}}
            <div class="relative w-full">
                {{-- Angka lingkaran --}}
                <div class="absolute top-2 left-2 w-8 h-8 rounded-full bg-white border-2 border-black flex items-center justify-center font-bold text-md z-10">
                    {{ $index + 1 }}
                </div>

                {{-- Gambar --}}
                <img src="{{ $images[$index] }}" alt="{{ $step }}" class="w-full h-[270px] object-cover border-b-2 border-black">
            </div>

            {{-- Judul --}}
            <div class="p-2 flex-1 flex items-center justify-center">
                <p class="text-lg font-semibold text-center">{{ $step }}</p>
            </div>
        </div>
    @endforeach
</div>

    <a href="{{ route('payment.index') }}" 
        class="flex items-center rounded-xl border border-2 border-black bg-gray-300 text-black p-2 mt-8">
        Next
        <div class="border border-2 border-black bg-gray-300 rounded-md ml-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
            </svg>
        </div>
    </a>


@endsection
