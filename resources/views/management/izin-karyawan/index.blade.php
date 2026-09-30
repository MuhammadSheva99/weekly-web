@extends('layouts.management')

@section('title', 'Izin Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Izin Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan pengajuan izin karyawan & atasan</p>

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
        @forelse ($daftar as $i)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 flex items-center gap-6">
                <div class="flex items-center gap-3 w-40 shrink-0">
                    <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-semibold">
                        {{ strtoupper(substr($i->user->nama, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $i->user->nama }}</p>
                        <p class="text-xs text-gray-400">{{ $i->user->divisi->nama ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex-1 grid grid-cols-5 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Jenis Izin</p>
                        <p class="font-medium text-gray-800">{{ $i->label_jenis_izin }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Tanggal</p>
                        <p class="font-medium text-gray-800">{{ $i->tanggal->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Jam Mulai</p>
                        <p class="font-medium text-gray-800">{{ $i->jam_mulai ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Estimasi Kembali</p>
                        <p class="font-medium text-gray-800">{{ $i->estimasi_kembali ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Lampiran</p>
                        @if ($i->lampiran_path)
                            <a href="{{ Storage::url($i->lampiran_path) }}" target="_blank" class="inline-block px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50">Lihat File</a>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </div>
                </div>

                <div class="w-32 shrink-0 text-sm">
                    <p class="text-xs text-gray-400">Status</p>
                    @php
                        $statusLabel = match($i->status) {
                            'menunggu_atasan' => 'Menunggu Atasan',
                            'menunggu_hrd' => 'Menunggu HRD',
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            default => $i->status,
                        };
                        $statusColor = match($i->status) {
                            'disetujui' => 'text-green-700',
                            'ditolak' => 'text-red-600',
                            default => 'text-amber-600',
                        };
                    @endphp
                    <p class="font-medium {{ $statusColor }}">{{ $statusLabel }}</p>
                    @if ($i->status === 'disetujui' && $i->disetujuiOleh)
                        <p class="text-xs text-gray-400 mt-0.5">oleh {{ $i->disetujuiOleh->nama }}</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Tidak ada pengajuan izin pada periode ini.
            </div>
        @endforelse
    </div>
@endsection