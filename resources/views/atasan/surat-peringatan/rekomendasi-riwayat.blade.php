@extends('layouts.atasan')

@section('title', 'Riwayat Rekomendasi SP')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Surat Peringatan</h1>
    <p class="text-gray-500 mt-1 mb-6">Peraturan perusahaan dan riwayat pelanggaran tim</p>

    @include('atasan.surat-peringatan._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    @php
        $labelStatus = [
            'menunggu' => ['Menunggu HRD', 'bg-yellow-100 text-yellow-700'],
            'diterbitkan' => ['Diterbitkan', 'bg-green-100 text-green-700'],
            'ditolak' => ['Ditolak', 'bg-gray-200 text-gray-600'],
        ];
    @endphp

    @if ($daftar->isEmpty())
        <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
            Belum ada rekomendasi SP yang pernah Anda kirim.
        </div>
    @else
        <div class="space-y-4">
            @foreach ($daftar as $r)
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-red-700 text-white flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr($r->user->nama, 0, 2)) }}
                            </div>
                            <p class="font-semibold text-gray-900">{{ $r->user->nama }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ $r->level_usulan }}</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $labelStatus[$r->status][1] }}">
                                {{ $labelStatus[$r->status][0] }}
                            </span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Dikirim: {{ $r->created_at->translatedFormat('d F Y, H:i') }}</p>
                    <p class="text-sm text-gray-700 mt-3"><span class="font-medium">Alasan:</span> {{ $r->alasan }}</p>

                    @if ($r->status === 'ditolak' && $r->alasan_penolakan)
                        <p class="text-sm text-red-600 mt-2"><span class="font-medium">Alasan ditolak HRD:</span> {{ $r->alasan_penolakan }}</p>
                    @endif

                    @if ($r->status === 'diterbitkan' && $r->suratPeringatan && $r->suratPeringatan->file_path)
                        <a href="{{ route('atasan.surat-peringatan.download', $r->suratPeringatan) }}" class="inline-block mt-4 px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                            Lihat Surat yang Diterbitkan
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection