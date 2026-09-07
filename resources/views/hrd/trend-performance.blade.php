@extends('layouts.hrd')

@section('title', 'Trend Performance')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Trend performance</h1>
    <p class="text-gray-500 mt-1 mb-8">Rata-rata achievement per divisi</p>

    <form method="GET" class="flex gap-3 mb-8">
        <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            @foreach ($divisiList as $divisi)
                <option value="{{ $divisi->id }}" @selected($selectedDivisiId == $divisi->id)>{{ $divisi->nama }}</option>
            @endforeach
        </select>

        <select name="range" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <option value="3" @selected($rangeBulan == 3)>3 bulan terakhir</option>
            <option value="6" @selected($rangeBulan == 6)>6 bulan terakhir</option>
            <option value="12" @selected($rangeBulan == 12)>12 bulan terakhir</option>
        </select>
    </form>

    @if ($selectedDivisi)
        <div class="bg-blue-50 rounded-2xl p-6 mb-10 inline-block">
            <p class="text-sm text-gray-500">Rata-rata achievement · {{ $selectedDivisi->nama }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $avgAchievement !== null ? $avgAchievement.'%' : '-' }}</p>
        </div>

        <h2 class="text-lg font-semibold text-gray-800 mb-4">Trend bulanan</h2>

        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            @foreach ($monthlyTrend as $month)
                <div class="flex items-center gap-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="w-20 font-medium text-gray-800">{{ $month['label'] }}</div>
                    <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $month['value'] !== null && $month['value'] < 80 ? 'bg-red-600' : 'bg-blue-600' }}"
                             style="width: {{ $month['value'] ?? 0 }}%"></div>
                    </div>
                    <div class="w-14 text-right font-semibold text-gray-900">
                        {{ $month['value'] !== null ? $month['value'].'%' : '-' }}
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-400">Belum ada divisi yang bisa ditampilkan.</p>
    @endif
@endsection