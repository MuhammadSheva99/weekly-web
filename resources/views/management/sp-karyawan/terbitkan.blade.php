@extends('layouts.management')

@section('title', 'Terbitkan SP')

@section('content')
<div class="max-w-4xl">
    @include('management.sp-karyawan._tabs')

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Terbitkan Surat Peringatan Baru</h2>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('management.sp-karyawan.store') }}" class="space-y-5">
            @csrf

            <!-- Karyawan -->
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                <select name="user_id" id="user_id" required class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500 py-2.5">
                    <option value="">Pilih karyawan</option>
                    @foreach($karyawans as $karyawan)
                        <option value="{{ $karyawan->id }}" {{ old('user_id') == $karyawan->id ? 'selected' : '' }}>
                            {{ $karyawan->nama }} {{ $karyawan->divisi ? '('.$karyawan->divisi->nama.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Level SP -->
            <div>
                <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-1">Level SP</label>
                <select name="tingkat" id="tingkat" required class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500 py-2.5">
                    <option value="">Pilih level</option>
                    <option value="1" {{ old('tingkat') == '1' ? 'selected' : '' }}>SP 1</option>
                    <option value="2" {{ old('tingkat') == '2' ? 'selected' : '' }}>SP 2</option>
                    <option value="3" {{ old('tingkat') == '3' ? 'selected' : '' }}>SP 3</option>
                </select>
            </div>

            <!-- Tanggal Terbit -->
            <div>
                <label for="tanggal_terbit" class="block text-sm font-medium text-gray-700 mb-1">Tanggal terbit</label>
                <input type="date" name="tanggal_terbit" id="tanggal_terbit" value="{{ old('tanggal_terbit', date('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500 py-2.5">
                <p class="text-xs text-gray-400 mt-1">SP akan otomatis berlaku selama 3 bulan sejak tanggal ini.</p>
            </div>

            <!-- Alasan Pelanggaran -->
            <div>
                <label for="alasan" class="block text-sm font-medium text-gray-700 mb-1">Alasan pelanggaran</label>
                <textarea name="alasan" id="alasan" rows="3" required class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">{{ old('alasan') }}</textarea>
            </div>

            <!-- Konsekuensi -->
            <div>
                <label for="konsekuensi" class="block text-sm font-medium text-gray-700 mb-1">Konsekuensi (opsional)</label>
                <textarea name="konsekuensi" id="konsekuensi" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">{{ old('konsekuensi') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 text-white font-semibold text-sm rounded-lg shadow-sm transition" style="background-color: #07B151;">
                    Terbitkan SP
                </button>
            </div>
        </form>
    </div>
</div>
@endsection