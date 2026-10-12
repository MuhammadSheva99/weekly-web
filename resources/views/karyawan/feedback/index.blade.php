@extends('layouts.karyawan')

@section('title', 'Feedback')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Feedback</h1>
    <p class="text-gray-500 mt-1 mb-6">Komentar dari Atasan dan HRD pada weekly kamu.</p>

    @forelse ($feedbackMingguan as $minggu)
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-gray-800">{{ $minggu->label }}</h2>
                <a href="{{ route('karyawan.feedback.show', $minggu->key) }}"
                   style="font-size:14px;font-weight:600;color:#b45309;text-decoration:none;">Lihat detail &rarr;</a>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                @foreach ($minggu->komentar as $k)
                    <div class="px-6 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $k->pemberi }}
                                @if ($k->peran)
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">{{ $k->peran }}</span>
                                @endif
                            </p>
                            <span class="text-xs text-gray-400">{{ $k->waktu->translatedFormat('d M Y, H:i') }}</span>
                        </div>

                        @if ($k->kpi)
                            <p class="text-xs text-gray-500 mt-1">KPI: {{ $k->kpi }}</p>
                        @endif

                        <p class="text-gray-700 mt-2 whitespace-pre-line">{{ $k->isi }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white border border-gray-200 rounded-xl px-6 py-10 text-center text-gray-400">
            Belum ada feedback dari Atasan atau HRD.
        </div>
    @endforelse
@endsection