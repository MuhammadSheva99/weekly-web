@extends('layouts.atasan')

@section('title', 'Surat Peringatan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Surat Peringatan</h1>
    <p class="text-gray-500 mt-1 mb-6">Peraturan perusahaan dan riwayat pelanggaran tim</p>

    @include('atasan.surat-peringatan._tabs')

    @if ($daftar->isEmpty())
        <div class="bg-green-50 border border-green-100 rounded-2xl p-10 text-center">
            <p class="text-green-700 font-medium">Tidak ada SP aktif untuk anggota tim anda</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($daftar as $sp)
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-red-700 text-white flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr($sp->user->nama, 0, 2)) }}
                            </div>
                            <p class="font-semibold text-gray-900">{{ $sp->user->nama }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ $sp->level }}</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sp->isAktif() ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $sp->isAktif() ? 'Aktif' : 'Berakhir' }}
                            </span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Tanggal terbit: {{ $sp->tanggal_terbit->translatedFormat('d F Y') }} · Berlaku sampai {{ $sp->tanggal_berakhir->translatedFormat('d F Y') }}</p>
                    <p class="text-sm text-gray-700 mt-3"><span class="font-medium">Alasan:</span> {{ $sp->alasan }}</p>
                    @if ($sp->konsekuensi)
                        <p class="text-sm text-gray-700 mt-1"><span class="font-medium">Konsekuensi:</span> {{ $sp->konsekuensi }}</p>
                    @endif
                    @if ($sp->file_path)
                        <a href="{{ route('atasan.surat-peringatan.download', $sp) }}" class="inline-block mt-4 px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                            Lihat Surat
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection