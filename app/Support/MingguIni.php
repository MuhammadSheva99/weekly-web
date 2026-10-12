<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Satu definisi "minggu ke-berapa dalam bulan" untuk seluruh aplikasi.
 *
 * Minggu mengikuti kalender: Senin-Minggu. Minggu 1 = minggu yang memuat tanggal 1,
 * lalu tiap Senin berganti minggu. Minggu ke-5 atau lebih memakai target minggu 4
 * (target mingguan hanya ada 4 per bulan).
 */
class MingguIni
{
    public static function mingguKe(?Carbon $tanggal = null): int
    {
        $tanggal ??= now();
        $selisihAwal = $tanggal->copy()->startOfMonth()->dayOfWeekIso - 1; // Senin = 0

        return min((int) ceil(($tanggal->day + $selisihAwal) / 7), 4);
    }
}