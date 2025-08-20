@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-black font-serif text-center mb-8">
    Preview Hasil Foto
</h1>

<div class="flex justify-center mb-6">
    <img id="finalPhoto" src="{{ asset('storage/' . $file) }}" 
         class="border-2 border-black rounded-lg shadow-lg max-w-lg max-h-lg object-contain">
</div>

<!-- Tombol untuk QR & Cetak -->
<div class="flex justify-center gap-4">
    <button onclick="showQRModal()" 
            class="px-6 py-3  bg-gray-400 text-white font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300">
 Cetak QR
    </button>

    <button onclick="printPhoto()" 
            class="px-6 py-3  bg-white text-black font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300">
        Cetak Foto
    </button>

    <a href="{{route('welcome')}}"   class="px-6 py-3  bg-gray-400 text-white font-semibold py-2 px-4 rounded-lg border-2 border-black shadow-black shadow-[4px_4px_0_0] hover:shadow-[6px_6px_0_0] transition duration-300">
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
// Cetak foto terakhir untuk kertas 4R (4x6 inch)
function printPhoto() {
    const photoUrl = document.getElementById('finalPhoto').src;
    const printWindow = window.open('', '', 'width=600,height=900'); // ukuran kasar untuk preview

    printWindow.document.write(`
        <html>
            <head>
                <style>
                    @page { 
                        size: 4in 6in; 
                        margin: 0; 
                    }
                    html, body {
                        margin: 0;
                        padding: 0;
                        height: 100%;
                        width: 100%;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        background: #fff;
                    }
                    img {
                        max-width: 100%;
                        max-height: 100%;
                        object-fit: contain;
                        display: block;
                    }
                </style>
            </head>
            <body>
                <img id="printImg" src="${photoUrl}" alt="Foto">
            </body>
        </html>
    `);

    printWindow.document.close();

    // Tunggu gambar load sebelum cetak
    printWindow.onload = () => {
        const img = printWindow.document.getElementById('printImg');
        if (img.complete) {
            printWindow.focus();
            printWindow.print();
        } else {
            img.onload = () => {
                printWindow.focus();
                printWindow.print();
            }
        }
    }
}

</script>
@endsection
