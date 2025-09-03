@extends('layouts.app')

@section('content')
<h1 class="text-3xl text-white font-black font-serif text-center mb-8">
    Preview Hasil Foto
</h1>

<div class="flex flex-wrap justify-center gap-4 mb-6">
    @forelse($prints as $print)
        <img id="finalPhoto" src="{{ asset('storage/' . $print->img_path) }}" 
             class="border-2 border-black rounded-lg shadow-lg max-w-xs max-h-xs object-contain">
    @empty
        <p>No print files uploaded yet for this order.</p>
    @endforelse
</div>

<!-- Tombol untuk QR & Cetak -->
<div class="flex justify-center gap-4">
    <button onclick="showQRModal()" 
            class="px-6 py-3  bg-gray-400 text-white font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300">
 Scan QR
    </button>

    <button onclick="printPhoto()" 
            class="px-6 py-3  bg-white text-black font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300">
        Cetak Foto
    </button>

    <a href="{{route('panduan')}}"   class="px-6 py-3  bg-gray-400 text-white font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300">
        Selesai
    </a>
</div>

<!-- Modal QR -->
<div id="qrModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white font-serif p-6 rounded-lg shadow-lg max-w-sm w-full text-center relative">
        <button onclick="closeQRModal()" 
                class="absolute top-2 right-2 text-gray-500 hover:text-black">&times;</button>

        <h2 class="text-xl font-bold mb-4">Cloud Gallery</h2>

        <div class="flex justify-center mb-4">
            @if($qr)
                <img id="qrImage" src="{{ $qr['image'] }}" alt="QR Code" class="w-50 h-50 object-contain border rounded-lg shadow-lg p-2">
            @else
                <p class="text-red-500">QR Code belum tersedia</p>
            @endif
        </div>
        <div class="text-center text-sm text-gray-700"> Scan QR untuk mengakses cloud gallery anda</div>
    </div>
</div>

<script>
    
function showQRModal() {
    document.getElementById('qrModal').classList.remove('hidden');
}

function closeQRModal() {
    document.getElementById('qrModal').classList.add('hidden');
}


   function printPhoto() {
    const jumlahCetak = {{ $order->jumlah_cetak ?? 1 }}; // ambil dari order
    const photoUrl = document.getElementById('finalPhoto').src;

    const printWindow = window.open('', '', 'width=400,height=600');

    printWindow.document.write(`
        <html>
            <head>
                <title>Cetak Foto 4R</title>
                <style>
                    @page { size: 4in 6in; margin: 0; }
                    html, body {
                        margin: 0;
                        padding: 0;
                        width: 100%;
                        height: 100%;
                        background: #fff;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }
                    img {
                        width: 100%;
                        height: 100%;
                        object-fit: contain;
                        display: block;
                    }
                </style>
            </head>
            <body>
                <img id="photo" src="${photoUrl}" alt="Foto">
                <script>
                    let counter = 0;
                    const max = ${jumlahCetak};
                    function doPrint() {
                        if (counter < max) {
                            window.print();
                            counter++;
                            setTimeout(doPrint, 1000); // jeda 1 detik antar print
                        } else {
                            window.close(); // tutup otomatis setelah selesai
                        }
                    }
                    document.getElementById('photo').onload = doPrint;
                <\/script>
            </body>
        </html>
    `);

    printWindow.document.close();
}
</script>
@endsection
