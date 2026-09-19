@extends('layouts.atasan')

@section('title', 'Riwayat Izin ' . $anggota->nama)

@section('content')
    <a href="{{ route('atasan.izin-anggota-divisi.riwayat') }}" class="text-sm text-gray-500 hover:underline">← Kembali</a>

    <h1 class="text-3xl font-bold text-gray-900 mt-3">Riwayat Izin {{ $anggota->nama }}</h1>
    <p class="text-gray-500 mt-1 mb-6">{{ $anggota->divisi->nama ?? '-' }}</p>

    <form method="GET" class="mb-4">
        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
               class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
    </form>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 border-b text-xs">
                    <th class="py-3 pr-4 pl-4">Jenis Izin</th>
                    <th class="py-3 pr-4">Tanggal</th>
                    <th class="py-3 pr-4">Jam</th>
                    <th class="py-3 pr-4">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $izin)
                    <tr class="border-b">
                        <td class="py-3 pr-4 pl-4 font-medium text-gray-900">{{ $izin->label_jenis_izin }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $izin->tanggal->format('d/m/Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">
                            {{ \Carbon\Carbon::parse($izin->jam_mulai)->format('H:i') }}
                            @if ($izin->estimasi_kembali)–{{ \Carbon\Carbon::parse($izin->estimasi_kembali)->format('H:i') }}@endif
                        </td>
                        <td class="py-3 pr-4 text-gray-600">{{ $izin->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-gray-400">Tidak ada izin di bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection