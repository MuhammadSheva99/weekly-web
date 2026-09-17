@extends('layouts.karyawan')

@section('title', 'Weekly Commitment')

@section('content')
    @php $first = $commitments->first(); @endphp

    <h1 class="text-3xl font-bold text-gray-900">Weekly Commitment</h1>
    <p class="text-gray-500 mt-1 mb-2">Senin - Minggu {{ $mingguKe }}, {{ now()->translatedFormat('F Y') }}</p>
    <p class="text-sm text-green-600 mb-8">Sudah disubmit — kamu masih bisa mengubah Big Goal/Prioritas/Output di bawah ini.</p>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="bg-amber-50 rounded-2xl p-6 mb-8">
        <p class="text-sm text-gray-500 mb-3">KPI kamu minggu ini ({{ $commitments->count() }} indikator)</p>
        <div class="space-y-2">
            @foreach ($commitments as $c)
                <div class="flex items-center justify-between text-sm bg-white rounded-lg px-4 py-2.5">
                    <span class="font-medium text-gray-800">{{ $c->targetMingguan->targetBulanan->kpi->nama_kpi }}</span>
                    <span class="text-gray-600">Target: {{ number_format($c->target, 0, ',', '.') }} {{ $c->targetMingguan->targetBulanan->kpi->satuan }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('karyawan.weekly-commitment.update', $first) }}">
        @csrf
        @method('PUT')

        <label class="block text-sm text-gray-500 mb-2">Big goal</label>
        <input type="text" name="big_goal" required value="{{ old('big_goal', $first->big_goal) }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-amber-400">

        <label class="block text-sm text-gray-700 font-semibold mb-3">3 prioritas</label>
        @for ($i = 0; $i < 3; $i++)
            <input type="text" name="prioritas[]" required value="{{ old('prioritas.'.$i, $first->prioritas[$i] ?? '') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-amber-400">
        @endfor

        <label class="block text-sm text-gray-700 font-semibold mt-8 mb-2">Output / Deliverable</label>
        <textarea name="output_deliverable" required rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-amber-400">{{ old('output_deliverable', $first->output_deliverable) }}</textarea>

        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3.5 rounded-lg transition">
            Update weekly commitment
        </button>
    </form>
@endsection