@extends('layouts.karyawan')

@section('title', 'Dashboard Cuti')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Cuti</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan saldo cuti - Tahun {{ now()->year }}</p>

    @include('karyawan.cuti._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
        <div class="bg-amber-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Jatah cuti tahunan</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $jatah }} hari</p>
        </div>
        <div class="bg-amber-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Cuti terpakai</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $terpakai }} hari</p>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Cuti Bulan Ini</h2>
    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Jenis Cuti</th>
                    <th class="py-3 pr-4">Tanggal</th>
                    <th class="py-3 pr-4">Jumlah Hari</th>
                    <th class="py-3 pr-4">Keterangan</th>
                    <th class="py-3 pr-4">Disetujui oleh</th>
                    <th class="py-3 pr-4">Lampiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cutiBulanIni as $c)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $c->jenis_cuti }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->tanggal_mulai->translatedFormat('d M Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->jumlah_hari }} Hari</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->keterangan ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->disetujuiOleh->nama ?? '-' }}</td>
                        <td class="py-3 pr-4">
                            @if ($c->lampiran_path)
                                <a href="{{ Storage::url($c->lampiran_path) }}" target="_blank" class="inline-block px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50">Lihat File</a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">Tidak ada cuti bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection