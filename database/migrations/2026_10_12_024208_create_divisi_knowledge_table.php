<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisi_knowledge', function (Blueprint $table) {
            $table->id();

            // Opsional: relasi ke tabel divisi aplikasi Anda.
            // Tidak dipasang foreign key agar tidak bergantung pada nama tabel divisi.
            $table->char('divisi_id', 36)->nullable()->index(); // ID divisi berupa UUID

            // Kunci pencarian yang stabil, misalnya: advertiser, admin-order, hr-ga
            $table->string('slug')->unique();
            $table->string('nama_divisi');

            // Isi referensi masalah & solusi (teks), bisa diedit HR/Supervisor.
            $table->longText('content');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisi_knowledge');
    }
};