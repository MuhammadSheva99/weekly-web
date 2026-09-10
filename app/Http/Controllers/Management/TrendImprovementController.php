<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\KpiPerformance;
use App\Models\User;
use Illuminate\Support\Carbon;

class TrendImprovementController extends Controller
{
    public function index()
    {
        $bulanList = collect();
        for ($i = 3; $i >= 0; $i--) {
            $bulanList->push(Carbon::now()->subMonths($i));
        }

        $trend = $bulanList->map(function ($bulan) {
            $avg = KpiPerformance::whereMonth('periode', $bulan->month)
                ->whereYear('periode', $bulan->year)
                ->avg('achievement_pct');

            return [
                'label' => $bulan->translatedFormat('F'),
                'value' => $avg ? round($avg) : null,
            ];
        });

        // Cek apakah menurun terus-menerus (hanya hitung bulan yang ada datanya)
        $validValues = $trend->pluck('value')->filter(fn ($v) => $v !== null)->values();
        $isDeclining = $validValues->count() >= 2 && $validValues->values()
            ->every(fn ($v, $i) => $i === 0 || $v <= $validValues[$i - 1]);

        // Divisi dengan achievement terendah bulan ini = "penarik ke bawah"
        $divisiList = Divisi::orderBy('nama')->get()->map(function ($divisi) {
            $avgSekarang = KpiPerformance::whereHas('user', fn ($q) => $q->where('divisi_id', $divisi->id))
                ->whereMonth('periode', now()->month)
                ->whereYear('periode', now()->year)
                ->avg('achievement_pct');

            $divisi->avg_sekarang = $avgSekarang ? round($avgSekarang) : null;

            // Cek tren 3 bulan terakhir divisi ini
            $riwayat = collect();
            for ($i = 2; $i >= 0; $i--) {
                $bulan = Carbon::now()->subMonths($i);
                $avg = KpiPerformance::whereHas('user', fn ($q) => $q->where('divisi_id', $divisi->id))
                    ->whereMonth('periode', $bulan->month)
                    ->whereYear('periode', $bulan->year)
                    ->avg('achievement_pct');
                $riwayat->push($avg);
            }
            $validRiwayat = $riwayat->filter(fn ($v) => $v !== null)->values();
            $divisi->is_declining_3mo = $validRiwayat->count() >= 3
                && $validRiwayat->every(fn ($v, $i) => $i === 0 || $v <= $validRiwayat[$i - 1]);

            return $divisi;
        });

        $divisiPenarikBawah = $divisiList->filter(fn ($d) => $d->avg_sekarang !== null)->sortBy('avg_sekarang')->first();
        $divisiTerbaik = $divisiList->filter(fn ($d) => $d->avg_sekarang !== null)->sortByDesc('avg_sekarang')->first();

        $insightText = null;
        if ($isDeclining && $divisiPenarikBawah) {
            $bulanTurun = $validValues->count();
            $insightText = "Achievement perusahaan menurun {$bulanTurun} bulan berturut-turut, terutama ditarik oleh Divisi {$divisiPenarikBawah->nama}. Perlu evaluasi menyeluruh sebelum periode berikutnya dimulai.";
        }

        // Rencana improvement: divisi turun 3 bulan, divisi ada underperform PIC, divisi terbaik
        $rencana = collect();

        foreach ($divisiList as $divisi) {
            if ($divisi->is_declining_3mo) {
                $rencana->push([
                    'judul' => "Divisi {$divisi->nama} - evaluasi kualitas proses produksi",
                    'deskripsi' => "Achievement turun 3 bulan berturut-turut, perlu peninjauan target dan sumber daya",
                ]);
            }
        }

        $adaUnderperformPic = User::whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'))
            ->whereHas('divisi')
            ->get()
            ->contains(function ($user) {
                $recent = KpiPerformance::where('user_id', $user->id)
                    ->orderByDesc('periode')->limit(3)->pluck('achievement_pct');
                return $recent->count() >= 3 && $recent->every(fn ($v) => $v < 80);
            });

        if ($adaUnderperformPic && $divisiPenarikBawah) {
            $rencana->push([
                'judul' => "Divisi {$divisiPenarikBawah->nama} - coaching untuk PIC underperform",
                'deskripsi' => "Tersedia di menu Report & Export",
            ]);
        }

        if ($divisiTerbaik) {
            $rencana->push([
                'judul' => "Divisi {$divisiTerbaik->nama} - pertahankan momentum",
                'deskripsi' => "Achievement konsisten di atas target, dapat dipertimbangkan menjadi acuan divisi lain",
            ]);
        }

        return view('management.trend', [
            'trend' => $trend,
            'insightText' => $insightText,
            'rencana' => $rencana,
        ]);
    }
}