@extends('layouts.hrd')

@section('title', 'KPI Saya')

@section('content')
    @php
        $fmt = fn ($n, $desimal = 1) => rtrim(rtrim(number_format((float) $n, $desimal, ',', '.'), '0'), ',');
    @endphp

    <h1 class="text-3xl font-bold text-gray-900">KPI Saya</h1>
    <p class="text-gray-500 mt-1 mb-8">Target, realisasi, dan pencapaian KPI kamu per bulan</p>

    <form method="GET" class="flex items-center gap-3 mb-6">
        <label class="text-sm text-gray-500">Periode</label>
        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
               class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
    </form>

    @if ($data->skor_total !== null)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5">
                <p class="text-xs text-gray-500">Skor KPI bulan ini</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $fmt($data->skor_total) }}%</p>
            </div>
            <div class="bg-white rounded-2xl p-5">
                <p class="text-xs text-gray-500">Status</p>
                <p class="text-xl font-bold text-gray-900 mt-2">{{ $data->status }} <span class="text-gray-400 font-normal">(Grade {{ $data->grade }})</span></p>
            </div>
            <div class="bg-white rounded-2xl p-5">
                <p class="text-xs text-gray-500">KPI terisi</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $data->terisi }}<span class="text-lg text-gray-400">/{{ $data->jumlah }}</span></p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden">
        @include('partials.kpi.tabel', ['d' => $data])
    </div>
    <p class="text-xs text-gray-400 mt-3">Skor dihitung dari pencapaian tiap KPI dikalikan bobotnya. Realisasi terisi setelah Self Review mingguan disimpan.</p>
@endsection