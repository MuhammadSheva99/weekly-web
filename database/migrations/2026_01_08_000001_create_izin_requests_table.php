<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('jenis_izin', 30); // berangkat_siang, pulang_cepat, berangkat_terlambat, keluar_sementara
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('estimasi_kembali')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('lampiran_path')->nullable();
            $table->foreignUuid('atasan_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('atasan_approved_at')->nullable();
            $table->foreignUuid('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['menunggu_atasan', 'menunggu_hrd', 'disetujui', 'ditolak'])->default('menunggu_atasan');
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin_requests');
    }
};