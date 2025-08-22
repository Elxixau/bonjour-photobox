<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       // tabel orders
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_code')->unique(); // kode unik order
    $table->integer('waktu'); 
    
    // relasi ke kategori
    $table->foreignId('kategori_id')
          ->constrained('kategori')
          ->onDelete('cascade'); 
    
    // relasi ke qr_access (boleh null)
    $table->foreignId('qr_id')
          ->nullable()
          ->constrained('qr_access')
          ->nullOnDelete();

    // harga paket saat order dilakukan (jaga history)
    $table->integer('harga_paket'); 

    // total harga (jika ada tambahan, dll)
    $table->integer('total_harga'); 

    // status order
    $table->enum('status', ['pending', 'success'])
          ->default('pending');
    $table->integer('waktu'); 
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
