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
          Schema::create('frames', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Nama frame, misal: Classic, Modern, dll
            $table->string('img_path');          // Path gambar frame (hiasan)
            $table->boolean('active')->default(true);  // Status frame aktif (default aktif)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frames');
    }
};
