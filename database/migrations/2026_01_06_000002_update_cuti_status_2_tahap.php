<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE cuti_requests MODIFY status ENUM('menunggu_atasan', 'menunggu_hrd', 'disetujui', 'ditolak') DEFAULT 'menunggu_atasan'");

        Schema::table('cuti_requests', function (Blueprint $table) {
            $table->foreignUuid('atasan_approved_by')->nullable()->after('disetujui_oleh')->constrained('users')->onDelete('set null');
            $table->dateTime('atasan_approved_at')->nullable()->after('atasan_approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('cuti_requests', function (Blueprint $table) {
            $table->dropForeign(['atasan_approved_by']);
            $table->dropColumn(['atasan_approved_by', 'atasan_approved_at']);
        });

        DB::statement("ALTER TABLE cuti_requests MODIFY status ENUM('menunggu', 'disetujui', 'ditolak') DEFAULT 'menunggu'");
    }
};