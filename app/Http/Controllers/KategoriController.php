<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    /**
     * Menampilkan daftar kategori untuk dipilih user
     */
    public function index()
    {
        $kategoris = Kategori::all();
        return view('kategori.select', compact('kategoris'));
    }

    /**
     * Menyimpan kategori yang dipilih ke session
     */
    public function choose(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        // Simpan kategori ke session
        session(['kategori_id' => $request->kategori_id]);

        // Redirect ke halaman panduan
        return redirect()->route('welcome');
    }
}
