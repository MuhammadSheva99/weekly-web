@extends('layouts.hrd')

@section('title', 'Ajukan Cuti')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Cuti</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan saldo cuti - Tahun {{ now()->year }}</p>

    @include('hrd.cuti._tabs')

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="bg-blue-50 rounded-2xl p-6 mb-8">
        <p class="text-sm text-gray-600">Sisa kuota cuti tahunan</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $sisaKuota }} hari tersisa dari {{ $jatah }} hari/tahun</p>
        <p class="text-sm text-gray-500 mt-1">Terpakai: {{ $terpakai }} hari · Menunggu persetujuan: {{ $menunggu }} pengajuan</p>
    </div>

    <form method="POST" action="{{ route('hrd.cuti.store') }}" enctype="multipart/form-data" class="space-y-6 max-w-xl">
        @csrf

        <div>
            <label class="block text-sm text-gray-600 mb-1">Jenis cuti</label>
            <select name="jenis_cuti" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                <option value="">Pilih jenis cuti</option>
                <option value="Cuti tahunan">Cuti tahunan</option>
                <option value="Izin">Izin</option>
                <option value="Cuti sakit">Cuti sakit</option>
                <option value="Cuti melahirkan">Cuti melahirkan</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Tanggal mulai</label>
                <input type="date" name="tanggal_mulai" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Tanggal selesai</label>
                <input type="date" name="tanggal_selesai" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Keterangan / alasan</label>
            <textarea name="keterangan" rows="3" placeholder="Jelaskan alasan pengajuan cuti"
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm"></textarea>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Lampiran (opsional - misal: surat dokter)</label>
            <input type="file" name="lampiran" class="w-full text-sm">
        </div>

        @if ($atasan)
            <div>
                <label class="block text-sm text-gray-600 mb-1">Disetujui oleh</label>
                <input type="text" value="{{ $atasan->nama }}" disabled class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-500">
                <p class="text-xs text-gray-400 mt-1">Pengajuan akan diteruskan ke atasan langsung untuk disetujui sebelum tanggal mulai cuti.</p>
            </div>
        @else
            <div class="px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">
                Akun Anda (HRD) — pengajuan otomatis disetujui.
            </div>
        @endif

        <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition">
            Ajukan Cuti
        </button>
    </form>
@endsection