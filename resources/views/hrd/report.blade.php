@extends('layouts.hrd')

@section('title', 'Report & Export')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Report & export</h1>
    <p class="text-gray-500 mt-1 mb-8">Unduh laporan performance untuk kebutuhan internal</p>

    <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter laporan</h2>

        <form method="GET" class="flex flex-wrap gap-3">
            <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                <option value="">Semua Divisi</option>
                @foreach ($divisiList as $divisi)
                    <option value="{{ $divisi->id }}" @selected($divisiId == $divisi->id)>{{ $divisi->nama }}</option>
                @endforeach
            </select>

            <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
                   class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">

            <select name="tipe" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                <option value="weekly" @selected($tipe === 'weekly')>Weekly Report</option>
                <option value="monthly" @selected($tipe === 'monthly')>Monthly Report</option>
            </select>
        </form>
    </div>

    <div class="flex gap-4 mb-8">
        <a href="{{ route('hrd.report.export-excel', ['divisi_id' => $divisiId, 'periode' => $periode]) }}"
           class="px-6 py-2.5 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50">
            Export Excel
        </a>
        <a href="{{ route('hrd.report.export-pdf', ['divisi_id' => $divisiId, 'periode' => $periode]) }}"
           class="px-6 py-2.5 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50">
            Export PDF
        </a>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Pratinjau Laporan</h2>
    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Divisi</th>
                    <th class="py-3 pr-4">Total PIC</th>
                    <th class="py-3 pr-4">Target bulanan</th>
                    <th class="py-3 pr-4">Actual bulanan</th>
                    <th class="py-3 pr-4">Achievement</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $row->divisi }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->total_pic }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->target ? number_format($row->target, 0, ',', '.') : '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->actual ? number_format($row->actual, 0, ',', '.') : '-' }}</td>
                        <td class="py-3 pr-4">
                            @if ($row->achievement !== null)
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $row->achievement >= 80 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $row->achievement }}%
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-gray-400">Belum ada data untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection