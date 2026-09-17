@extends('layouts.karyawan')

@section('title', 'Trend Performance')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Trend Performance</h1>
    <p class="text-gray-500 mt-1 mb-8">Achievement bulanan - 4 bulan terakhir</p>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-8 space-y-5">
        @foreach ($trend as $bulan)
            <div class="flex items-center gap-4">
                <div class="w-16 font-medium text-gray-800">{{ $bulan['label'] }}</div>
                <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full {{ $bulan['value'] !== null && $bulan['value'] < 80 ? 'bg-orange-800' : 'bg-blue-600' }}"
                         style="width: {{ $bulan['value'] ?? 0 }}%"></div>
                </div>
                <div class="w-14 text-right font-semibold text-gray-900">
                    {{ $bulan['value'] !== null ? $bulan['value'].'%' : '-' }}
                </div>
            </div>
        @endforeach
    </div>

    @if ($insightText)
        <div class="bg-amber-50 rounded-xl p-6">
            <p class="text-sm text-amber-800">{{ $insightText }}</p>
        </div>
    @endif
@endsection