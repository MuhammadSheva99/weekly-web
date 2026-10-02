@extends('layouts.atasan')

@section('title', 'Rekomendasikan SP')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Surat Peringatan</h1>
    <p class="text-gray-500 mt-1 mb-6">Peraturan perusahaan dan riwayat pelanggaran tim</p>

    @include('atasan.surat-peringatan._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Rekomendasikan Surat Peringatan</h2>
    <p class="text-sm text-gray-500 mb-4 -mt-2">Rekomendasi akan dikirim ke HRD. HRD yang akan menerbitkan SP resminya.</p>

    <form method="POST" action="{{ route('atasan.surat-peringatan.rekomendasi.store') }}" enctype="multipart/form-data" class="space-y-5 max-w-xl">
        @csrf

        <div>
            <label class="block text-sm text-gray-600 mb-1">Anggota Divisi</label>
            <select name="user_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                <option value="">Pilih anggota</option>
                @foreach ($anggotaTim as $anggota)
                    <option value="{{ $anggota->id }}" {{ old('user_id') == $anggota->id ? 'selected' : '' }}>
                        {{ $anggota->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Level SP yang diusulkan</label>
            <select name="level_usulan" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                <option value="">Pilih level</option>
                <option value="SP1" {{ old('level_usulan') === 'SP1' ? 'selected' : '' }}>SP1</option>
                <option value="SP2" {{ old('level_usulan') === 'SP2' ? 'selected' : '' }}>SP2</option>
                <option value="SP3" {{ old('level_usulan') === 'SP3' ? 'selected' : '' }}>SP3</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Alasan / kronologi pelanggaran</label>
            <textarea name="alasan" rows="4" required
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">{{ old('alasan') }}</textarea>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Lampiran bukti (opsional)</label>
            <input type="file" name="lampiran" class="w-full text-sm">
            <p class="text-xs text-gray-400 mt-1">Maks. 5 MB.</p>
        </div>

        <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-3 rounded-lg transition">
            Kirim Rekomendasi ke HRD
        </button>
    </form>
@endsection