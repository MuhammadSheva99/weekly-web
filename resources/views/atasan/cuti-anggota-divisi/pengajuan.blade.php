@extends('layouts.atasan')

@section('title', 'Cuti Anggota Divisi')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Cuti Anggota Divisi</h1>
    <p class="text-gray-500 mt-1 mb-6">Monitoring dan approval cuti anggota divisi</p>

    @include('atasan.cuti-anggota-divisi._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <div class="space-y-4" x-data="{ previewOpen: false, previewUrl: '' }">
        @forelse ($daftar as $c)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 flex items-center gap-6">
                <div class="flex items-center gap-3 w-40 shrink-0">
                    <div class="w-10 h-10 rounded-full bg-red-700 text-white flex items-center justify-center font-semibold">
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
                            <button type="button"
                                    @click="previewUrl = '{{ Storage::url($c->lampiran_path) }}'; previewOpen = true"
                                    class="inline-block px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50">
                                Lihat File
                            </button>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </div>
                </div>

                <div class="w-24 shrink-0 text-sm">
                    <p class="text-xs text-gray-400">Disetujui oleh</p>
                    <p class="font-medium text-gray-800">{{ auth()->user()->nama }}</p>
                </div>

                <div class="flex flex-col gap-2 shrink-0">
                    <form method="POST" action="{{ route('atasan.cuti-anggota-divisi.approve', $c) }}">
                        @csrf
                        <button type="submit" class="w-24 px-3 py-1.5 border border-green-300 text-green-700 rounded text-xs font-medium hover:bg-green-50">Setuju</button>
                    </form>
                    <form method="POST" action="{{ route('atasan.cuti-anggota-divisi.reject', $c) }}" onsubmit="return confirm('Yakin tolak pengajuan ini?')">
                        @csrf
                        <button type="submit" class="w-24 px-3 py-1.5 border border-red-300 text-red-700 rounded text-xs font-medium hover:bg-red-50">Tolak</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Tidak ada pengajuan cuti yang menunggu dari anggota tim kamu.
            </div>
        @endforelse

        <div x-show="previewOpen" x-cloak
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-6"
             @click.self="previewOpen = false">
            <div class="bg-white rounded-2xl overflow-hidden w-full max-w-3xl h-[80vh] flex flex-col">
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200">
                    <p class="font-semibold text-gray-800">Preview Lampiran</p>
                    <button type="button" @click="previewOpen = false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                </div>
                <div class="flex-1">
                    <iframe :src="previewUrl" class="w-full h-full" frameborder="0"></iframe>
                </div>
                <div class="px-5 py-3 border-t border-gray-200 text-right">
                    <a :href="previewUrl" target="_blank" class="text-sm text-blue-600 hover:underline">Buka di tab baru</a>
                </div>
            </div>
        </div>
    </div>
@endsection