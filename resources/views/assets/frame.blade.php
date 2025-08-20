@extends('layouts.app')

@section('content')
@livewire('frame-selector', ['orderCode' => $order->order_code, 'layout' => $layout, 'selectedFrame' => old('frame_id')])

@endsection
