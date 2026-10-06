<?php

namespace App\Support;

class KpiFormat
{
    /** Nilai dari DB -> teks tampilan sesuai satuan. */
    public static function tampil($nilai, ?string $satuan): string
    {
        $nilai = (float) $nilai;

        return match ($satuan) {
            '%'      => self::angka($nilai * 100) . ' %',   // 0.05 -> "5 %"
            'Rupiah' => 'Rp ' . number_format($nilai, 0, ',', '.'),
            default  => self::angka($nilai) . ' ' . $satuan, // Angka, Hari, Bulan, Menit
        };
    }

    /** Input user -> nilai untuk disimpan ke DB (20 -> 0.2 untuk %). */
    public static function simpan($input, ?string $satuan): float
    {
        $nilai = (float) str_replace(['.', ','], ['', '.'], (string) $input);

        return $satuan === '%' ? round($nilai / 100, 4) : $nilai;
    }

    private static function angka(float $n): string
    {
        // Maksimal 2 desimal, buang nol di belakang: 5.00 -> "5", 12.50 -> "12,5"
        return rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',');
    }
}