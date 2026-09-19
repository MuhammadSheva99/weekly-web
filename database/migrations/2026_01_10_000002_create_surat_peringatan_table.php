<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_peringatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('level', ['SP1', 'SP2', 'SP3']);
            $table->text('alasan');
            $table->text('konsekuensi')->nullable();
            $table->date('tanggal_terbit');
            $table->date('tanggal_berakhir');
            $table->foreignUuid('diterbitkan_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_peringatan');
    }
};