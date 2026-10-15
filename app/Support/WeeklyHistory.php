<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Logika bersama History Weekly:
 * satu baris per minggu per orang, dan data detail satu minggu.
 */
class WeeklyHistory
{
    /** Relasi yang dibutuhkan untuk riwayat & detail. */
    public static function relasi(): array
    {
        return [
            'targetMingguan.targetBulanan.kpi',
            'weeklyProgress',
            'selfReview',
            'actualMingguan',
            'approvalComments.commentedBy.role',
        ];
    }

    private static function tampil($nilai, $satuan): string
    {
        if ($nilai === null || $nilai === '') {
            return '—';
        }

        return $satuan
            ? KpiFormat::tampil($nilai, $satuan)
            : number_format((float) $nilai, 0, ',', '.');
    }

    /**
     * @param  Collection  $commitments  WeeklyCommitment (banyak orang boleh bercampur)
     * @param  Collection  $users        User keyBy id (untuk nama)
     */
    public static function riwayat(Collection $commitments, Collection $users): Collection
    {
        return $commitments
            ->groupBy(fn ($wc) => $wc->user_id.'|'.($wc->targetMingguan->minggu_ke ?? '?').'|'.$wc->created_at->format('Y-m'))
            ->map(function ($group) use ($users) {
                $terbaru = $group->sortByDesc('created_at')->first();

                // Kalau minggu itu disubmit lebih dari sekali, ambil yang terbaru per KPI.
                $perKpi = $group->sortByDesc('created_at')
                    ->unique(fn ($wc) => $wc->target_mingguan_id ?? $wc->id)
                    ->sortBy('id')
                    ->values();

                $kpi = $perKpi->map(function ($wc) {
                    $k = $wc->targetMingguan->targetBulanan->kpi ?? null;
                    $satuan = $k->satuan ?? null;
                    $actual = $wc->selfReview?->actual
                        ?? $wc->actualMingguan?->nilai_actual_final
                        ?? $wc->weeklyProgress?->actual_sementara;

                    return (object) [
                        'nama' => $k->nama_kpi ?? 'Target Manual',
                        'target' => self::tampil($wc->target, $satuan),
                        'actual' => self::tampil($actual, $satuan),
                    ];
                });

                $isFinal = $perKpi->every(fn ($wc) => $wc->selfReview);
                $achievement = $perKpi
                    ->map(fn ($wc) => $wc->actualMingguan?->achievement_pct ?? $wc->selfReview?->achievement_pct)
                    ->filter(fn ($v) => $v !== null)
                    ->avg();

                return (object) [
                    'user_id' => $terbaru->user_id,
                    'nama' => $users[$terbaru->user_id]->nama ?? '-',
                    'minggu_label' => 'Minggu '.($terbaru->targetMingguan->minggu_ke ?? '-').' - '.$terbaru->created_at->translatedFormat('M'),
                    'big_goal' => $terbaru->big_goal,
                    'kpi' => $kpi,
                    'status_label' => $isFinal ? ($achievement !== null ? round($achievement).'%' : 'Selesai') : 'Berjalan',
                    'is_final' => $isFinal,
                    'achievement' => $achievement,
                    'jumlah_komentar' => $perKpi->sum(fn ($wc) => $wc->approvalComments->count()),
                    'key' => $terbaru->batch_id ?? $terbaru->id,
                    'terakhir' => $group->max('created_at'),
                ];
            })
            ->sortByDesc('terakhir')
            ->values();
    }

    /** Data halaman detail satu minggu (satu batch) milik satu orang. */
    public static function detail(Collection $commitments): array
    {
        $first = $commitments->first();
        $mingguKe = $first->targetMingguan->minggu_ke ?? null;
        $label = $first->created_at->translatedFormat('\M\i\n\g\g\u '.($mingguKe ?? '?').' - F Y');

        $prioritas = $first->prioritas;
        if (is_string($prioritas)) {
            $prioritas = json_decode($prioritas, true) ?: [];
        }
        $prioritas = array_values(array_filter((array) $prioritas));

        $catatan = $commitments->map->weeklyProgress->filter()->first();

        $pertanyaan = [
            'apa_berhasil' => 'Apa yang berhasil?',
            'apa_gagal' => 'Apa yang gagal?',
            'kenapa_gagal' => 'Kenapa gagal?',
            'apa_beda' => 'Apa yang seharusnya dilakukan berbeda?',
            'improvement_depan' => 'Apa improvement minggu depan?',
            'kritik_diri' => 'Kalau saya jadi atasan, apa yang akan saya kritik dari pekerjaan saya minggu ini?',
        ];
        $reviewCatatan = [];
        $review = $commitments->map->selfReview->filter()->first();
        if ($review) {
            foreach ($pertanyaan as $kolom => $judul) {
                if (filled($review->{$kolom})) {
                    $reviewCatatan[$judul] = $review->{$kolom};
                }
            }
        }

        $komentar = $commitments->flatMap(function ($c) {
            $kpi = $c->targetMingguan->targetBulanan->kpi->nama_kpi ?? null;

            return $c->approvalComments->map(fn ($a) => (object) [
                'pemberi' => $a->commentedBy->nama ?? '-',
                'peran' => $a->commentedBy->role->nama ?? '',
                'kpi' => $kpi,
                'isi' => $a->comment,
                'waktu' => $a->created_at,
            ]);
        })->sortByDesc('waktu')->values();

        return compact('commitments', 'first', 'label', 'prioritas', 'catatan', 'reviewCatatan', 'komentar');
    }
}