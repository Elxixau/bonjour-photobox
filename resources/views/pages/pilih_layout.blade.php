@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-black text-black font-serif mb-8 text-center">Galeri Foto Photobox</h1>

    {{-- Bungkus semua card dengan grid 2 kolom --}}
    <div class="max-w-6xl mx-auto grid grid-cols-2 gap-8">
        
<a href="{{ route('frame.choose', ['orderCode' => $order->order_code, 'layout' => 4]) }}" class="block">

            <div class="border-2 border-black rounded-lg shadow-black shadow-[6px_6px_0_0] p-4 bg-white hover:bg-gray-200 cursor-pointer max-w-md mx-auto">
                <h2 class="text-lg font-semibold mb-4 text-center">Contoh Layout 4 Foto</h2>
                <div class="grid grid-cols-2 gap-4">
                    @php
                        $photos4 = [
                            'https://via.placeholder.com/300x400?text=Foto+1',
                            'https://via.placeholder.com/300x400?text=Foto+2',
                            'https://via.placeholder.com/300x400?text=Foto+3',
                            'https://via.placeholder.com/300x400?text=Foto+4',
                        ];
                    @endphp

                    @foreach($photos4 as $photo)
                        <div class="border-2 border-black rounded-lg overflow-hidden">
                            <img src="{{ $photo }}" alt="Foto Photobox" class="w-full h-auto object-cover" />
                        </div>
                    @endforeach
                </div>
            </div>
        </a>

       <a href="{{ route('frame.choose', ['orderCode' => $order->order_code, 'layout' => 6]) }}" class="block">

            <div class="border-2 border-black rounded-lg shadow-black shadow-[6px_6px_0_0] p-4 bg-white hover:bg-gray-200 cursor-pointer max-w-md mx-auto">
                <h2 class="text-lg font-semibold mb-4 text-center">Contoh Layout 6 Foto (Portrait 2x3)</h2>
                <div class="grid grid-cols-2 gap-4">
                    @php
                        $photos6 = [
                            'https://via.placeholder.com/300x400?text=Foto+1',
                            'https://via.placeholder.com/300x400?text=Foto+2',
                            'https://via.placeholder.com/300x400?text=Foto+3',
                            'https://via.placeholder.com/300x400?text=Foto+4',
                            'https://via.placeholder.com/300x400?text=Foto+5',
                            'https://via.placeholder.com/300x400?text=Foto+6',
                        ];
                    @endphp

                    @foreach($photos6 as $photo)
                        <div class="border-2 border-black rounded-lg overflow-hidden">
                            <img src="{{ $photo }}" alt="Foto Photobox" class="w-full h-auto object-cover" />
                        </div>
                    @endforeach
                </div>
            </div>
        </a>

        <a href="{{ route('frame.choose', ['orderCode' => $order->order_code, 'layout' => 7]) }}" class="block">

            <div class="border-2 border-black rounded-lg shadow-black shadow-[6px_6px_0_0] p-4 bg-white hover:bg-gray-200 cursor-pointer max-w-md mx-auto">
                <h2 class="text-lg font-semibold mb-4 text-center">Contoh Layout 7 Foto (Portrait 2x3)</h2>
                <div class="grid grid-cols-2 gap-4">
                    @php
                        $photos7 = [
                            'https://via.placeholder.com/300x400?text=Foto+1',
                            'https://via.placeholder.com/300x400?text=Foto+2',
                            'https://via.placeholder.com/300x400?text=Foto+3',
                            'https://via.placeholder.com/300x400?text=Foto+4',
                            'https://via.placeholder.com/300x400?text=Foto+5',
                            'https://via.placeholder.com/300x400?text=Foto+6',
                            'https://via.placeholder.com/300x400?text=Foto+7',
                        ];
                    @endphp

                    @foreach($photos7 as $photo)
                        <div class="border-2 border-black rounded-lg overflow-hidden">
                            <img src="{{ $photo }}" alt="Foto Photobox" class="w-full h-auto object-cover" />
                        </div>
                    @endforeach
                    @if(count($photos7) < 9)
                        @for($i = 0; $i < 9 - count($photos7); $i++)
                            <div></div>
                        @endfor
                    @endif
                </div>
            </div>
        </a>

        <a href="{{ route('frame.choose', ['orderCode' => $order->order_code, 'layout' => 8]) }}" class="block">

            <div class="border-2 border-black rounded-lg shadow-black shadow-[6px_6px_0_0] p-4 bg-white hover:bg-gray-200 cursor-pointer max-w-md mx-auto">
                <h2 class="text-lg font-semibold mb-4 text-center">Contoh Layout 8 Foto (Portrait 2x4)</h2>
                <div class="grid grid-cols-2 gap-4">
                    @php
                        $photos8 = [
                            'https://via.placeholder.com/300x400?text=Foto+1',
                            'https://via.placeholder.com/300x400?text=Foto+2',
                            'https://via.placeholder.com/300x400?text=Foto+3',
                            'https://via.placeholder.com/300x400?text=Foto+4',
                            'https://via.placeholder.com/300x400?text=Foto+5',
                            'https://via.placeholder.com/300x400?text=Foto+6',
                            'https://via.placeholder.com/300x400?text=Foto+7',
                            'https://via.placeholder.com/300x400?text=Foto+8',
                        ];
                    @endphp

                    @foreach($photos8 as $photo)
                        <div class="border-2 border-black rounded-lg overflow-hidden">
                            <img src="{{ $photo }}" alt="Foto Photobox" class="w-full h-auto object-cover" />
                        </div>
                    @endforeach
                </div>
            </div>
        </a>

    </div>

@endsection
