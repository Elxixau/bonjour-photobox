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
        Schema::create('kategori_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')
                  ->constrained('kategori')
                  ->onDelete('cascade'); // jika kategori dihapus, background ikut terhapus
            $table->string('background_video')->nullable(); // path/url video background
            $table->string('background_color')->nullable(); // hex/nama warna background
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_backgrounds');
    }
};
