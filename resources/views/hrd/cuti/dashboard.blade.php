@extends('layouts.hrd')

@section('title', 'Dashboard Cuti')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Cuti</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan saldo cuti - Tahun {{ now()->year }}</p>

    @include('hrd.cuti._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
        <div class="bg-blue-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Jatah cuti tahunan</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $jatah }} hari</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Cuti terpakai</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $terpakai }} hari</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Sisa cuti tahun lalu (terbawa)</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $sisaTahunLalu }} hari</p>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Status pengajuan terakhir</h2>
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
        @forelse ($terakhir as $c)
            <div class="flex items-center justify-between px-6 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <p class="text-gray-800">
                    {{ $c->jenis_cuti }} · {{ $c->tanggal_mulai->translatedFormat('d M Y') }}
                    @if (!$c->tanggal_mulai->isSameDay($c->tanggal_selesai))
                        – {{ $c->tanggal_selesai->translatedFormat('d M Y') }}
                    @endif
                    ({{ $c->jumlah_hari }} hari)
                </p>
                <span @class([
                    'px-3 py-1 rounded-full text-xs font-medium',
                    'bg-orange-100 text-orange-700' => $c->status === 'menunggu',
                    'bg-green-100 text-green-700' => $c->status === 'disetujui',
                    'bg-red-100 text-red-700' => $c->status === 'ditolak',
                ])>
                    {{ ['menunggu' => 'Menunggu Persetujuan', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'][$c->status] }}
                </span>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-gray-400">Belum ada pengajuan cuti.</div>
        @endforelse
    </div>
@endsection