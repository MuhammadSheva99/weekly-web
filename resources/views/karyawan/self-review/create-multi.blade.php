@extends('layouts.karyawan')

@section('title', 'Self Review')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Self Review</h1>
    <p class="text-gray-500 mt-1 mb-8">Jumat - {{ now()->translatedFormat('d F Y') }}</p>

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="bg-amber-50 rounded-2xl p-6 mb-8">
        <p class="text-sm text-gray-500 mb-1">Big Goal</p>
        <p class="font-bold text-gray-900">{{ $commitments->first()->big_goal }}</p>
    </div>

    <form method="POST" action="{{ route('karyawan.self-review.store') }}">
        @csrf

        <label class="block text-sm text-gray-700 font-semibold mb-3">Actual (final) per KPI</label>
        <div class="border border-gray-200 rounded-xl overflow-hidden mb-8">
            <div class="grid grid-cols-4 px-5 py-3 bg-gray-50 text-xs text-gray-400 font-medium">
                <div>KPI</div>
                <div>Target</div>
                <div>Actual Rabu</div>
                <div>Actual Final</div>
            </div>
            @foreach ($commitments as $c)
                <div class="grid grid-cols-4 gap-4 px-5 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }} items-center">
                    <input type="hidden" name="weekly_commitment_ids[]" value="{{ $c->id }}">
                    <div class="text-sm font-medium text-gray-800">
                        {{ $c->targetMingguan?->targetBulanan?->kpi?->nama_kpi ?? 'Target Manual' }}
                    </div>
                    <div class="text-sm text-gray-600">{{ number_format($c->target, 0, ',', '.') }}</div>
                    <div class="text-sm text-gray-600">{{ number_format($c->weeklyProgress->actual_sementara ?? 0, 0, ',', '.') }}</div>
                    <input type="number" step="0.01" name="actual[{{ $c->id }}]" required
                           value="{{ old('actual.'.$c->id, $c->weeklyProgress->actual_sementara ?? '') }}"
                           class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
            @endforeach
        </div>

        @php
            $questions = [
                'apa_berhasil' => 'Apa yang berhasil?',
                'apa_gagal' => 'Apa yang gagal?',
                'kenapa_gagal' => 'Kenapa gagal?',
                'apa_beda' => 'Apa yang seharusnya dilakukan berbeda?',
                'improvement_depan' => 'Apa improvement minggu depan?',
                'kritik_diri' => 'Kalau saya jadi atasan, apa yang akan saya kritik dari pekerjaan saya minggu ini?',
            ];
        @endphp

        @foreach ($questions as $field => $label)
            <label class="block text-sm text-gray-700 font-semibold mb-2">{{ $label }}</label>
            <textarea name="{{ $field }}" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-amber-400">{{ old($field) }}</textarea>
        @endforeach

        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3.5 rounded-lg transition">
            Simpan self review ({{ $commitments->count() }} KPI)
        </button>
    </form>
@endsection