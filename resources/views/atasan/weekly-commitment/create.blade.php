@extends('layouts.atasan')

@section('title', 'Weekly Commitment')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Weekly Commitment</h1>
    <p class="text-gray-500 mt-1 mb-8">Senin - Minggu {{ $mingguKe }}, {{ now()->translatedFormat('F Y') }} - KPI {{ $targetMingguan->targetBulanan->kpi->nama_kpi }}</p>

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-red-50 rounded-2xl p-6 mb-2">
        <p class="text-sm text-gray-500">Linked KPI</p>
        <p class="font-bold text-gray-900 mt-1">
            {{ $targetMingguan->targetBulanan->kpi->nama_kpi }} · target minggu ini Rp {{ number_format($targetMingguan->nilai_target, 0, ',', '.') }}
        </p>
    </div>
    <p class="text-sm text-gray-400 mb-8">Wajib disubmit sebelum Senin 23:59</p>

    <form method="POST" action="{{ route('atasan.weekly-commitment.store') }}">
        @csrf
        <input type="hidden" name="target_mingguan_id" value="{{ $targetMingguan->id }}">

        <label class="block text-sm text-gray-500 mb-2">Big goal</label>
        <input type="text" name="big_goal" required value="{{ old('big_goal') }}"
               placeholder="Mencapai omzet paket usaha Rp {{ number_format($targetMingguan->nilai_target, 0, ',', '.') }} minggu ini"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-red-400">

        <label class="block text-sm text-gray-700 font-semibold mb-3">3 prioritas</label>
        @for ($i = 0; $i < 3; $i++)
            <input type="text" name="prioritas[]" required value="{{ old('prioritas.'.$i) }}"
                   placeholder="Prioritas ke-{{ $i + 1 }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-red-400">
        @endfor

        <label class="block text-sm text-gray-700 font-semibold mt-8 mb-3">5 metric / score</label>
        <div class="border border-gray-200 rounded-xl overflow-hidden mb-8">
            <div class="grid grid-cols-2 px-5 py-3 bg-gray-50 text-xs text-gray-400 font-medium">
                <div>METRIC</div>
                <div>TARGET</div>
            </div>
            @php
                $defaultMetrics = ['Total leads contacted tim', 'Rata-rata response rate tim', 'Total qualified leads tim', 'Total closing tim', 'Omzet tim'];
            @endphp
            @foreach ($defaultMetrics as $i => $namaDefault)
                <div class="grid grid-cols-2 gap-4 px-5 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }} items-center">
                    <input type="text" name="metric_nama[]" required value="{{ old('metric_nama.'.$i, $namaDefault) }}"
                           class="text-sm text-gray-800 border-0 focus:ring-0 p-0 bg-transparent w-full">
                    @if ($loop->last)
                        <input type="text" name="metric_target[]" value="{{ old('metric_target.'.$i, number_format($targetMingguan->nilai_target, 0, ',', '.')) }}"
                               class="text-sm text-gray-800 border-0 focus:ring-0 p-0 bg-transparent w-full">
                    @else
                        <input type="text" name="metric_target[]" value="{{ old('metric_target.'.$i) }}" placeholder="-"
                               class="text-sm text-gray-800 border-0 focus:ring-0 p-0 bg-transparent w-full">
                    @endif
                </div>
            @endforeach
        </div>

        <label class="block text-sm text-gray-700 font-semibold mb-2">Target angka</label>
        <input type="number" name="target" required step="0.01" value="{{ old('target', $targetMingguan->nilai_target) }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-red-400">

        <label class="block text-sm text-gray-700 font-semibold mb-2">Output / Deliverable</label>
        <textarea name="output_deliverable" required rows="3"
                  placeholder="Seluruh anggota tim submit weekly tepat waktu, omzet tercapai"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-red-400">{{ old('output_deliverable') }}</textarea>

        <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-3.5 rounded-lg transition">
            Simpan weekly commitment
        </button>
    </form>
@endsection