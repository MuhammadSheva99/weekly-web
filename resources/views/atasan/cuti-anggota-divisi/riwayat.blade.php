@extends('layouts.atasan')

@section('title', 'Riwayat Cuti Anggota Divisi')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Cuti Anggota Divisi</h1>
    <p class="text-gray-500 mt-1 mb-6">Monitoring dan approval cuti anggota divisi</p>

    @include('atasan.cuti-anggota-divisi._tabs')

    <div class="space-y-4">
        @forelse ($data as $item)
            <div x-data="{ open: false }">
                <div class="flex items-center gap-3 py-3">
                    <div class="w-9 h-9 rounded-full bg-red-700 text-white flex items-center justify-center text-xs font-semibold">
                        {{ strtoupper(substr($item->user->nama, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ $item->user->nama }}</p>
                        <p class="text-xs text-gray-400">{{ $item->user->divisi->nama ?? '-' }}</p>
                    </div>
                    <button type="button" @click="open = !open" class="px-4 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                        Detail
                    </button>
                </div>

                <div x-show="open" class="pl-12 pb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Cuti {{ $item->user->nama }}</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-red-50 rounded-xl p-5">
                            <p class="text-sm text-gray-500">Jatah cuti tahunan</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $item->jatah }} hari</p>
                        </div>
                        <div class="bg-red-50 rounded-xl p-5">
                            <p class="text-sm text-gray-500">Cuti terpakai</p>
                            <p class="text-2xl font-bold text-red-600 mt-1">{{ $item->terpakai }} hari</p>
                        </div>
                        <div class="bg-red-50 rounded-xl p-5">
                            <p class="text-sm text-gray-500">Sisa Cuti</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ $item->sisa }} hari</p>
                        </div>
                    </div>

                    <form method="GET" class="mb-4">
                        <input type="hidden" name="anggota" value="{{ $item->user->id }}">
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
                                @forelse ($item->riwayat as $c)
                                    <tr class="border-b">
                                        <td class="py-3 pr-4 pl-4 font-medium text-gray-900">{{ $c->jenis_cuti }}</td>
                                        <td class="py-3 pr-4 text-gray-600">{{ $c->tanggal_mulai->format('d/m/Y') }}</td>
                                        <td class="py-3 pr-4 text-gray-600">{{ $c->tanggal_selesai->format('d/m/Y') }}</td>
                                        <td class="py-3 pr-4 text-gray-600">{{ $item->sisa }}/{{ $item->jatah }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-6 text-center text-gray-400">Tidak ada cuti di bulan ini.</td></tr>
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