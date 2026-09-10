@extends('layouts.management')

@section('title', 'Underperform')

@section('content')
    <a href="{{ route('management.dashboard') }}" class="text-sm text-gray-500 hover:underline">← Kembali ke Dashboard</a>

    <h1 class="text-3xl font-bold text-gray-900 mt-3">Karyawan Perlu Perhatian</h1>
    <p class="text-gray-500 mt-1 mb-8">Karyawan dengan penurunan achievement berturut-turut</p>

    <div class="space-y-6">
        @forelse ($data as $item)
            <div class="bg-white border border-gray-200 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-semibold">
                            {{ strtoupper(substr($item->user->nama, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $item->user->nama }}</p>
                            <p class="text-sm text-gray-500">{{ $item->user->divisi->nama ?? '-' }} · {{ $item->kpi }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-red-50 text-red-600 text-sm font-medium rounded-full">
                        {{ $item->weeks->count() }} minggu turun
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach ($item->weeks as $i => $achievement)
                        <div class="flex items-center gap-4">
                            <div class="w-20 text-gray-600">Minggu {{ $i + 1 }}</div>
                            <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-red-600" style="width: {{ min($achievement, 100) }}%"></div>
                            </div>
                            <div class="w-14 text-right font-semibold text-gray-900">{{ round($achievement) }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Belum ada karyawan dengan tren penurunan achievement.
            </div>
        @endforelse
    </div>
@endsection