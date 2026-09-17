@extends('layouts.hrd')

@section('title', 'Riwayat Izin Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Izin Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Riwayat izin seluruh karyawan</p>

    <form method="GET" class="mb-8">
        <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Divisi</option>
            @foreach ($divisiList as $divisi)
                <option value="{{ $divisi->id }}" @selected(request('divisi_id') == $divisi->id)>{{ $divisi->nama }}</option>
            @endforeach
        </select>
    </form>

    <div class="space-y-4">
        @forelse ($riwayat as $izin)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 flex items-center gap-6">
                <div class="flex items-center gap-3 w-40 shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-700 text-white flex items-center justify-center font-semibold">
                        {{ strtoupper(substr($izin->user->nama, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $izin->user->nama }}</p>
                        <p class="text-xs text-gray-400">{{ $izin->user->divisi->nama ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex-1 grid grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Jenis Izin</p>
                        <p class="font-medium text-gray-800">{{ $izin->label_jenis_izin }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Tanggal</p>
                        <p class="font-medium text-gray-800">{{ $izin->tanggal->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Jam</p>
                        <p class="font-medium text-gray-800">
                            {{ \Carbon\Carbon::parse($izin->jam_mulai)->format('H:i') }}
                            @if ($izin->estimasi_kembali)–{{ \Carbon\Carbon::parse($izin->estimasi_kembali)->format('H:i') }}@endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Keterangan</p>
                        <p class="font-medium text-gray-800">{{ $izin->keterangan ?? '-' }}</p>
                    </div>
                </div>

                <span @class([
                    'px-3 py-1 rounded-full text-xs font-medium shrink-0',
                    'bg-green-100 text-green-700' => $izin->status === 'disetujui',
                    'bg-red-100 text-red-700' => $izin->status === 'ditolak',
                ])>
                    {{ ucfirst($izin->status) }}
                </span>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Belum ada riwayat izin.
            </div>
        @endforelse
    </div>
@endsection