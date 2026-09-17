@extends('layouts.hrd')

@section('title', 'Izin Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Izin Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Approval final pengajuan izin seluruh karyawan</p>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <div class="space-y-4">
        @forelse ($daftar as $izin)
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

                <div class="flex-1 grid grid-cols-5 gap-4 text-sm">
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
                    <div>
                        <p class="text-xs text-gray-400">Lampiran</p>
                        @if ($izin->lampiran_path)
                            <a href="{{ Storage::url($izin->lampiran_path) }}" target="_blank" class="inline-block px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50">Lihat File</a>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </div>
                </div>

                <div class="w-28 shrink-0 text-sm">
                    <p class="text-xs text-gray-400">Diteruskan ke</p>
                    <p class="font-medium text-gray-800">{{ $izin->user->atasan->nama ?? '-' }}</p>
                </div>

                <div class="flex flex-col gap-2 shrink-0">
                    <form method="POST" action="{{ route('hrd.izin-karyawan.approve', $izin) }}">
                        @csrf
                        <button type="submit" class="w-24 px-3 py-1.5 border border-green-300 text-green-700 rounded text-xs font-medium hover:bg-green-50">Setuju</button>
                    </form>
                    <form method="POST" action="{{ route('hrd.izin-karyawan.reject', $izin) }}" onsubmit="return confirm('Yakin tolak pengajuan ini?')">
                        @csrf
                        <button type="submit" class="w-24 px-3 py-1.5 border border-red-300 text-red-700 rounded text-xs font-medium hover:bg-red-50">Tolak</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Tidak ada pengajuan izin yang menunggu.
            </div>
        @endforelse
    </div>
@endsection