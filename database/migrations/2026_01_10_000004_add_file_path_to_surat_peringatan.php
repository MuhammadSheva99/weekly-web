<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_peringatan', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('diterbitkan_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('surat_peringatan', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
};