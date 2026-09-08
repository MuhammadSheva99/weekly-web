<?php

namespace App\Exports;

use App\Models\Divisi;
use App\Models\KpiPerformance;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportExport implements FromArray, WithHeadings, WithStyles
{
    public function __construct(
        protected ?string $divisiId,
        protected string $periode,
    ) {}

    protected function getData(): array
    {
        $bulan = Carbon::createFromFormat('Y-m', $this->periode);

        $query = Divisi::query();
        if ($this->divisiId) {
            $query->where('id', $this->divisiId);
        }

        return $query->orderBy('nama')->get()->map(function ($divisi) use ($bulan) {
            $totalPic = $divisi->users()->whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'))->count();

            $performances = KpiPerformance::whereHas('user', fn ($q) => $q->where('divisi_id', $divisi->id))
                ->whereMonth('periode', $bulan->month)
                ->whereYear('periode', $bulan->year)
                ->get();

            $targetTotal = $performances->sum(fn ($p) => $p->actual_bulanan / max($p->achievement_pct / 100, 0.0001));
            $actualTotal = $performances->sum('actual_bulanan');
            $avgAchievement = $performances->avg('achievement_pct');

            return [
                $divisi->nama,
                $totalPic,
                $targetTotal ? number_format($targetTotal, 0, ',', '.') : '-',
                $actualTotal ? number_format($actualTotal, 0, ',', '.') : '-',
                $avgAchievement !== null ? round($avgAchievement).'%' : '-',
            ];
        })->toArray();
    }

    public function array(): array
    {
        return $this->getData();
    }

    public function headings(): array
    {
        return ['Divisi', 'Total PIC', 'Target Bulanan', 'Actual Bulanan', 'Achievement'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}