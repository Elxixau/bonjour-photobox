@extends('layouts.app')

@section('content')

    <h1 class="text-3xl font-black font-serif">Selamat datang di:</h1>

    <div class="relative font-serif text-5xl rounded-xl border-2 border-black bg-gray-400 text-black font-extrabold mt-4 mb-8 px-8 py-24">
        Bonjour
    </div>

    <div class="max-w-lg text-center font-semibold mx-auto ">
        <p>Studio foto dan photobox digital, mengabadikan momen berharga, baik untuk sesi pribadi maupun acara spesial.</p>
    </div>

        
    <a href="{{route('panduan')}}" 
        class="flex  items-center rounded-xl border border-2 border-black bg-gray-300 text-black p-2  mt-4 "
    >
        Next
        <div class="border border-2 border-black bg-gray-300 rounded-md ml-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
            </svg>
        </div>
    </a>
@endsection
