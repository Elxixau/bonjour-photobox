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
           Schema::dropIfExists('addon_kategori');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
          $table->id();
        $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');
        $table->foreignId('addons_id')->constrained('addons')->onDelete('cascade');
        $table->timestamps();
    }
};
