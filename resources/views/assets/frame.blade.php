@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-serif font-bold text-center mb-6">Pilih Frame</h1>

@livewire('frame-selector', ['orderCode' => $order->order_code, 'layout' => $layout, 'selectedFrame' => old('frame_id')])

@endsection
