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
    Schema::create('screenings', function (Blueprint $table) {
        $table->id();
        // Data Profil
        $table->string('name');
        $table->string('whatsapp');
        $table->string('email');
        $table->string('info_source')->nullable();
        $table->boolean('marketing_opt_in')->default(false);

        // Data Hasil Screening
        $table->string('cancer_type');
        $table->string('risk_level'); // Rendah, Sedang, Tinggi
        $table->text('summary');

        $table->timestamps(); // otomatis membuat kolom created_at dan updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screenings');
    }
};
