@extends('layouts.hrd')

@section('title', 'Riwayat Pengajuan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Cuti</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan saldo cuti - Tahun {{ now()->year }}</p>

    @include('hrd.cuti._tabs')

    <form method="GET" class="flex gap-3 mb-6">
        <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            @for ($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}" @selected(request('tahun') == $y)>{{ $y }}</option>
            @endfor
        </select>
        <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Status</option>
            <option value="menunggu" @selected(request('status') === 'menunggu')>Menunggu</option>
            <option value="disetujui" @selected(request('status') === 'disetujui')>Disetujui</option>
            <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
        </select>
    </form>

    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Jenis cuti</th>
                    <th class="py-3 pr-4">Tanggal</th>
                    <th class="py-3 pr-4">Jumlah hari</th>
                    <th class="py-3 pr-4">Disetujui oleh</th>
                    <th class="py-3 pr-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $c)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $c->jenis_cuti }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->tanggal_mulai->translatedFormat('d M Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->jumlah_hari }} hari</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->disetujuiOleh->nama ?? '-' }}</td>
                        <td class="py-3 pr-4">
                            <span @class([
                                'px-2 py-1 rounded text-xs font-semibold',
                                'bg-orange-100 text-orange-700' => $c->status === 'menunggu',
                                'bg-green-100 text-green-700' => $c->status === 'disetujui',
                                'bg-red-100 text-red-700' => $c->status === 'ditolak',
                            ])>
                                {{ ucfirst($c->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-gray-400">Belum ada riwayat pengajuan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection