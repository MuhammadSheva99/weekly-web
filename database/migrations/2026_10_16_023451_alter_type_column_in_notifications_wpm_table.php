<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    composerRequireDbal:
    public function up(): void
    {
        Schema::table('notifications_wpm', function (Blueprint $table) {
            // Mengubah tipe data kolom type menjadi VARCHAR(50) agar tidak terjadi truncation error
            $table->string('type', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications_wpm', function (Blueprint $table) {
            // Mengembalikan ke tipe enum semula jika dilakukan rollback (sesuaikan nilainya jika perlu)
            $table->enum('type', ['info', 'warning', 'success', 'sp_rekomendasi_baru'])->change();
        });
    }
};