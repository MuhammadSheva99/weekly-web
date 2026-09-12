@extends('layouts.karyawan')

@section('title', 'Self Review')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Self Review</h1>
    <p class="text-gray-500 mt-1 mb-2">Jumat - {{ now()->translatedFormat('d F Y') }}</p>
    <p class="text-sm text-green-600 mb-8">Sudah disubmit — kamu masih bisa mengubahnya di bawah ini.</p>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="bg-amber-50 rounded-2xl p-6 mb-8">
        <p class="text-sm text-gray-500">Target minggu ini</p>
        <p class="font-bold text-gray-900 mt-1">{{ number_format($commitment->target, 0, ',', '.') }}</p>
    </div>

    <form method="POST" action="{{ route('karyawan.self-review.update', $review) }}">
        @csrf
        @method('PUT')

        <label class="block text-sm text-gray-700 font-semibold mb-2">Actual (final)</label>
        <input type="number" step="0.01" name="actual" required value="{{ old('actual', $review->actual) }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-amber-400">

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
            <textarea name="{{ $field }}" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-amber-400">{{ old($field, $review->$field) }}</textarea>
        @endforeach

        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3.5 rounded-lg transition">
            Update self review
        </button>
    </form>
@endsection