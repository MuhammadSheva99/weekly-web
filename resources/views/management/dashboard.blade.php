@extends('layouts.management')

@section('title', 'Dashboard Management')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Management</h1>
    <p class="text-gray-500 mt-1 mb-8">Ringkasan Perusahaan · {{ now()->translatedFormat('F Y') }}</p>

    <div class="bg-green-50 rounded-2xl p-8 mb-8">
        <p class="text-sm text-gray-600">Performance perusahaan (rata-rata seluruh divisi)</p>
        <p class="text-5xl font-bold text-gray-800 mt-2">{{ $companyAchievement !== null ? $companyAchievement.'%' : '-' }}</p>
        <p class="text-sm text-gray-500 mt-2">Achievement bulan berjalan terhadap target</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
        <div class="bg-green-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Total karyawan</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalKaryawan }} orang</p>
        </div>
        <div class="bg-green-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Total divisi</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalDivisi }} Divisi</p>
        </div>
        <div class="bg-green-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Perlu perhatian</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $perluPerhatianCount }} Orang</p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Performance per Divisi (ringkas)</h2>
        <span class="text-sm text-gray-400">Bulan berjalan</span>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-10 space-y-1">
        @forelse ($achievementPerDivisi as $divisi)
            <a href="{{ route('management.divisi.show', $divisi) }}"
               class="flex items-center gap-4 py-3 px-2 -mx-2 rounded-lg hover:bg-gray-50 transition">
                <div class="w-28 font-medium text-gray-800">{{ $divisi->nama }}</div>
                <div class="w-16 text-sm text-gray-400">{{ $divisi->users_count }} orang</div>
                <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full {{ $divisi->avg_achievement !== null && $divisi->avg_achievement < 80 ? 'bg-red-600' : 'bg-blue-600' }}"
                         style="width: {{ $divisi->avg_achievement ?? 0 }}%"></div>
                </div>
                <div class="w-14 text-right font-semibold text-gray-900">
                    {{ $divisi->avg_achievement !== null ? $divisi->avg_achievement.'%' : '-' }}
                </div>
            </a>
        @empty
            <p class="text-gray-400 text-sm">Belum ada divisi.</p>
        @endforelse
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Sorotan PIC</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
        <div class="bg-green-50 rounded-xl p-5">
            <p class="text-sm text-green-700 font-medium">Top performer</p>
            @if ($topPerformer)
                <p class="text-lg font-bold text-gray-900 mt-1">{{ $topPerformer->user->nama }} - {{ $topPerformer->user->divisi->nama ?? '-' }}</p>
                <p class="text-sm text-gray-500 mt-1">Achievement {{ round($topPerformer->achievement_pct) }}%</p>
            @else
                <p class="text-gray-400 mt-1">Belum ada data.</p>
            @endif
        </div>
        <div class="bg-red-50 rounded-xl p-5">
            <p class="text-sm text-red-600 font-medium">Perlu perhatian</p>
            @if ($lowestPerformer)
                <p class="text-lg font-bold text-gray-900 mt-1">{{ $lowestPerformer->user->nama }} - {{ $lowestPerformer->user->divisi->nama ?? '-' }}</p>
                <p class="text-sm text-gray-500 mt-1">Achievement {{ round($lowestPerformer->achievement_pct) }}%</p>
            @else
                <p class="text-gray-400 mt-1">Belum ada data.</p>
            @endif
        </div>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">KPI achievement - target vs actual bulanan</h2>
    <div class="bg-white rounded-2xl overflow-hidden mb-8">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Divisi</th>
                    <th class="py-3 pr-4">Total PIC</th>
                    <th class="py-3 pr-4">Achievement</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($achievementPerDivisi as $divisi)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $divisi->nama }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $divisi->users_count }}</td>
                        <td class="py-3 pr-4">
                            @if ($divisi->avg_achievement !== null)
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $divisi->avg_achievement >= 80 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $divisi->avg_achievement }}%
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-6 text-center text-gray-400">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($perluPerhatianCount > 0)
        <div class="flex items-center justify-between px-6 py-4 bg-green-50 rounded-xl">
            <p class="text-sm text-gray-700">{{ $perluPerhatianCount }} karyawan terdeteksi repeatedly underperform.</p>
            <a href="{{ route('management.underperform') }}" class="px-4 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50">
                Lihat detail
            </a>
        </div>
    @endif
@endsection