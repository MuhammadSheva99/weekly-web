@extends('layouts.management')

@section('title', 'Cuti Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Cuti Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Tren Bulanan Perusahaan Dan Rencana Perbaikan</p>

    <form method="GET" class="flex flex-wrap items-end gap-4 mb-6">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Periode</label>
            <input type="month" name="periode" value="{{ $periode }}"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm"
                   onchange="this.form.submit()">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Divisi</label>
            <select name="divisi_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                <option value="">Semua Divisi</option>
                @foreach ($divisiList as $d)
                    <option value="{{ $d->id }}" @selected(request('divisi_id') == $d->id)>{{ $d->nama }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="space-y-4">
        @forelse ($daftar as $c)
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

                <div class="flex-1 grid grid-cols-5 gap-4 text-sm">
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
                        <p class="text-xs text-gray-400">Sisa Kuota</p>
                        <p class="font-medium text-gray-800">{{ $c->sisa_cuti }} hari</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Lampiran</p>
                        @if ($c->lampiran_path)
                            <a href="{{ Storage::url($c->lampiran_path) }}" target="_blank" class="inline-block px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50">Lihat File</a>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </div>
                </div>

                <div class="w-32 shrink-0 text-sm">
                    <p class="text-xs text-gray-400">Status</p>
                    @php
                        $statusLabel = match($c->status) {
                            'menunggu_atasan' => 'Menunggu Atasan',
                            'menunggu_hrd' => 'Menunggu HRD',
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            default => $c->status,
                        };
                        $statusColor = match($c->status) {
                            'disetujui' => 'text-green-700',
                            'ditolak' => 'text-red-600',
                            default => 'text-amber-600',
                        };
                    @endphp
                    <p class="font-medium {{ $statusColor }}">{{ $statusLabel }}</p>
                    @if ($c->status === 'disetujui' && $c->disetujuiOleh)
                        <p class="text-xs text-gray-400 mt-0.5">oleh {{ $c->disetujuiOleh->nama }}</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Tidak ada pengajuan cuti pada periode ini.
            </div>
        @endforelse
    </div>
@endsection