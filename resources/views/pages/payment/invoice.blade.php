@extends('layouts.app')

@section('content')
  



{{-- Tutorial Section --}}
<div id="tutorial" class="w-full mx-auto bg-white border-2 border-black rounded-lg p-4 font-serif text-sm mb-4">
    <div class="tutorial-step hidden" data-step="1">
        <img src="{{asset('image/tutorial/tutorial1.png')}}" 
             class="mx-auto mb-4 w-full  object-contain" alt="Step 1">
        <p class="text-center text-xl font-bold">Pilih frame tersedia yang diinginkan, lalu tekan next.</p>
    </div>
    <div class="tutorial-step hidden" data-step="2">
        <img src="{{asset('image/tutorial/tutorial2.png')}}" 
             class="mx-auto mb-4 w-full object-contain" alt="Step 2">
        <p class="text-center text-xl font-bold">Selanjutnya melakukan capture gambar. Tap tombol X pada pojok kiri layar untuk capture ulang.</p>
    </div>
    <div class="tutorial-step hidden" data-step="3">
        <img src="{{asset('image/tutorial/tutorial3.png')}}" 
             class="mx-auto mb-4 w-full  object-contain" alt="Step 3">
        <p class="text-center text-xl font-bold">Pilih efek filter yang diinginkan, lalu tekan next.</p>
    </div>
      <div class="tutorial-step hidden" data-step="4">
        <img src="{{asset('image/tutorial/tutorial4.png')}}" 
             class="mx-auto mb-4 w-full  object-contain" alt="Step 3">
        <p class="text-center text-xl font-bold">Tahap terakhir, pada tahap ini tekan selesai untuk menyelesaikan sesi foto.</p>
    </div>
</div>
<div class="w-full mx-auto bg-white border-2 border-black rounded-lg p-4 font-serif text-sm mb-4">
    <div class="px-4 text-base ">
        <span class="font-black"> Reminder :</span>
          <span> Jangan menekan tombol selesai atau bagian timer sampai waktu sampai pada tahap terakhir sesi foto.</span>
    </div>
</div>

<div id="stepButton" class="flex w-full justify-between ">
    <button id="prevBtn" class="px-4 py-2 rounded-md border-2 border-black text-black font-semibold bg-gray-400">Back</button>
    <button id="nextBtn" class="px-4 py-2 rounded-md border-2 border-black text-black font-semibold bg-white">Next</button>
</div>

{{-- Start Button (hidden until tutorial selesai) --}}
<button id="startBtn" class="hidden rounded-md border-2 border-black bg-white text-black font-semibold py-2 p-4 mt-4">
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
        document.getElementById('nextBtn').innerText = step === totalSteps ? 'Next' : 'Next';
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
            document.getElementById('stepButton').classList.add('hidden');
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
