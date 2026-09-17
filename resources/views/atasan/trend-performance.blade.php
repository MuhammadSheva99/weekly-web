@extends('layouts.atasan')

@section('title', 'Trend Performance Tim')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Trend Performance Tim</h1>
    <p class="text-gray-500 mt-1 mb-8">Rata-rata achievement per anggota - 4 minggu terakhir</p>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-8 space-y-5">
        @forelse ($rataRata as $r)
            <a href="{{ route('atasan.trend-performance.index', ['user' => $r->user->id]) }}"
               class="flex items-center gap-4 py-1 -mx-1 px-1 rounded-lg hover:bg-gray-50 transition">
                <div class="w-20 font-medium text-gray-800">{{ $r->user->nama }}</div>
                <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full {{ $r->achievement !== null && $r->achievement < 80 ? 'bg-orange-800' : 'bg-blue-600' }}"
                         style="width: {{ $r->achievement ?? 0 }}%"></div>
                </div>
                <div class="w-14 text-right font-semibold text-gray-900">
                    {{ $r->achievement !== null ? $r->achievement.'%' : '-' }}
                </div>
            </a>
        @empty
            <p class="text-gray-400 text-sm">Belum ada anggota tim.</p>
        @endforelse
    </div>

    @if ($selectedUser)
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Riwayat achievement {{ $selectedUser->nama }}</h2>
        <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            @forelse ($riwayat as $r)
                <div class="flex items-center gap-4">
                    <div class="w-20 font-medium text-gray-800">{{ $r->label }}</div>
                    <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $r->value !== null && $r->value < 80 ? 'bg-orange-800' : 'bg-blue-600' }}"
                             style="width: {{ $r->value ?? 0 }}%"></div>
                    </div>
                    <div class="w-14 text-right font-semibold text-gray-900">
                        {{ $r->value !== null ? $r->value.'%' : '-' }}
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm">Belum ada riwayat weekly.</p>
            @endforelse
        </div>
    @endif
@endsection