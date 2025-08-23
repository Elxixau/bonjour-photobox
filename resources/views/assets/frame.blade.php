@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-serif font-bold text-center mb-6">Pilih Frame</h1>

<div class="absolute top-4   left-2 z-50 ">
    <a href="{{ url()->previous() }}" 
       class="bg-white text-black font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300 text-sm">
        Kembali
    </a>
</div>


@livewire('frame-selector', ['orderCode' => $order->order_code, 'layout' => $layout, 'selectedFrame' => old('frame_id')])

@endsection
