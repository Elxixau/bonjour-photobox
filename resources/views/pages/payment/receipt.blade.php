@extends('layouts.app')

@section('content')
  <script type="text/javascript"
  src="https://app.midtrans.com/snap/snap.js"
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
                <span class="text-green-600 font-semibold">Berhasil</span>
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
     <div class="flex justify-between py-1">
        <span>Jumlah Cetak</span>
         <span>{{ $order->jumlah_cetak }} </span>
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

<button id="pay-button" class="mt-4  px-6 py-2 border-2 border-black bg-white rounded hover:bg-gray-100">
  Bayar!
</button>

    <script type="text/javascript">
      // For example trigger on button clicked, or any time you need
      var payButton = document.getElementById('pay-button');
      payButton.addEventListener('click', function () {
        // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token
        window.snap.pay('{{$snapToken}}', {
          onSuccess: function(result){
            Swal.fire({
          icon: 'success',
          title: 'Payment Success!',
          text: 'Pembayaran berhasil, mengalihkan ke invoice...',
          timer: 2000,
          timerProgressBar: true,
          showConfirmButton: false
        }).then(() => {
          window.location.href = '/invoice/{{$order->id}}';
        });
        console.log(result);
          },
          onPending: function(result){
            /* You may add your own implementation here */
            alert("wating your payment!"); console.log(result);
          },
          onError: function(result){
            /* You may add your own implementation here */
            alert("payment failed!"); console.log(result);
          },
          onClose: function(){
            /* You may add your own implementation here */
            alert('you closed the popup without finishing the payment');
          }
        })
      });
    </script>
@endsection
