@extends('layouts.hrd')

@section('title', 'Cuti Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Cuti Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Monitoring Cuti Karyawan</p>

    @include('hrd.cuti-karyawan._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Karyawan yang mengajukan cuti</h2>

    <div class="space-y-4">
        @forelse ($daftar as $c)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 flex items-center gap-6">
                <div class="flex items-center gap-3 w-40 shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-700 text-white flex items-center justify-center font-semibold">
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
                        <p class="text-xs text-gray-400">Keterangan</p>
                        <p class="font-medium text-gray-800">{{ $c->keterangan ?? '-' }}</p>
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

                <div class="w-28 shrink-0 text-sm">
                    <p class="text-xs text-gray-400">Diteruskan ke</p>
                    <p class="font-medium text-gray-800">{{ $c->user->atasan->nama ?? '-' }}</p>
                </div>

                <div class="flex flex-col gap-2 shrink-0">
                    <form method="POST" action="{{ route('hrd.cuti-karyawan.approve', $c) }}">
                        @csrf
                        <button type="submit" class="w-24 px-3 py-1.5 border border-green-300 text-green-700 rounded text-xs font-medium hover:bg-green-50">Setuju</button>
                    </form>
                    <form method="POST" action="{{ route('hrd.cuti-karyawan.reject', $c) }}" onsubmit="return confirm('Yakin tolak pengajuan ini?')">
                        @csrf
                        <button type="submit" class="w-24 px-3 py-1.5 border border-red-300 text-red-700 rounded text-xs font-medium hover:bg-red-50">Tolak</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Tidak ada pengajuan cuti yang menunggu.
            </div>
        @endforelse
    </div>
@endsection