@extends('layouts.management')

@section('title', 'Trend & Performance')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Trend & Performance</h1>
    <p class="text-gray-500 mt-1 mb-8">Tren Bulanan Perusahaan Dan Rencana Perbaikan</p>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Trend achievement perusahaan</h2>
        <span class="text-sm text-gray-400">4 bulan terakhir</span>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-8 space-y-5">
        @foreach ($trend as $bulan)
            <div class="flex items-center gap-4">
                <div class="w-24 font-medium text-gray-800">{{ $bulan['label'] }}</div>
                <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full {{ $bulan['value'] !== null && $bulan['value'] < 80 ? 'bg-red-600' : 'bg-blue-600' }}"
                         style="width: {{ $bulan['value'] ?? 0 }}%"></div>
                </div>
                <div class="w-14 text-right font-semibold text-gray-900">
                    {{ $bulan['value'] !== null ? $bulan['value'].'%' : '-' }}
                </div>
            </div>
        @endforeach
    </div>

    @if ($insightText)
        <div class="bg-green-50 rounded-xl p-6 mb-10">
            <p class="text-sm text-gray-700">{{ $insightText }}</p>
        </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Rencana improvement</h2>
        <span class="text-sm text-gray-400">Berdasarkan Analysis & Improvement bulanan</span>
    </div>

    <div class="space-y-3">
        @forelse ($rencana as $item)
            <div class="flex items-center gap-4 bg-white border border-gray-200 rounded-xl px-5 py-4">
                <div class="w-8 h-8 rounded-full bg-green-600 shrink-0"></div>
                <div>
                    <p class="font-medium text-gray-900">{{ $item['judul'] }}</p>
                    <p class="text-sm text-gray-500">{{ $item['deskripsi'] }}</p>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-400">
                Belum ada rekomendasi improvement bulan ini.
            </div>
        @endforelse
    </div>
@endsection