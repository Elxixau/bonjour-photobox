<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{config('app.name')}}</title>
      @vite('resources/css/app.css')
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



     
</head>
<body >

<div class="min-h-screen flex flex-col justify-center items-center bg-gray-100" style="background-image: url('{{ asset('image/bgBonjour.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;" >
    <div class=" x-4">
        
    {{-- Navbar Elevated --}}
    <nav
        class="relative rounded-lg border-2 border-black bg-white px-6 py-10 mb-10 w-full max-w-6xl 
        shadow-black shadow-[8px_8px_0_0] flex items-center flex-wrap gap-6 min-h-[100px]"
    >
        {{-- Logo kiri --}}
        <div class="relative lg:text-7xl text-5xl px-2 md:px-2 py-3 font-black md:mr-2 mr-8 font-serif">
            Bonjour
        </div>

        {{-- Menu label statis dengan kondisi aktif --}}
        <a href="{{ route('panduan') }}" 
        class="px-2  py-2 md:text-md text-2xl font-reguler rounded whitespace-nowrap
                {{ request()->routeIs('panduan') ? 'bg-black text-white' : '' }}">
            Panduan
        </a>

        <a href="{{ route('payment.index') }}" 
        class="px-2  py-2 md:text-md text-2xl font-reguler rounded whitespace-nowrap
                {{ request()->routeIs('payment.*') ? 'bg-black text-white' : '' }}">
            Pembayaran
        </a>

        <a href="{{ route('frame.choose', ['orderCode' => 1, 'layout' => 'default']) }}" 
        class="px-2  py-2 md:text-md text-2xl font-reguler rounded whitespace-nowrap
                {{ request()->routeIs('frame.*') ? 'bg-black text-white' : '' }}">
            Pilih Frame
        </a>

        <a href="{{ route('sesi-foto.show', ['orderCode' => 1]) }}" 
        class="px-2  py-2 md:text-md text-2xl font-reguler rounded whitespace-nowrap
                {{ request()->routeIs('sesi-foto.*') ? 'bg-black text-white' : '' }}">
            Sesi Foto
        </a>

        <a href="{{ route('filter.index', ['orderCode' => 1]) }}" 
        class="px-2  py-2 md:text-md text-2xl font-reguler rounded whitespace-nowrap
                {{ request()->routeIs('filter.*') ? 'bg-black text-white' : '' }}">
            Edit Foto
        </a>

    </nav>


    {{-- Section Elevated --}}
    <section
        class="relative  border-2 border-black bg-white px-12 py-14 max-w-6xl w-full 
        shadow-black shadow-[10px_10px_0_0] hover:shadow-[12px_12px_0_0] 
        transition-all flex flex-col items-center"     
    >
        @yield('content')
    </section>

    </div>
</div>

    {{-- JS --}}
    @vite('resources/js/app.js')
</body>
</html>