@extends('layouts.karyawan')

@section('title', 'Weekly Commitment')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Weekly Commitment</h1>
    <p class="text-gray-500 mt-1 mb-2">Senin - Minggu {{ $mingguKe }}, {{ now()->translatedFormat('F Y') }} - KPI {{ $targetMingguan->targetBulanan->kpi->nama_kpi }}</p>
    <p class="text-sm text-green-600 mb-8">Sudah disubmit — kamu masih bisa mengubahnya di bawah ini.</p>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-amber-50 rounded-2xl p-6 mb-2">
        <p class="text-sm text-gray-500">Linked KPI</p>
        <p class="font-bold text-gray-900 mt-1">
            {{ $targetMingguan->targetBulanan->kpi->nama_kpi }} · target minggu ini Rp {{ number_format($targetMingguan->nilai_target, 0, ',', '.') }}
        </p>
    </div>
    <p class="text-sm text-gray-400 mb-8">Wajib disubmit sebelum Senin 23:59</p>

    <form method="POST" action="{{ route('karyawan.weekly-commitment.update', $commitment) }}">
        @csrf
        @method('PUT')

        <label class="block text-sm text-gray-500 mb-2">Big goal</label>
        <input type="text" name="big_goal" required value="{{ old('big_goal', $commitment->big_goal) }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-amber-400">

        <label class="block text-sm text-gray-700 font-semibold mb-3">3 prioritas</label>
        @for ($i = 0; $i < 3; $i++)
            <input type="text" name="prioritas[]" required value="{{ old('prioritas.'.$i, $commitment->prioritas[$i] ?? '') }}"
                   placeholder="Prioritas ke-{{ $i + 1 }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-amber-400">
        @endfor

        <label class="block text-sm text-gray-700 font-semibold mt-8 mb-3">5 metric / score</label>
        <div class="border border-gray-200 rounded-xl overflow-hidden mb-8">
            <div class="grid grid-cols-2 px-5 py-3 bg-gray-50 text-xs text-gray-400 font-medium">
                <div>METRIC</div>
                <div>TARGET</div>
            </div>
            @foreach ($commitment->metric as $i => $item)
                <div class="grid grid-cols-2 gap-4 px-5 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }} items-center">
                    <input type="text" name="metric_nama[]" required value="{{ old('metric_nama.'.$i, $item['nama_metric']) }}"
                           class="text-sm text-gray-800 border-0 focus:ring-0 p-0 bg-transparent w-full">
                    <input type="text" name="metric_target[]" value="{{ old('metric_target.'.$i, $item['target']) }}" placeholder="-"
                           class="text-sm text-gray-800 border-0 focus:ring-0 p-0 bg-transparent w-full">
                </div>
            @endforeach
        </div>

        <label class="block text-sm text-gray-700 font-semibold mb-2">Target angka</label>
        <input type="number" name="target" required step="0.01" value="{{ old('target', $commitment->target) }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-amber-400">

        <label class="block text-sm text-gray-700 font-semibold mb-2">Output / Deliverable</label>
        <textarea name="output_deliverable" required rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-amber-400">{{ old('output_deliverable', $commitment->output_deliverable) }}</textarea>

        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3.5 rounded-lg transition">
            Update weekly commitment
        </button>
    </form>
@endsection