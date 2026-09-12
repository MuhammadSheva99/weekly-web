@extends('layouts.karyawan')

@section('title', 'Dashboard Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Minggu {{ $mingguKe }} - {{ now()->translatedFormat('F Y') }}</p>

    @if ($commitment)
        <div class="bg-amber-50 rounded-xl px-5 py-4 mb-6">
            <p class="text-sm text-gray-700">
                Linked KPI : {{ $commitment->targetMingguan->targetBulanan->kpi->nama_kpi ?? '-' }}
                - target bulanan Rp {{ number_format($commitment->targetMingguan->targetBulanan->nilai_target ?? 0, 0, ',', '.') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
            <div class="bg-amber-50 rounded-xl p-5">
                <p class="text-sm text-gray-500">Target minggu ini</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($commitment->target, 0, ',', '.') }}</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-5">
                <p class="text-sm text-gray-500">Actual sementara</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">
                    Rp {{ number_format($commitment->actualMingguan->nilai_actual_final ?? $commitment->weeklyProgress->actual_sementara ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-amber-50 rounded-xl p-5">
                <p class="text-sm text-gray-500">Achievment</p>
                @php
                    $ach = $commitment->actualMingguan->achievement_pct ?? $commitment->weeklyProgress->achievement_pct ?? null;
                @endphp
                <p class="text-2xl font-bold text-amber-700 mt-1">{{ $ach !== null ? round($ach).'%' : '-' }}</p>
            </div>
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
            <div class="flex items-center justify-between px-6 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <p class="text-gray-700">{{ $h->label }}</p>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ str_contains($h->status, '%') ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $h->status }}
                </span>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-gray-400">Belum ada riwayat.</div>
        @endforelse
    </div>
@endsection