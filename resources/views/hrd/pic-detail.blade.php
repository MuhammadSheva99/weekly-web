@extends('layouts.hrd')

@section('title', 'Detail PIC')

@section('content')
    <a href="{{ route('hrd.monitoring-pic.index') }}" class="text-sm text-gray-500 hover:underline">← Kembali ke Monitoring PIC</a>

    <div class="flex items-center gap-4 mt-3 mb-8">
        <div class="w-14 h-14 rounded-full bg-blue-700 text-white flex items-center justify-center font-bold text-lg">
            {{ strtoupper(substr($pic->nama, 0, 2)) }}
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $pic->nama }}</h1>
            <p class="text-gray-500">{{ $pic->divisi->nama ?? '-' }} · {{ $pic->jabatan ?? $pic->role->nama }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
        <div class="bg-blue-50 rounded-xl p-6">
            <p class="text-sm text-gray-500">KPI Value bulan {{ $periode->translatedFormat('F Y') }}</p>
            <p class="text-4xl font-bold text-gray-900 mt-1">{{ $ringkasan['kpi_value'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Total bobot terhitung: {{ $ringkasan['total_bobot'] }}%</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-6 flex flex-col justify-center items-center">
            <p class="text-sm text-gray-500 mb-2">Grade</p>
            <span @class([
                'w-16 h-16 rounded-full flex items-center justify-center text-3xl font-bold text-white',
                'bg-green-600' => $ringkasan['grade'] === 'A',
                'bg-blue-600' => $ringkasan['grade'] === 'B',
                'bg-orange-500' => $ringkasan['grade'] === 'C',
                'bg-red-600' => $ringkasan['grade'] === 'D',
            ])>
                {{ $ringkasan['grade'] }}
            </span>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Breakdown per KPI</h2>
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-10">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4 pl-6">KPI</th>
                    <th class="py-3 pr-4">Bobot</th>
                    <th class="py-3 pr-4">Achievement</th>
                    <th class="py-3 pr-4">Skor</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ringkasan['items'] as $item)
                    <tr class="border-b">
                        <td class="py-3 pr-4 pl-6 font-medium text-gray-900">{{ $item['nama_kpi'] }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $item['bobot'] }}%</td>
                        <td class="py-3 pr-4 text-gray-600">{{ round($item['achievement'], 1) }}%</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-700">{{ $item['score'] }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-gray-400">Belum ada data KPI Performance bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Weekly Commitment (4 terakhir)</h2>
    <div class="space-y-4">
        @forelse ($riwayatMingguan as $wc)
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="font-semibold text-gray-900">{{ $wc->targetMingguan->targetBulanan->kpi->nama_kpi ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $wc->created_at->translatedFormat('d M Y') }}</p>
                </div>
                <p class="text-sm text-gray-600 mb-3">{{ $wc->big_goal }}</p>
                <div class="flex gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Target</p>
                        <p class="font-medium">{{ number_format($wc->target, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Actual Final</p>
                        <p class="font-medium">{{ $wc->actualMingguan ? number_format($wc->actualMingguan->nilai_actual_final, 0, ',', '.') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Achievement</p>
                        <p class="font-medium">{{ $wc->actualMingguan ? round($wc->actualMingguan->achievement_pct).'%' : '-' }}</p>
                    </div>
                </div>
                @if ($wc->selfReview)
                    <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-600">
                        <p><span class="font-medium text-gray-800">Apa yang berhasil:</span> {{ $wc->selfReview->apa_berhasil ?? '-' }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-400">
                Belum ada riwayat Weekly Commitment.
            </div>
        @endforelse
    </div>
@endsection