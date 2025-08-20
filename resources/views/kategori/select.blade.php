@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6">Pilih Kategori</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($kategoris as $kategori)
        <form method="POST" action="{{ route('kategori.set') }}">
            @csrf
            <input type="hidden" name="kategori_id" value="{{ $kategori->id }}">
            <div class="border-2 border-black p-6 rounded-lg shadow-black shadow-[5px_5px_0_0] bg-white">
                <h2 class="text-xl font-semibold">{{ $kategori->nama }}</h2>


                <button type="submit" class="mt-4 bg-black text-white px-4 py-2 rounded hover:bg-gray-800">
                    Pilih Kategori
                </button>
            </div>
        </form>
    @endforeach
</div>
@endsection
