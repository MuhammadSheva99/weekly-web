@extends('layouts.hrd')

@section('title', 'Rekomendasi SP dari Atasan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Surat Peringatan Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Management SP Karyawan</p>

    @include('hrd.sp-karyawan._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    @if ($daftar->isEmpty())
        <div class="bg-green-50 border border-green-100 rounded-2xl p-10 text-center">
            <p class="text-green-700 font-medium">Tidak ada rekomendasi SP yang menunggu</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($daftar as $r)
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-700 text-white flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr($r->user->nama, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $r->user->nama }}</p>
                                <p class="text-xs text-gray-400">{{ $r->user->divisi->nama ?? '-' }} · Direkomendasikan oleh {{ $r->direkomendasikanOleh->nama }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ $r->level_usulan }}</span>
                    </div>

                    <p class="text-sm text-gray-500 mb-1">Dikirim: {{ $r->created_at->translatedFormat('d F Y, H:i') }}</p>
                    <p class="text-sm text-gray-700 mt-3"><span class="font-medium">Alasan:</span> {{ $r->alasan }}</p>

                    @if ($r->lampiran_path)
                        <a href="{{ Storage::url($r->lampiran_path) }}" target="_blank" class="inline-block mt-2 text-sm text-blue-600 hover:underline">
                            Lihat lampiran
                        </a>
                    @endif

                    <div class="flex gap-3 mt-4">
                        <a href="{{ route('hrd.sp-karyawan.terbitkan', ['rekomendasi_id' => $r->id]) }}"
                           class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-lg">
                            Terbitkan SP
                        </a>

                        <button type="button" onclick="document.getElementById('tolak-{{ $r->id }}').classList.toggle('hidden')"
                                class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50">
                            Tolak
                        </button>
                    </div>

                    <form id="tolak-{{ $r->id }}" method="POST" action="{{ route('hrd.sp-karyawan.rekomendasi.tolak', $r) }}" class="hidden mt-4 space-y-3">
                        @csrf
                        <textarea name="alasan_penolakan" rows="2" placeholder="Alasan penolakan (opsional)"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm"></textarea>
                        <button type="submit" class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-semibold rounded-lg">
                            Kirim Penolakan
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
@endsection