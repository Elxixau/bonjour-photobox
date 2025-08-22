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
          Schema::create('frame_layouts', function (Blueprint $table) {
                  $table->id();
            $table->foreignId('frame_id')->constrained('frames')->onDelete('cascade');
            $table->integer('slot_number'); // <<=== tambahkan ini
            $table->integer('x');
            $table->integer('y');
            $table->integer('width');
            $table->integer('height');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frame_layouts');
    }
};
