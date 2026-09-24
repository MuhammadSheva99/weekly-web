@extends('layouts.atasan')

@section('title', 'Weekly Commitment')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Weekly Commitment</h1>
    <p class="text-gray-500 mt-1 mb-8">Senin - Minggu {{ $mingguKe }}, {{ now()->translatedFormat('F Y') }}</p>

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-red-50 rounded-2xl p-6 mb-8">
        <p class="text-sm text-gray-500 mb-3">KPI kamu minggu ini ({{ $targetMingguanList->count() }} indikator)</p>
        <div class="space-y-2">
            @foreach ($targetMingguanList as $tm)
                <div class="flex items-center justify-between text-sm bg-white rounded-lg px-4 py-2.5">
                    <span class="font-medium text-gray-800">{{ $tm->targetBulanan->kpi->nama_kpi }}</span>
                    <span class="text-gray-600">Target: {{ number_format($tm->nilai_target, 0, ',', '.') }} {{ $tm->targetBulanan->kpi->satuan }}</span>
                </div>
            @endforeach
        </div>
    </div>
    <p class="text-sm text-gray-400 mb-8">Wajib disubmit sebelum Senin 23:59</p>

    <form method="POST" action="{{ route('atasan.weekly-commitment.store') }}">
        @csrf

        @foreach ($targetMingguanList as $tm)
            <input type="hidden" name="target_mingguan_ids[]" value="{{ $tm->id }}">
        @endforeach

        <label class="block text-sm text-gray-500 mb-2">Big goal</label>
        <input type="text" name="big_goal" required value="{{ old('big_goal') }}"
               placeholder="Contoh: Mencapai seluruh target KPI minggu ini"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-red-400">

        <label class="block text-sm text-gray-700 font-semibold mb-3">3 prioritas</label>
        @for ($i = 0; $i < 3; $i++)
            <input type="text" name="prioritas[]" required value="{{ old('prioritas.'.$i) }}"
                   placeholder="Prioritas ke-{{ $i + 1 }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-red-400">
        @endfor

        <label class="block text-sm text-gray-700 font-semibold mt-8 mb-2">Output / Deliverable</label>
        <textarea name="output_deliverable" required rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-red-400">{{ old('output_deliverable') }}</textarea>

        <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-3.5 rounded-lg transition">
            Simpan weekly commitment ({{ $targetMingguanList->count() }} KPI)
        </button>
    </form>
@endsection