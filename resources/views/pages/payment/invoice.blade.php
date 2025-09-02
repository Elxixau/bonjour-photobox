@extends('layouts.app')

@section('content')
  <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="{{config('midtrans.client_key')}}"></script>
    <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
<div class="max-w-4xl mx-auto bg-white border-2 border-black rounded-lg p-8 font-serif text-sm">
    <h1 class="text-2xl font-bold mb-6 text-center">Struk Pesanan</h1>

    <div class="mb-6">
        <p><span class="font-semibold">Kode Order:</span> {{ $order->order_code }}</p>
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

   <div class="border-t-2 border-black pt-4">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-semibold">Detail Paket - {{ $order->kategori->nama }}</h2>
    </div>
    <div class="flex justify-between border-b border-gray-300 py-1">
        <span>Harga Paket</span>
        <span>Rp {{ number_format($order->harga_paket, 0, ',', '.') }}</span>
    </div>
    <div class="flex justify-between py-1">
        <span>Durasi Waktu</span>
        <span>{{ $order->kategori->waktu }} menit</span>
    </div>
    <div class="flex justify-between py-1">
        <span>Total Waktu</span>
         <span>{{ $order->waktu }} menit</span>
    </div>
</div>


    <div class="border-t-2 border-black pt-4 mt-4">
        <h2 class="text-lg font-semibold mb-3">Add-ons</h2>
        @if($order->orderAddons->count() > 0)
            <ul>
                @foreach ($order->orderAddons as $addon)
                <li class="border-b border-gray-200 py-2 flex justify-between items-center">
                    
                    <div class="flex flex-col items-end text-sm">
                    <div class="font-semibold">{{ $addon->addon->nama }}</div>
                    <span>Qty: {{ $addon->qty }}</span>
                   
                    </div>
                     <span class="font-semibold">Rp {{ number_format($addon->harga * $addon->qty, 0, ',', '.') }}</span>
                </li>
                @endforeach
            </ul>

        @else
            <p class="text-gray-600 italic">Tidak ada add-ons</p>
        @endif
    </div>
    <div class="border-t-2 border-black pt-4 mt-6 flex justify-between text-base font-bold">
        <span>Total Harga</span>
        
        <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
    </div>


</div>

    <a 
      >
    </a>

     <button id="startBtn"   class="flex items-center rounded-xl border-2 border-black bg-gray-300 text-black p-2 mt-8">
        
            Next
            <div class="border-2 border-black bg-gray-300 rounded-md ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                </svg>
            </div>
     </button>
</div>

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
                        
               
                        window.location.href = "/preview/{$orderCode}";
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


