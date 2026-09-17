@extends('layouts.atasan')

@section('title', 'Monitoring Tim')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Monitoring Tim</h1>
    <p class="text-gray-500 mt-1 mb-8">Seluruh data weekly anggota tim</p>

    <div class="flex gap-3 mb-8">
        <div class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-600">
            {{ $mingguLabel }}
        </div>
        <form method="GET">
            <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                <option value="">Semua status</option>
                <option value="Lengkap" @selected($status === 'Lengkap')>Lengkap</option>
                <option value="Progress saja" @selected($status === 'Progress saja')>Progress saja</option>
                <option value="Belum Submit" @selected($status === 'Belum Submit')>Belum Submit</option>
            </select>
        </form>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b uppercase text-xs">
                    <th class="py-3 pr-4">Nama</th>
                    <th class="py-3 pr-4">Big Goal</th>
                    <th class="py-3 pr-4">Target</th>
                    <th class="py-3 pr-4">Actual</th>
                    <th class="py-3 pr-4">Achievement</th>
                    <th class="py-3 pr-4">Status Submit</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $row)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $row->user->nama }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->big_goal ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->target ? 'Rp'.number_format($row->target / 1000000, 0).'jt' : '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->actual ? 'Rp '.number_format($row->actual / 1000000, 0).'jt' : '-' }}</td>
                        <td class="py-3 pr-4">
                            @if ($row->achievement !== null)
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $row->achievement >= 100 ? 'bg-green-100 text-green-700' : ($row->achievement >= 80 ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $row->achievement }}%
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $row->status_submit === 'Lengkap' ? 'bg-green-100 text-green-700' : ($row->status_submit === 'Progress saja' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') }}">
                                {{ $row->status_submit }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada anggota tim.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection