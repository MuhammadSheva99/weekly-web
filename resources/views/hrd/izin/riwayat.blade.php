@extends('layouts.hrd')

@section('title', 'Riwayat Pengajuan Izin')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Izin</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan pengajuan izin - Tahun {{ now()->year }}</p>

    @include('hrd.izin._tabs')

    <form method="GET" class="flex gap-3 mb-6">
        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
               class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Status</option>
            <option value="disetujui" @selected(request('status') === 'disetujui')>Disetujui</option>
            <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
        </select>
    </form>

    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Jenis izin</th>
                    <th class="py-3 pr-4">Tanggal</th>
                    <th class="py-3 pr-4">Jam</th>
                    <th class="py-3 pr-4">Keterangan</th>
                    <th class="py-3 pr-4">Disetujui oleh</th>
                    <th class="py-3 pr-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $izin)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $izin->label_jenis_izin }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $izin->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">
                            {{ \Carbon\Carbon::parse($izin->jam_mulai)->format('H:i') }}
                            @if ($izin->estimasi_kembali)–{{ \Carbon\Carbon::parse($izin->estimasi_kembali)->format('H:i') }}@endif
                        </td>
                        <td class="py-3 pr-4 text-gray-600">{{ $izin->keterangan ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $izin->disetujuiOleh->nama ?? '-' }}</td>
                        <td class="py-3 pr-4">
                            <span @class([
                                'px-2 py-1 rounded text-xs font-semibold',
                                'bg-green-100 text-green-700' => $izin->status === 'disetujui',
                                'bg-red-100 text-red-700' => $izin->status === 'ditolak',
                            ])>
                                {{ ucfirst($izin->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-gray-400">Belum ada riwayat pengajuan izin.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection