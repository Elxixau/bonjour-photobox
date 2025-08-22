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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('frame_id')->nullable()->after('status');
            $table->unsignedBigInteger('sticker_id')->nullable()->after('frame_id');

            $table->foreign('frame_id')->references('id')->on('frames')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['frame_id']);
            $table->dropColumn(['frame_id']);
        });
    }
};
