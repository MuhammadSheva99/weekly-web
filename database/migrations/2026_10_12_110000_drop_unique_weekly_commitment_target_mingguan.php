<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Minggu sekarang mengikuti kalender (Senin-Minggu), jadi satu target_mingguan boleh
     * punya commitment lagi di minggu kalender berikutnya (mis. minggu ke-5 memakai target minggu 4).
     * Index biasa ditambah dulu supaya foreign key tetap punya index saat unique dihapus.
     */
    public function up(): void
    {
        Schema::table('weekly_commitment', function (Blueprint $table) {
            $table->index('target_mingguan_id', 'weekly_commitment_target_mingguan_id_index');
        });

        Schema::table('weekly_commitment', function (Blueprint $table) {
            $table->dropUnique('weekly_commitment_target_mingguan_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('weekly_commitment', function (Blueprint $table) {
            $table->unique('target_mingguan_id', 'weekly_commitment_target_mingguan_id_unique');
        });

        Schema::table('weekly_commitment', function (Blueprint $table) {
            $table->dropIndex('weekly_commitment_target_mingguan_id_index');
        });
    }
};