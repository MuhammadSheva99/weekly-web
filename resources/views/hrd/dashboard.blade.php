@extends('layouts.hrd')

@section('title', 'Dashboard HRD')

@section('content')
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard HRD</h1>
            <p class="text-gray-500 mt-1">Seluruh divisi · {{ now()->translatedFormat('d F Y') }}</p>
        </div>

        <form method="GET">
            <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                <option value="">Semua Divisi</option>
                @foreach ($divisiList as $divisi)
                    <option value="{{ $divisi->id }}" @selected($divisiId == $divisi->id)>{{ $divisi->nama }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if ($repeatedUnderperform->isNotEmpty())
        <div class="mb-8 px-5 py-4 bg-red-50 text-red-700 rounded-xl text-sm font-medium">
            {{ $repeatedUnderperform->count() }} karyawan terdeteksi repeatedly underperform. Perlu tindak lanjut counseling.
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-10">
        <div class="bg-blue-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Total PIC</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalPic }} orang</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Sudah submit</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $sudahSubmit }} / {{ $totalPic }}</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Belum submit</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $belumSubmit }} / {{ $totalPic }}</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Rata-rata achievement</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $rataRataAchievement !== null ? $rataRataAchievement.'%' : '-' }}</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-10 space-y-1">
        @forelse ($achievementPerDivisi as $divisi)
            <a href="{{ route('hrd.divisi.show', $divisi) }}"
            class="flex items-center gap-4 py-3 px-2 -mx-2 rounded-lg hover:bg-gray-50 transition">
                <div class="w-28 font-medium text-gray-800">{{ $divisi->nama }}</div>
                <div class="w-16 text-sm text-gray-400">{{ $divisi->users->count() }} PIC</div>
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
        @if ($achievementPerDivisi->every(fn ($d) => $d->avg_achievement === null))
            <p class="text-xs text-gray-400 pt-2 border-t mt-2">Belum ada data KPI Performance bulan ini.</p>
        @endif
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Perlu perhatian</h2>
        <span class="text-sm text-gray-400">Repeatedly underperform</span>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        @forelse ($repeatedUnderperform as $user)
            <div class="flex items-center gap-4">
                <div class="w-40 font-medium text-gray-800">{{ $user->nama }} · {{ $user->divisi->nama ?? '-' }}</div>
                <div class="w-28 text-sm text-gray-400">3 minggu turun</div>
                <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-red-600" style="width: 45%"></div>
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-sm">Belum ada karyawan yang terdeteksi repeatedly underperform.</p>
        @endforelse
    </div>
@endsection