@extends('layouts.karyawan')

@section('title', 'History Weekly')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">History Weekly</h1>
    <p class="text-gray-500 mt-1 mb-8">Seluruh data weekly yang pernah disubmit</p>

    <form method="GET" class="flex gap-3 mb-8">
        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
               class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">

        <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Status</option>
            <option value="selesai" @selected($status === 'selesai')>Selesai</option>
            <option value="berjalan" @selected($status === 'berjalan')>Berjalan</option>
        </select>
    </form>

    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b uppercase text-xs">
                    <th class="py-3 pr-4">Minggu</th>
                    <th class="py-3 pr-4">Big Goal</th>
                    <th class="py-3 pr-4">Target</th>
                    <th class="py-3 pr-4">Actual</th>
                    <th class="py-3 pr-4">Achievement</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $r)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $r->minggu_label }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $r->big_goal }}</td>
                        <td class="py-3 pr-4 text-gray-600">Rp{{ number_format($r->target / 1000000, 0) }}jt</td>
                        <td class="py-3 pr-4 text-gray-600">
                            {{ $r->actual !== null ? 'Rp'.number_format($r->actual / 1000000, 0).'jt' : '—' }}
                        </td>
                        <td class="py-3 pr-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ ! $r->is_final ? 'bg-gray-100 text-gray-500' : ($r->achievement >= 100 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700') }}">
                                {{ $r->status_label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-gray-400">Belum ada data weekly untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection