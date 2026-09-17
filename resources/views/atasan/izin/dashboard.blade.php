@extends('layouts.atasan')

@section('title', 'Dashboard Izin')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Izin</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan pengajuan izin - Tahun {{ now()->year }}</p>

    @include('atasan.izin._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <div class="bg-red-50 rounded-xl p-5 mb-10 max-w-xs">
        <p class="text-sm text-gray-500">Izin Bulan ini</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $jumlahIzinBulanIni }} Kali Izin</p>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Izin Bulan Ini</h2>
    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Jenis izin</th>
                    <th class="py-3 pr-4">Tanggal</th>
                    <th class="py-3 pr-4">Jam</th>
                    <th class="py-3 pr-4">Keterangan</th>
                    <th class="py-3 pr-4">Disetujui oleh</th>
                    <th class="py-3 pr-4">Lampiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($izinBulanIni as $izin)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $izin->label_jenis_izin }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $izin->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">
                            @if ($izin->jenis_izin === 'pulang_cepat')
                                Pulang {{ \Carbon\Carbon::parse($izin->jam_mulai)->format('H:i') }}
                            @elseif ($izin->jenis_izin === 'keluar_sementara')
                                {{ \Carbon\Carbon::parse($izin->jam_mulai)->format('H:i') }}@if($izin->estimasi_kembali)–{{ \Carbon\Carbon::parse($izin->estimasi_kembali)->format('H:i') }}@endif
                            @else
                                Masuk {{ \Carbon\Carbon::parse($izin->jam_mulai)->format('H:i') }}
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-gray-600">{{ $izin->keterangan ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $izin->disetujuiOleh->nama ?? '-' }}</td>
                        <td class="py-3 pr-4">
                            @if ($izin->lampiran_path)
                                <a href="{{ Storage::url($izin->lampiran_path) }}" target="_blank" class="inline-block px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50">Lihat File</a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">Tidak ada izin bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection