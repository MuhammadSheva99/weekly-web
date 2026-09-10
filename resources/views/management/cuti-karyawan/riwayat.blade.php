@extends('layouts.management')

@section('title', 'Riwayat Cuti Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Cuti Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Tren Bulanan Perusahaan Dan Rencana Perbaikan</p>

    @include('management.cuti-karyawan._tabs')

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Divisi</h2>
    <form method="GET" class="mb-8">
        <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Divisi</option>
            @foreach ($divisiList as $divisi)
                <option value="{{ $divisi->id }}" @selected(request('divisi_id') == $divisi->id)>{{ $divisi->nama }}</option>
            @endforeach
        </select>
    </form>

    <div class="space-y-4">
        @forelse ($riwayat as $c)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 flex items-center gap-6">
                <div class="flex items-center gap-3 w-40 shrink-0">
                    <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-semibold">
                        {{ strtoupper(substr($c->user->nama, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $c->user->nama }}</p>
                        <p class="text-xs text-gray-400">{{ $c->user->divisi->nama ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex-1 grid grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Jenis Cuti</p>
                        <p class="font-medium text-gray-800">{{ $c->jenis_cuti }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Tanggal Mulai</p>
                        <p class="font-medium text-gray-800">{{ $c->tanggal_mulai->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Tanggal Selesai</p>
                        <p class="font-medium text-gray-800">{{ $c->tanggal_selesai->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Sisa Cuti</p>
                        <p class="font-medium text-gray-800">{{ $c->sisa_cuti }}/{{ $c->user->jatah_cuti_tahunan }}</p>
                    </div>
                </div>

                <span @class([
                    'px-3 py-1 rounded-full text-xs font-medium shrink-0',
                    'bg-green-100 text-green-700' => $c->status === 'disetujui',
                    'bg-red-100 text-red-700' => $c->status === 'ditolak',
                ])>
                    {{ ucfirst($c->status) }}
                </span>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Belum ada riwayat cuti.
            </div>
        @endforelse
    </div>
@endsection