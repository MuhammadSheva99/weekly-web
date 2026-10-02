<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sp_rekomendasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('direkomendasikan_oleh')->constrained('users')->onDelete('cascade');
            $table->enum('level_usulan', ['SP1', 'SP2', 'SP3']);
            $table->text('alasan');
            $table->string('lampiran_path')->nullable();
            $table->enum('status', ['menunggu', 'diterbitkan', 'ditolak'])->default('menunggu');
            $table->text('alasan_penolakan')->nullable();
            $table->foreignUuid('diproses_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('surat_peringatan_id')->nullable()->constrained('surat_peringatan')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sp_rekomendasi');
    }
};