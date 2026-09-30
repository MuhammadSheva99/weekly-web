@extends('layouts.management')

@section('title', 'SP Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Surat Peringatan Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Riwayat Surat Peringatan yang diterbitkan</p>

    <form method="GET" class="flex flex-wrap items-end gap-4 mb-6">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Divisi</label>
            <select name="divisi_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                <option value="">Semua Divisi</option>
                @foreach ($divisiList as $d)
                    <option value="{{ $d->id }}" @selected(request('divisi_id') == $d->id)>{{ $d->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="berakhir" @selected(request('status') === 'berakhir')>Berakhir</option>
            </select>
        </div>
    </form>

    <div class="space-y-4">
        @forelse ($daftar as $sp)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 flex items-center gap-6">
                <div class="flex items-center gap-3 w-40 shrink-0">
                    <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-semibold">
                        {{ strtoupper(substr($sp->user->nama, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $sp->user->nama }}</p>
                        <p class="text-xs text-gray-400">{{ $sp->user->divisi->nama ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex-1 grid grid-cols-5 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">No. SP</p>
                        <p class="font-medium text-gray-800">{{ $sp->no_sp }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Level</p>
                        <p class="font-medium text-gray-800">{{ $sp->level }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Tanggal Terbit</p>
                        <p class="font-medium text-gray-800">{{ $sp->tanggal_terbit->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Berlaku Sampai</p>
                        <p class="font-medium text-gray-800">{{ $sp->tanggal_berakhir->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Diterbitkan Oleh</p>
                        <p class="font-medium text-gray-800">{{ $sp->diterbitkanOleh->nama ?? '-' }}</p>
                    </div>
                </div>

                <div class="w-28 shrink-0 text-sm">
                    <p class="text-xs text-gray-400">Status</p>
                    @if ($sp->isAktif())
                        <p class="font-medium text-amber-600">Aktif</p>
                    @else
                        <p class="font-medium text-gray-500">Berakhir</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Belum ada Surat Peringatan yang diterbitkan.
            </div>
        @endforelse
    </div>
@endsection