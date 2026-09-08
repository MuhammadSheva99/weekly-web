<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Exports\ReportExport;
use App\Models\Divisi;
use App\Models\KpiPerformance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $divisiId = $request->get('divisi_id');
        $periode = $request->get('periode', now()->format('Y-m'));
        $tipe = $request->get('tipe', 'weekly');

        $rows = $this->buildReportRows($divisiId, $periode);

        return view('hrd.report', [
            'divisiList' => Divisi::orderBy('nama')->get(),
            'rows' => $rows,
            'divisiId' => $divisiId,
            'periode' => $periode,
            'tipe' => $tipe,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $divisiId = $request->get('divisi_id');
        $periode = $request->get('periode', now()->format('Y-m'));

        return Excel::download(new ReportExport($divisiId, $periode), 'laporan-performance-'.$periode.'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $divisiId = $request->get('divisi_id');
        $periode = $request->get('periode', now()->format('Y-m'));

        $rows = $this->buildReportRows($divisiId, $periode);

        $pdf = Pdf::loadView('hrd.report-pdf', [
            'rows' => $rows,
            'periode' => Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y'),
        ]);

        return $pdf->download('laporan-performance-'.$periode.'.pdf');
    }

    protected function buildReportRows(?string $divisiId, string $periode): array
    {
        $bulan = Carbon::createFromFormat('Y-m', $periode);

        $query = Divisi::query();
        if ($divisiId) {
            $query->where('id', $divisiId);
        }

        return $query->orderBy('nama')->get()->map(function ($divisi) use ($bulan) {
            $totalPic = $divisi->users()->whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'))->count();

            $performances = KpiPerformance::whereHas('user', fn ($q) => $q->where('divisi_id', $divisi->id))
                ->whereMonth('periode', $bulan->month)
                ->whereYear('periode', $bulan->year)
                ->get();

            $targetTotal = $performances->sum(fn ($p) => $p->achievement_pct > 0 ? $p->actual_bulanan / ($p->achievement_pct / 100) : 0);
            $actualTotal = $performances->sum('actual_bulanan');
            $avgAchievement = $performances->avg('achievement_pct');

            return (object) [
                'divisi' => $divisi->nama,
                'total_pic' => $totalPic,
                'target' => $targetTotal,
                'actual' => $actualTotal,
                'achievement' => $avgAchievement !== null ? round($avgAchievement) : null,
            ];
        })->toArray();
    }
}