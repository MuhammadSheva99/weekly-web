@extends('layouts.hrd')

@section('title', 'SP Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Surat Peringatan Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Management SP Karyawan</p>

    @include('hrd.sp-karyawan._tabs')

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Terbitkan Surat Peringatan Baru</h2>

    <form method="POST" action="{{ route('hrd.sp-karyawan.store') }}" class="space-y-5 max-w-xl">
        @csrf

        <div>
            <label class="block text-sm text-gray-600 mb-1">Karyawan</label>
            <select name="user_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                <option value="">Pilih karyawan</option>
                @foreach ($allUsers as $u)
                    <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->nama }} @if($u->divisi) — {{ $u->divisi->nama }} @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Level SP</label>
            <select name="level" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                <option value="">Pilih level</option>
                <option value="SP1" {{ old('level') === 'SP1' ? 'selected' : '' }}>SP1</option>
                <option value="SP2" {{ old('level') === 'SP2' ? 'selected' : '' }}>SP2</option>
                <option value="SP3" {{ old('level') === 'SP3' ? 'selected' : '' }}>SP3</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Tanggal terbit</label>
            <input type="date" name="tanggal_terbit" required value="{{ old('tanggal_terbit', now()->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <p class="text-xs text-gray-400 mt-1">SP akan otomatis berlaku selama 3 bulan sejak tanggal ini.</p>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Alasan pelanggaran</label>
            <textarea name="alasan" rows="3" required
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">{{ old('alasan') }}</textarea>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Konsekuensi (opsional)</label>
            <textarea name="konsekuensi" rows="2"
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">{{ old('konsekuensi') }}</textarea>
        </div>

        <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition">
            Terbitkan SP
        </button>
    </form>
@endsection