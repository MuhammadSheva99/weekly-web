@extends('layouts.karyawan')

@section('title', 'Surat Peringatan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Surat Peringatan</h1>
    <p class="text-gray-500 mt-1 mb-6">Peraturan perusahaan dan riwayat pelanggaran</p>

    @include('karyawan.surat-peringatan._tabs')

    @if ($dokumen)
        <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-700 font-semibold text-xs">
                    DOC
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ $dokumen->judul }}</p>
                    <p class="text-sm text-gray-400">Format .{{ pathinfo($dokumen->file_path, PATHINFO_EXTENSION) }}</p>
                </div>
            </div>
            <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium">
                Unduh
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden" style="height: 700px;">
            <iframe
                src="https://docs.google.com/gview?url={{ urlencode(Storage::url($dokumen->file_path)) }}&embedded=true"
                class="w-full h-full" frameborder="0">
            </iframe>
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
            Belum ada dokumen Peraturan Perusahaan yang diunggah.
        </div>
    @endif
@endsection