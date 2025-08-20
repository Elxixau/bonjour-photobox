<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Galeri Foto - Order {{ $order->order_code }}</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-white font-serif text-gray-900 font-sans flex flex-col p-4 sm:p-6">

<header class="max-w-5xl mx-auto mb-8 text-center px-4">
    <h1 class="text-4xl font-extrabold tracking-tight mb-1">Galeri Foto</h1>
    <p class="text-gray-600 text-base sm:text-lg">
{{ $order->order_code }}
    </p>
<div class="mb-4 p-4">
    <p class="text-sm text-gray-600">
        Penyimpanan berlaku dari 
        <span class="font-semibold">{{ $startDate->format('d F Y H:i') }}</span>
        sampai 
        <span class="font-semibold">{{ $endDate->format('d F Y H:i') }}</span>
    </p>
</div>

    <main class="flex-grow max-w-6xl mx-auto w-full px-2 sm:px-6">
        @if($photos->isEmpty())
            <p class="text-center text-gray-500 text-lg py-20">Belum ada foto untuk order ini.</p>
        @else
           <div class="grid gap-5 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
    @foreach ($photos as $photo)
        <div class="relative rounded-xl overflow-hidden shadow-lg bg-white cursor-pointer transform transition duration-300 hover:scale-105 active:scale-95 aspect-[6/4]">
            <img 
                src="{{ asset('storage/' . $photo->img_path) }}" 
                alt="Foto {{ $loop->iteration }}" 
                loading="lazy" 
                class="w-full h-full object-cover"
            />
        </div>
    @endforeach
</div>


        @endif
    </main>

    <footer class="max-w-5xl mx-auto mt-16 text-center text-gray-400 text-sm select-none px-4 pb-6">
        &copy; {{ date('Y') }} Your Company. All rights reserved.
    </footer>

</body>
</html>
