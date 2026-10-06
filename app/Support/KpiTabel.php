<?php

namespace App\Support;

use App\Models\KpiPerformance;
use App\Models\TargetBulanan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Menyusun data tabel KPI per orang untuk satu bulan:
 * KPI + bobot + target dari TargetBulanan, realisasi + pencapaian dari KpiPerformance.
 * Dipakai bersama oleh halaman Karyawan, Atasan, HRD, dan Management.
 */
class KpiTabel
{
    /** Ubah teks "Y-m" jadi awal bulan; kalau kosong/salah, pakai bulan ini. */
    public static function periode(?string $input): Carbon
    {
        if ($input && preg_match('/^\d{4}-\d{2}$/', $input)) {
            return Carbon::createFromFormat('Y-m', $input)->startOfMonth();
        }

        return now()->startOfMonth();
    }

    /** Status penilaian dari skor total (sama dengan Monitoring Karyawan). */
    public static function status(?float $skor): ?string
    {
        if ($skor === null) {
            return null;
        }

        return match (true) {
            $skor >= 90 => 'Sangat Baik',
            $skor >= 80 => 'Baik',
            $skor >= 70 => 'Cukup',
            default => 'Buruk',
        };
    }

    /** Grade huruf dari skor (sama dengan ActualMingguanObserver). */
    public static function grade(?float $skor): ?string
    {
        if ($skor === null) {
            return null;
        }

        return match (true) {
            $skor >= 90 => 'A',
            $skor >= 80 => 'B',
            $skor >= 70 => 'C',
            default => 'D',
        };
    }

    /**
     * Daftar orang (yang punya target KPI di periode itu) dari query User yang
     * sudah dibatasi sesuai role (semua / satu divisi / bawahan).
     */
    public static function daftar($userQuery, Carbon $periode, ?string $cari = null): Collection
    {
        $idPunyaKpi = TargetBulanan::whereYear('periode', $periode->year)
            ->whereMonth('periode', $periode->month)
            ->pluck('user_id')
            ->unique();

        $users = $userQuery
            ->whereIn('id', $idPunyaKpi)
            ->when($cari, fn ($q) => $q->where('nama', 'like', '%'.$cari.'%'))
            ->with('divisi')
            ->orderBy('nama')
            ->get();

        return self::bangunBanyak($users, $periode);
    }

    public static function bangunBanyak(Collection $users, Carbon $periode): Collection
    {
        $ids = $users->pluck('id');

        $targets = TargetBulanan::with('kpi')
            ->whereIn('user_id', $ids)
            ->whereYear('periode', $periode->year)
            ->whereMonth('periode', $periode->month)
            ->get()
            ->groupBy('user_id');

        $performa = KpiPerformance::whereIn('user_id', $ids)
            ->whereYear('periode', $periode->year)
            ->whereMonth('periode', $periode->month)
            ->get()
            ->groupBy('user_id')
            ->map(fn ($g) => $g->keyBy('kpi_id'));

        return $users->map(function ($user) use ($targets, $performa) {
            $perfUser = $performa->get($user->id, collect());

            $rows = $targets->get($user->id, collect())
                ->filter(fn ($t) => $t->kpi)
                ->sortBy(fn ($t) => $t->kpi->id)
                ->values()
                ->map(function ($t) use ($perfUser) {
                    $p = $perfUser->get($t->kpi_id);
                    $bobot = (float) $t->bobot;
                    $pencapaian = $p ? (float) $p->achievement_pct : null;

                    return (object) [
                        'kpi' => $t->kpi,
                        'bobot' => $bobot,
                        'target' => (float) $t->nilai_target,
                        'realisasi' => $p ? (float) $p->actual_bulanan : null,
                        'pencapaian' => $pencapaian,
                        'kontribusi' => $pencapaian !== null ? round($pencapaian * $bobot / 100, 2) : null,
                        'grade' => $p->score ?? null,
                    ];
                });

            $terisi = $rows->whereNotNull('pencapaian')->count();
            $skorTotal = $terisi > 0 ? round((float) $rows->sum(fn ($r) => $r->kontribusi ?? 0), 2) : null;

            return (object) [
                'user' => $user,
                'rows' => $rows,
                'jumlah' => $rows->count(),
                'terisi' => $terisi,
                'bobot_total' => (float) $rows->sum('bobot'),
                'skor_total' => $skorTotal,
                'status' => self::status($skorTotal),
                'grade' => self::grade($skorTotal),
            ];
        })->values();
    }
}