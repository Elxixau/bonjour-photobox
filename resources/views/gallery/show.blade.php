<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Galeri Foto - Order {{ $order->order_code }}</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-white font-sans text-gray-900 flex flex-col p-4 sm:p-6">

<header class="max-w-5xl mx-auto mb-8 text-center px-4">
    <h1 class="text-4xl font-extrabold tracking-tight mb-1">Galeri Foto</h1>
    <p class="text-gray-600 text-base sm:text-lg">Order: {{ $order->order_code }}</p>
    <div class="mb-4 p-4">
        <p class="text-sm text-gray-600">
            Penyimpanan berlaku dari 
            <span class="font-semibold">{{ $startDate->format('d F Y H:i') }}</span>
            sampai 
            <span class="font-semibold">{{ $endDate->format('d F Y H:i') }}</span>
        </p>
    </div>
</header>
<main class="flex-grow max-w-6xl mx-auto w-full px-2 sm:px-6">
    @if($photos->isEmpty())
        <p class="text-center text-gray-500 text-lg py-20">Belum ada foto untuk order ini.</p>
    @else
        <div class="grid gap-5 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
            @foreach ($photos as $photo)
                @if($photo->img_path)
                <div class="relative group rounded-lg overflow-hidden">
                    
                    <!-- Foto -->
                    <img 
                        src="{{ asset('storage/' . $photo->img_path) }}" 
                        alt="Foto {{ $loop->iteration }}" 
                        loading="lazy" 
                        class="w-full h-auto object-contain rounded-lg"
                    />

                    <!-- Tombol Aksi (Download & Share) -->
                    <div class="absolute top-2 right-2 flex gap-2 opacity-0 group-hover:opacity-100 transition">
                        
                        <!-- Download -->
                        <a href="{{ asset('storage/' . $photo->img_path) }}" 
                           download="{{ basename($photo->img_path) }}"
                           class="p-2 bg-white rounded-full shadow hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" 
                                 fill="none" viewBox="0 0 24 24" 
                                 stroke-width="1.5" stroke="currentColor" 
                                 class="w-5 h-5 text-gray-700">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-6-6l-3 3m0 0l-3-3m3 3V3"/>
                            </svg>
                        </a>

                        <!-- Share -->
                        <button onclick="sharePhoto('{{ asset('storage/' . $photo->img_path) }}')" 
                                class="p-2 bg-white rounded-full shadow hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" 
                                 fill="none" viewBox="0 0 24 24" 
                                 stroke-width="1.5" stroke="currentColor" 
                                 class="w-5 h-5 text-gray-700">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                      d="M4.5 12a7.5 7.5 0 0115 0m-7.5 9V3m0 18l-3-3m3 3l3-3"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    @endif
</main>


<footer class="max-w-5xl mx-auto mt-16 text-center text-gray-400 text-sm select-none px-4 pb-6">
    &copy; {{ date('Y') }} Your Company. All rights reserved.
</footer>

<script>
function sharePhoto(url) {
    if (navigator.share) {
        navigator.share({
            title: 'Foto Galeri',
            text: 'Cek foto ini dari order {{ $order->order_code }}',
            url: url
        })
        .then(() => console.log('Berhasil dibagikan'))
        .catch((error) => console.error('Gagal share', error));
    } else {
        alert('Browser tidak mendukung fitur share. Silakan download foto.');
    }
}
</script>

</body>
</html>
