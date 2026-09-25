<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_peringatan', function (Blueprint $table) {
            $table->string('no_sp')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('surat_peringatan', function (Blueprint $table) {
            $table->dropColumn('no_sp');
        });
    }
};