@extends('layouts.hrd')

@section('title', 'Monitoring PIC')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Monitoring PIC</h1>
    <p class="text-gray-500 mt-1 mb-8">Seluruh karyawan, lintas divisi</p>

    <form method="GET" class="mb-6">
        <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Divisi</option>
            @foreach ($divisiList as $divisi)
                <option value="{{ $divisi->id }}" @selected(request('divisi_id') == $divisi->id)>{{ $divisi->nama }}</option>
            @endforeach
        </select>
    </form>

    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Nama</th>
                    <th class="py-3 pr-4">Divisi</th>
                    <th class="py-3 pr-4">KPI</th>
                    <th class="py-3 pr-4">Target</th>
                    <th class="py-3 pr-4">Actual</th>
                    <th class="py-3 pr-4">Achievement</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 pr-4"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawan as $row)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $row->user->nama }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->user->divisi->nama ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->kpi ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->target ? 'Rp'.number_format($row->target, 0, ',', '.') : '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->actual ? 'Rp'.number_format($row->actual, 0, ',', '.') : '-' }}</td>
                        <td class="py-3 pr-4">
                            @if ($row->achievement !== null)
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    {{ $row->achievement >= 100 ? 'bg-green-100 text-green-700' : ($row->achievement >= 80 ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') }}">
                                    {{ round($row->achievement) }}%
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $row->status === 'Lengkap' ? 'bg-green-100 text-green-700' : ($row->status === 'Progress saja' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') }}">
                                {{ $row->status }}
                            </span>
                        </td>
                        <td class="py-3 pr-4">
                            <a href="{{ route('hrd.monitoring-pic.show', $row->user) }}" class="inline-block px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50">
                                Lihat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-6 text-center text-gray-400">Belum ada karyawan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection