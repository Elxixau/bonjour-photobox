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
        // Drop tabel lama jika ada
        Schema::dropIfExists('order_addons');

        // Buat tabel baru
        Schema::create('order_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('addons_id')->constrained('addons')->onDelete('cascade');
            $table->integer('qty')->default(1);
            $table->integer('harga'); // harga addon pada saat order
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_addons');
    }
};
