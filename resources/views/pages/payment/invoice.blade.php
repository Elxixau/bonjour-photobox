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
          <span> Pakai total waktu untuk melakukan tahap awal sesi foto hingga akhir, 
            <span class="font-bold">sisihkan 1 menit</span> untuk sampai pada tahap akhir</span>
    </div>
</div>

{{-- Tutorial Section --}}
<div id="tutorial" class="w-full mx-auto bg-white border-2 border-black rounded-lg p-4 font-serif text-sm mb-4">
    <div class="tutorial-step hidden" data-step="1">
        <img src="{{asset('images/tutorial/tutorial1.png')}}" class="mx-auto mb-4 w-64" alt="Step 1">
        <p class="text-center">1. Duduklah dengan nyaman di depan kamera.</p>
    </div>
    <div class="tutorial-step hidden" data-step="2">
        <img src="{{asset('images/tutorial/tutorial2.png')}} " class="mx-auto mb-4 w-64" alt="Step 2">
        <p class="text-center">2. Tekan tombol untuk memulai pengambilan foto.</p>
    </div>
    <div class="tutorial-step hidden" data-step="3">
        <img src="/images/tutorial3.png" class="mx-auto mb-4 w-64" alt="Step 3">
        <p class="text-center">3. Tunggu timer selesai dan cek hasil foto Anda.</p>
    </div>

    <div class="flex justify-between mt-4">
        <button id="prevBtn" class="px-4 py-2 border rounded bg-gray-200">Back</button>
        <button id="nextBtn" class="px-4 py-2 border rounded bg-blue-500 text-white">Next</button>
    </div>
</div>

{{-- Start Button (hidden until tutorial selesai) --}}
<button id="startBtn" class="hidden flex items-center rounded-md border-2 border-black bg-white text-black font-semibold py-2 p-4 mt-8">
    Mulai Sesi Foto
</button>


<script>
    const WS_URL = 'ws://localhost:8090'; // ganti dengan IP PC Photobooth
    const orderCode = "{{ $order->order_code }}";
    const durationMinutes = {{ $order->waktu ?? 5 }};

    let ws;

    // -----------------
    // Tutorial Handling
    // -----------------
    let currentStep = 1;
    const totalSteps = document.querySelectorAll('.tutorial-step').length;

    function showStep(step) {
        document.querySelectorAll('.tutorial-step').forEach(el => el.classList.add('hidden'));
        document.querySelector(`.tutorial-step[data-step="${step}"]`).classList.remove('hidden');
        document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-block';
        document.getElementById('nextBtn').innerText = step === totalSteps ? 'Selesai' : 'Next';
    }

    document.getElementById('prevBtn').addEventListener('click', () => {
        if(currentStep > 1){
            currentStep--;
            showStep(currentStep);
        }
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        if(currentStep < totalSteps){
            currentStep++;
            showStep(currentStep);
        } else {
            // selesai tutorial
            document.getElementById('tutorial').classList.add('hidden');
            document.getElementById('startBtn').classList.remove('hidden');
        }
    });

    // init first step
    showStep(currentStep);


    // -----------------
    // WebSocket Handling
    // -----------------
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
