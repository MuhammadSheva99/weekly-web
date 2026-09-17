@extends('layouts.atasan')

@section('title', 'Detail & Feedback')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Detail & feedback</h1>
    <p class="text-gray-500 mt-1 mb-6">Meninjau weekly milik anggota tim</p>

    <div class="flex gap-3 mb-8 flex-wrap">
        @forelse ($anggotaTim as $anggota)
            <a href="{{ route('atasan.feedback.index', ['user' => $anggota->id]) }}"
               class="flex items-center gap-2 px-4 py-2 rounded-lg border {{ $pic && $pic->id === $anggota->id ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <span class="w-7 h-7 rounded-full bg-red-700 text-white flex items-center justify-center text-xs font-semibold">
                    {{ strtoupper(substr($anggota->nama, 0, 2)) }}
                </span>
                <span class="text-sm text-gray-800">{{ $anggota->nama }}</span>
            </a>
        @empty
            <p class="text-gray-400 text-sm">Belum ada anggota tim.</p>
        @endforelse
    </div>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    @if (! $pic)
        {{-- Tidak ada anggota, tidak perlu tampilkan apa-apa lagi --}}
    @elseif (! $commitment)
        <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
            Belum ada Weekly Commitment minggu ini dari {{ $pic->nama }}.
        </div>
    @else
        <div class="bg-red-50 rounded-2xl p-6 mb-6">
            <p class="text-xs uppercase tracking-wide text-gray-400 mb-4">Weekly Commitment - {{ $pic->nama }}</p>

            <p class="text-sm font-semibold text-gray-700 mb-1">Big Goal</p>
            <p class="text-sm text-gray-600 mb-4">{{ $commitment->big_goal }}</p>

            <p class="text-sm font-semibold text-gray-700 mb-1">Target</p>
            <p class="text-sm text-gray-600">{{ number_format($commitment->target, 0, ',', '.') }}</p>
        </div>

        @if ($commitment->weeklyProgress)
            <div class="bg-red-50 rounded-2xl p-6 mb-6">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-4">Weekly Progress - Rabu</p>

                <p class="text-sm font-semibold text-gray-700 mb-1">Actual sementara</p>
                <p class="text-sm text-gray-600 mb-4">
                    {{ number_format($commitment->weeklyProgress->actual_sementara, 0, ',', '.') }}
                    ({{ round($commitment->weeklyProgress->achievement_pct) }}%)
                </p>

                <p class="text-sm font-semibold text-gray-700 mb-1">Problem / kendala</p>
                <p class="text-sm text-gray-600 mb-4">{{ $commitment->weeklyProgress->problem ?? '-' }}</p>

                <p class="text-sm font-semibold text-gray-700 mb-1">Analysis</p>
                <p class="text-sm text-gray-600 mb-4">{{ $commitment->weeklyProgress->analysis ?? '-' }}</p>

                <p class="text-sm font-semibold text-gray-700 mb-1">Solution</p>
                <p class="text-sm text-gray-600 mb-4">{{ $commitment->weeklyProgress->solution ?? '-' }}</p>

                <p class="text-sm font-semibold text-gray-700 mb-1">Action plan</p>
                <p class="text-sm text-gray-600">{{ $commitment->weeklyProgress->action_plan ?? '-' }}</p>
            </div>
        @endif

        @if ($commitment->selfReview)
            <div class="bg-red-50 rounded-2xl p-6 mb-6">
                <p class="text-xs uppercase tracking-wide text-gray-400 mb-4">Self Review - Jumat</p>
                <p class="text-sm font-semibold text-gray-700 mb-1">Apa yang berhasil</p>
                <p class="text-sm text-gray-600 mb-4">{{ $commitment->selfReview->apa_berhasil ?? '-' }}</p>
                <p class="text-sm font-semibold text-gray-700 mb-1">Apa yang gagal</p>
                <p class="text-sm text-gray-600 mb-4">{{ $commitment->selfReview->apa_gagal ?? '-' }}</p>
                <p class="text-sm font-semibold text-gray-700 mb-1">Improvement minggu depan</p>
                <p class="text-sm text-gray-600">{{ $commitment->selfReview->improvement_depan ?? '-' }}</p>
            </div>
        @endif

        @if ($commitment->approvalComments->isNotEmpty())
            <div class="space-y-3 mb-6">
                @foreach ($commitment->approvalComments as $c)
                    <div class="bg-white border border-gray-200 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $c->commentedBy->nama }}</p>
                            <p class="text-xs text-gray-400">{{ $c->created_at->diffForHumans() }}</p>
                        </div>
                        <p class="text-sm text-gray-600">{{ $c->comment }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <h2 class="text-lg font-semibold text-gray-800 mb-4">Komentar / feedback untuk {{ $pic->nama }}</h2>
        <form method="POST" action="{{ route('atasan.feedback.store', $pic) }}">
            @csrf
            <input type="hidden" name="weekly_commitment_id" value="{{ $commitment->id }}">
            <textarea name="comment" rows="4" required placeholder="Tulisan feedback, misalnya arahan untuk sisa minggu ini"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-red-400"></textarea>
            <button type="submit" class="px-6 py-2.5 bg-red-700 hover:bg-red-800 text-white font-semibold rounded-lg text-sm transition">
                Kirim Komentar
            </button>
        </form>
    @endif
@endsection