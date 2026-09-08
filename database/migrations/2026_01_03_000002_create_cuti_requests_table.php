<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuti_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('jenis_cuti', 50);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedInteger('jumlah_hari');
            $table->text('keterangan')->nullable();
            $table->string('lampiran_path')->nullable();
            $table->foreignUuid('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('jatah_cuti_tahunan')->default(12)->after('is_active');
            $table->unsignedInteger('sisa_cuti_tahun_lalu')->default(0)->after('jatah_cuti_tahunan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jatah_cuti_tahunan', 'sisa_cuti_tahun_lalu']);
        });
        Schema::dropIfExists('cuti_requests');
    }
};