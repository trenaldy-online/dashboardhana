<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('screening_reports', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('screening_reports', function (Blueprint $table) {
            $table->dropColumn('ip_address');
        });
    }
};
