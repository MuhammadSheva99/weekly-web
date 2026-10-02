@extends('layouts.management')

@section('title', 'Monitoring Divisi')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Monitoring Divisi</h1>
    <p class="text-gray-500 mt-1 mb-8">Monitoring Performa Divisi</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($divisiList as $d)
            @php
                $colorClass = match ($d->status_label) {
                    'Sangat Baik' => 'text-green-700',
                    'Baik' => 'text-blue-700',
                    'Cukup' => 'text-amber-700',
                    'Buruk' => 'text-red-700',
                    default => 'text-gray-400',
                };
            @endphp
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900">{{ $d->divisi->nama }}</h2>
                <p class="text-sm text-gray-400 mb-4">Head : {{ $d->head ?? '-' }}</p>

                <div class="text-4xl font-bold {{ $colorClass }} mb-1">
                    {{ $d->achievement !== null ? rtrim(rtrim(number_format($d->achievement, 1), '0'), '.').'%' : '-' }}
                </div>
                <p class="text-sm text-gray-500 mb-4">Achievement bulan berjalan</p>

                <div class="border-t pt-3 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Karyawan</span>
                        <span class="font-semibold text-gray-900">{{ $d->total_karyawan }} Orang</span>
                    </div>
                    @forelse ($d->kpiList as $kpi)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Target Bulanan ({{ $kpi->nama_kpi }})</span>
                            <span class="font-semibold text-gray-900">
                                {{ rtrim(rtrim(number_format($kpi->total_target, 1), '0'), '.') }} {{ $kpi->satuan }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-xs">Belum ada KPI untuk divisi ini.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
@endsection