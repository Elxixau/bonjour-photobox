@extends('layouts.app')

@section('content')
  
<div class="w-full mx-auto bg-white border-2 border-black rounded-lg p-8 font-serif text-sm mb-4">
    
    <div class="w-full flex justify-between items-center gap-2">
        <p><span class="font-semibold">Order-Id:</span> {{ $order->order_code }}</p>
        <p><span class="font-semibold">Status:</span> 
             @if($order->status === 'pending')
                <span class="text-yellow-600 font-semibold">Pending</span>
            @elseif($order->status === 'success')
                <span class="text-green-600 font-semibold">Paid</span>
            @else
                <span class="text-gray-600 font-semibold capitalize">{{ $order->status }}</span>
            @endif
        </p>
    </div>

    <div class="flex justify-between py-1">
        <span>Total Waktu</span>
        <span>{{ $order->waktu }} menit</span>
    </div>
    <div class="border-t-2 border-black pt-4 flex justify-between text-base font-bold">
        <span>Total Harga</span>
        <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
    </div>
</div>

<div class="w-full mx-auto bg-white border-2 border-black rounded-lg p-4 font-serif text-sm mb-4">
    <div class="px-4 text-lg font-reguler">
        <span class="font-bold"> Reminder :</span>
          <span> Pakai total waktu untuk melakukan tahap awal sesi foto hingga akhir, <span class="font-bold">sisihkan 1 menit</span>  untuk sampai pada tahap akhir</span>
    </div>
</div>

<div class="w-full mx-auto bg-white border-2 border-black rounded-lg p-4 font-serif text-sm mb-4">
  <div class="flex items-start space-x-3 px-4 text-lg">
    <div class="w-6 h-6 flex items-center justify-center border-2 border-black rounded-full mr-2">
      ✓
    </div>
    <span>Pilih frame yang akan dipakai sesuai layout yang diinginkan.</span>
  </div>
</div>

<div class="w-full mx-auto bg-white border-2 border-black rounded-lg p-4 font-serif text-sm mb-4">
  <div class="flex items-start space-x-3 px-4 text-lg">
    <div class="w-6 h-6 flex items-center justify-center border-2 border-black rounded-full mr-2">
      ✓
    </div>
    <span>Lakukan sesi foto dengan pose yang menarik untuk mengisi setiap layout foto.</span>
  </div>
</div>

<div class="w-full mx-auto bg-white border-2 border-black rounded-lg p-4 font-serif text-sm mb-4">
  <div class="flex items-start space-x-3 px-4 text-lg">
    <div class="w-6 h-6 flex items-center justify-center border-2 border-black rounded-full mr-2">
      ✓
    </div>
    <span>Jika ingin mengulang foto sentuh tombol X di pojok kiri saat sesi foto.</span>
  </div>
</div>

<div class="w-full mx-auto bg-white border-2 border-black rounded-lg p-4 font-serif text-sm mb-4">
  <div class="flex items-start space-x-3 px-4 text-lg">
    <div class="w-6 h-6 flex items-center justify-center border-2 border-black rounded-full mr-2">
      ✓
    </div>
    <span>Pilih Filter yang telah tersedia setelah melakukan sesi foto.</span>
  </div>
</div>

<div class="w-full mx-auto bg-white border-2 border-black rounded-lg p-4 font-serif text-sm mb-4">
  <div class="flex items-start space-x-3 px-4 text-lg">
    <div class="w-6 h-6 flex items-center justify-center border-2 border-black rounded-full mr-2">
      ✓
    </div>
    <span>Selanjutnya hasil foto dengan frame yang terpilih akan muncul pada screen.</span>
  </div>
</div>

<button id="startBtn"   class="flex items-center rounded-md border-2 border-black bg-white text-black font-semibold py-2 p-4 mt-8">
    Mulai Sesi Foto
</button>


<script>
    const WS_URL = 'ws://localhost:8090'; // ganti dengan IP PC Photobooth
    const orderCode = "{{ $order->order_code }}";
    const durationMinutes = {{ $order->waktu ?? 5 }};

    let ws;

    document.getElementById('startBtn').addEventListener('click', () => {
        if(!ws || ws.readyState !== WebSocket.OPEN){
            ws = new WebSocket(WS_URL);

            ws.onopen = () => {
                console.log('Connected to Node.js Agent');
                sendStartSession();
            };

            ws.onmessage = e => {
                const data = JSON.parse(e.data);
                if(data.type === 'timer'){
                    console.log('Remaining:', data.remaining);
                }
                if(data.type === 'sessionEnd'){
                    window.location.href = "/preview/{{$order->order_code}}";
                }
            };

            ws.onerror = e => console.error('WebSocket error:', e);
            ws.onclose = () => console.log('WebSocket closed');
        } else {
            sendStartSession();
        }
    });

    function sendStartSession(){
        ws.send(JSON.stringify({
            type: 'startSession',
            order_code: orderCode,
            duration: durationMinutes
        }));
    }
</script>

    
@endsection


