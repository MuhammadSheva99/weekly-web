{{--
    Daftar orang + KPI-nya (klik baris untuk membuka tabel KPI lengkap).
    Param: $daftar, $periode, $cari, opsional $divisiList & $divisiId (filter divisi)
--}}
@php
    $fmt = fn ($n, $desimal = 1) => rtrim(rtrim(number_format((float) $n, $desimal, ',', '.'), '0'), ',');
    $badgeStatus = fn ($s) => match ($s) {
        'Sangat Baik' => 'bg-green-50 text-green-700',
        'Baik' => 'bg-blue-50 text-blue-700',
        'Cukup' => 'bg-amber-50 text-amber-700',
        'Buruk' => 'bg-red-50 text-red-700',
        default => 'bg-gray-100 text-gray-500',
    };
@endphp

<form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
    @isset($divisiList)
        <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Divisi</option>
            @foreach ($divisiList as $divisi)
                <option value="{{ $divisi->id }}" @selected(($divisiId ?? null) == $divisi->id)>{{ $divisi->nama }}</option>
            @endforeach
        </select>
    @endisset
    <div class="flex items-center gap-2">
        <label class="text-sm text-gray-500">Periode</label>
        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
               class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
    </div>
    <input type="text" name="q" value="{{ $cari }}" placeholder="Cari nama..."
           class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm w-56">
    <button type="submit" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">Cari</button>
</form>

<p class="text-xs text-gray-400 mb-4">Klik baris untuk melihat tabel KPI lengkap (target, realisasi, pencapaian, skor).</p>

<div class="bg-white rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-500 border-b">
                <th class="py-3 pr-4 pl-4">Nama</th>
                <th class="py-3 pr-4">Divisi</th>
                <th class="py-3 pr-4">Jumlah KPI</th>
                <th class="py-3 pr-4">Terisi</th>
                <th class="py-3 pr-4">Skor</th>
                <th class="py-3 pr-4">Status</th>
            </tr>
        </thead>

        @forelse ($daftar as $d)
            <tbody x-data="{ open: false }" class="border-b">
                <tr @click="open = !open" class="cursor-pointer hover:bg-gray-50">
                    <td class="py-3 pr-4 pl-4 font-medium text-gray-900">{{ $d->user->nama ?? '-' }}</td>
                    <td class="py-3 pr-4 text-gray-600">{{ $d->user->divisi->nama ?? '-' }}</td>
                    <td class="py-3 pr-4 text-gray-600">{{ $d->jumlah }} KPI</td>
                    <td class="py-3 pr-4 text-gray-600">{{ $d->terisi }}/{{ $d->jumlah }}</td>
                    <td class="py-3 pr-4">
                        @if ($d->skor_total !== null)
                            <span class="px-3 py-1 rounded text-xs font-semibold {{ $badgeStatus($d->status) }}">{{ $fmt($d->skor_total) }}%</span>
                        @else
                            <span class="px-3 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-500">Belum ada data</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <span class="px-3 py-1 rounded text-xs font-semibold {{ $badgeStatus($d->status) }}">{{ $d->status ?? '-' }}</span>
                    </td>
                </tr>
                <tr x-show="open" x-cloak>
                    <td colspan="6" class="pb-4 px-4">
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            @include('partials.kpi.tabel', ['d' => $d])
                        </div>
                    </td>
                </tr>
            </tbody>
        @empty
            <tbody>
                <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada karyawan dengan KPI pada periode ini.</td></tr>
            </tbody>
        @endforelse
    </table>
</div>