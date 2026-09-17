@extends('layouts.atasan')

@section('title', 'Riwayat Izin Anggota Divisi')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Izin Anggota Divisi</h1>
    <p class="text-gray-500 mt-1 mb-6">Monitoring dan approval izin anggota divisi</p>

    @include('atasan.izin-anggota-divisi._tabs')

    <form method="GET" class="mb-6">
        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
               class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
    </form>

    <div class="space-y-4">
        @forelse ($data as $item)
            <div x-data="{ open: false }">
                <div class="flex items-center gap-3 py-3">
                    <div class="w-9 h-9 rounded-full bg-red-700 text-white flex items-center justify-center text-xs font-semibold">
                        {{ strtoupper(substr($item->user->nama, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ $item->user->nama }}</p>
                        <p class="text-xs text-gray-400">{{ $item->user->divisi->nama ?? '-' }} · {{ $item->jumlah }} kali izin bulan ini</p>
                    </div>
                    <button type="button" @click="open = !open" class="px-4 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                        Detail
                    </button>
                </div>

                <div x-show="open" class="pl-12 pb-6">
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
                                @forelse ($item->riwayat as $izin)
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
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Belum ada anggota tim.
            </div>
        @endforelse
    </div>
@endsection