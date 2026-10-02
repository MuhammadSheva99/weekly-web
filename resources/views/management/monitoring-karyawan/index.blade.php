@extends('layouts.management')

@section('title', 'Monitoring Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Monitoring Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-8">Monitoring Performa Kinerja Karyawan</p>

    <form method="GET" class="mb-8">
        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Divisi</label>
        <select name="divisi_id" onchange="this.form.submit()"
                class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm w-64">
            @foreach ($divisiList as $divisi)
                <option value="{{ $divisi->id }}" @selected($divisiId == $divisi->id)>{{ $divisi->nama }}</option>
            @endforeach
        </select>
    </form>

    <p class="text-xs text-gray-400 mb-4">Klik baris karyawan untuk lihat rincian tiap KPI (bobot &amp; kontribusi ke Achievement).</p>

    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4 pl-4">Nama</th>
                    <th class="py-3 pr-4">Divisi</th>
                    <th class="py-3 pr-4">Jumlah KPI</th>
                    <th class="py-3 pr-4">Achievement</th>
                    <th class="py-3 pr-4">Status</th>
                </tr>
            </thead>

            @forelse ($karyawan as $k)
                @php
                    $badgeClass = match ($k->status_label) {
                        'Sangat Baik' => 'bg-green-50 text-green-700',
                        'Baik' => 'bg-blue-50 text-blue-700',
                        'Cukup' => 'bg-amber-50 text-amber-700',
                        'Buruk' => 'bg-red-50 text-red-700',
                        default => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                {{-- Satu <tbody> per karyawan (HTML sah: satu <table> boleh punya
                     banyak <tbody>), supaya baris ringkas & baris detail bisa
                     berbagi state Alpine (x-data) yang sama. --}}
                <tbody x-data="{ open: false }" class="border-b">
                    <tr @click="open = !open" class="cursor-pointer hover:bg-gray-50">
                        <td class="py-3 pr-4 pl-4 font-medium text-gray-900">{{ $k->user->nama ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $k->user->divisi->nama ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $k->kpiList->count() }} KPI</td>
                        <td class="py-3 pr-4">
                            @if ($k->achievement_total !== null)
                                <span class="px-3 py-1 rounded text-xs font-semibold {{ $badgeClass }}">
                                    {{ rtrim(rtrim(number_format($k->achievement_total, 1), '0'), '.') }}%
                                </span>
                            @else
                                <span class="px-3 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-500">
                                    Data belum lengkap
                                </span>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            <span class="px-3 py-1 rounded text-xs font-semibold {{ $badgeClass }}">
                                {{ $k->status_label ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    <tr x-show="open" x-cloak>
                        <td colspan="5" class="pb-4 px-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="text-left text-gray-400">
                                            <th class="py-1.5 pr-4">Nama KPI</th>
                                            <th class="py-1.5 pr-4">Achievement</th>
                                            <th class="py-1.5 pr-4">Bobot</th>
                                            <th class="py-1.5 pr-4">Kontribusi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($k->kpiList as $kpi)
                                            <tr>
                                                <td class="py-1.5 pr-4 text-gray-700">{{ $kpi->nama_kpi }}</td>
                                                <td class="py-1.5 pr-4 text-gray-600">{{ rtrim(rtrim(number_format($kpi->achievement_pct, 1), '0'), '.') }}%</td>
                                                <td class="py-1.5 pr-4 text-gray-600">
                                                    {{ $kpi->bobot !== null ? rtrim(rtrim(number_format($kpi->bobot, 1), '0'), '.').'%' : '— (tidak ditemukan di Target Bulanan)' }}
                                                </td>
                                                <td class="py-1.5 pr-4 text-gray-600">
                                                    {{ $kpi->kontribusi !== null ? rtrim(rtrim(number_format($kpi->kontribusi, 1), '0'), '.') : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @empty
                <tbody>
                    <tr><td colspan="5" class="py-6 text-center text-gray-400">Belum ada data KPI untuk divisi ini bulan ini.</td></tr>
                </tbody>
            @endforelse
        </table>
    </div>
@endsection