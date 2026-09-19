@extends('layouts.atasan')

@section('title', 'Riwayat Cuti ' . $anggota->nama)

@section('content')
    <a href="{{ route('atasan.cuti-anggota-divisi.riwayat') }}" class="text-sm text-gray-500 hover:underline">← Kembali</a>

    <h1 class="text-3xl font-bold text-gray-900 mt-3">Riwayat Cuti {{ $anggota->nama }}</h1>
    <p class="text-gray-500 mt-1 mb-6">{{ $anggota->divisi->nama ?? '-' }}</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-red-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Jatah cuti tahunan</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $jatah }} hari</p>
        </div>
        <div class="bg-red-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Cuti terpakai</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $terpakai }} hari</p>
        </div>
        <div class="bg-red-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Sisa Cuti</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $sisa }} hari</p>
        </div>
    </div>

    <form method="GET" class="mb-4">
        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
               class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
    </form>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 border-b text-xs">
                    <th class="py-3 pr-4 pl-4">Jenis Cuti</th>
                    <th class="py-3 pr-4">Tanggal Mulai</th>
                    <th class="py-3 pr-4">Tanggal Selesai</th>
                    <th class="py-3 pr-4">Sisa Cuti</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $c)
                    <tr class="border-b">
                        <td class="py-3 pr-4 pl-4 font-medium text-gray-900">{{ $c->jenis_cuti }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->tanggal_mulai->format('d/m/Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->tanggal_selesai->format('d/m/Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $sisa }}/{{ $jatah }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-gray-400">Tidak ada cuti di bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection