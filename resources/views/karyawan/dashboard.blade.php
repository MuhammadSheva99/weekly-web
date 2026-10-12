@extends('layouts.karyawan')

@section('title', 'Dashboard Karyawan')

@section('content')
    @php
        // Format nilai sesuai jenis metric (berdasarkan nama KPI).
        $fmt = function ($nama, $nilai) {
            $nilai = (float) $nilai;
            $n = strtolower((string) $nama);
            if (preg_match('/revenue|sales|omzet|rupiah|biaya|cost|budget/', $n)) {
                return 'Rp '.number_format($nilai, 0, ',', '.');
            }
            if (preg_match('/rate|persen|%|conversion|rasio/', $n)) {
                $pct = abs($nilai) <= 1 ? $nilai * 100 : $nilai;
                return rtrim(rtrim(number_format($pct, 2, ',', '.'), '0'), ',').'%';
            }
            return rtrim(rtrim(number_format($nilai, 2, ',', '.'), '0'), ',');
        };
    @endphp

    <h1 class="text-3xl font-bold text-gray-900">Dashboard Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Minggu {{ $mingguKe }} - {{ now()->translatedFormat('F Y') }}</p>

    @if ($commitments->isNotEmpty())
        <div class="space-y-6 mb-10">
            @foreach ($commitments as $c)
                @php
                    $kpi = $c->targetMingguan->targetBulanan->kpi ?? null;
                    $namaKpi = $kpi->nama_kpi ?? '-';
                    $targetBulanan = $c->targetMingguan->targetBulanan->nilai_target ?? 0;
                    $actual = $c->actualMingguan->nilai_actual_final ?? $c->weeklyProgress->actual_sementara ?? 0;
                    $ach = $c->actualMingguan->achievement_pct ?? $c->weeklyProgress->achievement_pct ?? null;
                @endphp

                <div>
                    <div class="bg-amber-50 rounded-xl px-5 py-4 mb-3">
                        <p class="text-sm text-gray-700">
                            Linked KPI : {{ $namaKpi }} - target bulanan {{ $fmt($namaKpi, $targetBulanan) }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-amber-50 rounded-xl p-5">
                            <p class="text-sm text-gray-500">Target minggu ini</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $fmt($namaKpi, $c->target) }}</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-5">
                            <p class="text-sm text-gray-500">Actual sementara</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $fmt($namaKpi, $actual) }}</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-5">
                            <p class="text-sm text-gray-500">Achievment</p>
                            <p class="text-2xl font-bold text-amber-700 mt-1">{{ $ach !== null ? round($ach).'%' : '-' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center mb-10">
            <p class="text-gray-600">Kamu belum mengisi Weekly Commitment minggu ini.</p>
            <a href="{{ route('karyawan.weekly-commitment.create') }}" class="inline-block mt-4 px-6 py-2.5 bg-amber-500 text-white rounded-lg text-sm font-semibold hover:bg-amber-600">
                Isi Weekly Commitment
            </a>
        </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">History Weekly</h2>
        <span class="text-sm text-gray-400">4 minggu terakhir</span>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        @forelse ($historyWeekly as $h)
            <a href="{{ route('karyawan.feedback.show', $h->key) }}"
               class="flex items-center justify-between px-6 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}"
               style="text-decoration:none;color:inherit;">
                <p class="text-gray-700">{{ $h->label }}</p>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ str_contains($h->status, '%') ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $h->status }}
                </span>
            </a>
        @empty
            <div class="px-6 py-8 text-center text-gray-400">Belum ada riwayat.</div>
        @endforelse
    </div>
@endsection