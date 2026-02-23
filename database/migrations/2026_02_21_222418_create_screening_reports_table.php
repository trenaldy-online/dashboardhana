<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screening_reports', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Membuat ID URL yang acak dan aman
            $table->json('user_data'); // Menyimpan nama, usia, dll
            $table->json('chat_history'); // Menyimpan riwayat percakapan dengan AI
            $table->json('report_data'); // Menyimpan JSON hasil AI
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screening_reports');
    }
};
