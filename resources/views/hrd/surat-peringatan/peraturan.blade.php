@extends('layouts.hrd')

@section('title', 'Surat Peringatan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Surat Peringatan</h1>
    <p class="text-gray-500 mt-1 mb-6">Peraturan perusahaan dan riwayat pelanggaran karyawan</p>

    @include('hrd.surat-peringatan._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    @if ($dokumen)
        <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-8 flex items-center justify-between">
            <div>
                <p class="font-semibold text-gray-900">{{ $dokumen->judul }}</p>
                <p class="text-sm text-gray-400">Diunggah oleh {{ $dokumen->uploadedBy->nama }} · {{ $dokumen->created_at->translatedFormat('d M Y') }}</p>
            </div>
            <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                Lihat / Unduh
            </a>
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center text-gray-400 mb-8">
            Belum ada dokumen PP diunggah.
        </div>
    @endif

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Unggah / Ganti Dokumen</h2>
    <form method="POST" action="{{ route('hrd.surat-peringatan.store-dokumen') }}" enctype="multipart/form-data" class="space-y-4 max-w-xl">
        @csrf
        <div>
            <label class="block text-sm text-gray-600 mb-1">Judul dokumen</label>
            <input type="text" name="judul" required value="PERATURAN PERUSAHAAN (PP)"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">File (.docx atau .pdf)</label>
            <input type="file" name="file" required accept=".doc,.docx,.pdf" class="w-full text-sm">
        </div>
        <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg text-sm">
            Simpan Dokumen
        </button>
    </form>
@endsection