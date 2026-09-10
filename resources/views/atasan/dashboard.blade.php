@extends('layouts.atasan')

@section('title', 'Dashboard Head')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Head</h1>
    <p class="text-gray-500 mt-1 mb-6">Tim {{ auth()->user()->divisi->nama ?? '-' }} - {{ now()->translatedFormat('d F Y') }}</p>

    @if ($anggotaMenurun->isNotEmpty())
        <div class="mb-8 px-5 py-4 bg-red-50 text-red-700 rounded-xl text-sm font-medium">
            {{ $anggotaMenurun->pluck('nama')->join(', ') }} menunjukkan penurunan achievement 3 minggu berturut turut. Perlu perhatian.
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
        <div class="bg-red-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Total anggota tim</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalAnggota }} orang</p>
        </div>
        <div class="bg-red-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Sudah submit minggu ini</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $sudahSubmit }} / {{ $totalAnggota }}</p>
        </div>
        <div class="bg-red-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Rata rata achievement</p>
            <p class="text-2xl font-bold text-orange-700 mt-1">{{ $rataRataAchievement !== null ? $rataRataAchievement.'%' : '-' }}</p>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Status anggota tim</h2>
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        @forelse ($statusTim as $s)
            <div class="flex items-center gap-6 px-6 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <div class="flex items-center gap-3 w-40 shrink-0">
                    <div class="w-9 h-9 rounded-full bg-red-100 text-red-700 flex items-center justify-center font-semibold text-sm">
                        {{ strtoupper(substr($s->user->nama, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $s->user->nama }}</p>
                        <p class="text-xs text-gray-400">{{ $s->kpi ?? '-' }}</p>
                    </div>
                </div>

                <div class="w-24">
                    <p class="text-xs text-gray-400">TARGET</p>
                    <p class="font-semibold text-gray-800 text-sm">{{ $s->target ? 'Rp'.number_format($s->target, 0, ',', '.') : '-' }}</p>
                </div>
                <div class="w-24">
                    <p class="text-xs text-gray-400">ACTUAL</p>
                    <p class="font-semibold text-gray-800 text-sm">{{ $s->actual ? 'Rp'.number_format($s->actual, 0, ',', '.') : '-' }}</p>
                </div>

                <div class="w-16">
                    @if ($s->achievement !== null)
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $s->achievement >= 100 ? 'bg-green-100 text-green-700' : ($s->achievement >= 80 ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') }}">
                            {{ $s->achievement }}%
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">-</span>
                    @endif
                </div>

                <div class="flex gap-1.5">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold {{ $s->submitSenin ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">S</span>
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold {{ $s->submitRabu ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">R</span>
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold {{ $s->submitJumat ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-400' }}">J</span>
                </div>

                <div class="ml-auto">
                    <button type="button" disabled class="px-4 py-1.5 border border-gray-200 text-gray-400 rounded-lg text-sm cursor-not-allowed">
                        Detail
                    </button>
                </div>
            </div>
        @empty
            <div class="px-6 py-10 text-center text-gray-400">Belum ada anggota tim.</div>
        @endforelse
    </div>
@endsection