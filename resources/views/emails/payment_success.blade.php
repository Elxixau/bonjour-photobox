<x-mail::message>
# Pembayaran Berhasil

Halo, {{ $order->email }}

Terima kasih sudah melakukan pembayaran untuk pesanan dengan kode:

**{{ $order->order_code }}**

---

## Detail Pesanan

- **Nama Paket:** {{ $order->kategori->nama }}
- **Harga Paket:** Rp {{ number_format($order->harga_paket, 0, ',', '.') }}
- **Durasi Waktu:** {{ $order->waktu }} menit

@if($order->orderAddons->count() > 0)
## Add-ons
@foreach ($order->orderAddons as $addon)
- {{ $addon->addon->nama }} — Qty: {{ $addon->qty }} — Rp {{ number_format($addon->harga * $addon->qty, 0, ',', '.') }}
@endforeach
@endif

---

**Total Harga:** Rp {{ number_format($order->total_harga, 0, ',', '.') }}

<x-mail::button :url="route('home')">
Kembali ke Beranda
</x-mail::button>

Terima kasih telah menggunakan layanan kami.<br>
{{ config('app.name') }}
</x-mail::message>
