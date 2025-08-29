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
                <div class="relative rounded-xl overflow-hidden shadow-lg bg-white transform transition duration-300 hover:scale-105 active:scale-95 group">
                    <!-- Preview foto HD -->
                    <div class="w-full">
                        <img 
                            src="{{ asset('storage/' . $photo->img_path) }}" 
                            alt="Foto {{ $loop->iteration }}" 
                            loading="lazy" 
                            class="w-full h-auto object-contain rounded-lg"
                        />
                    </div>

                    <div class="flex justify-between mt-2 px-3 pb-3">
                        <a href="{{ route('photo.download', ['photo' => $photo->img_path]) }}"
   class="px-3 py-1 bg-white text-black rounded-md text-sm font-semibold hover:bg-gray-200 transition " target="_blank">
   Download 
</a>


                        <!-- Tombol Share -->
                        <button onclick="sharePhoto('{{ asset('storage/' . $photo->img_path) }}')" 
                                class="px-3 py-1 bg-white text-black rounded-md text-sm font-semibold hover:bg-gray-200 transition">
                            Share
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
