<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('screening_reports', function (Blueprint $table) {
            $table->string('status')->default('valid')->after('user_data'); // 'valid' atau 'invalid'
            $table->integer('tokens_used')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('screening_reports', function (Blueprint $table) {
            $table->dropColumn(['status', 'tokens_used']);
        });
    }
};
