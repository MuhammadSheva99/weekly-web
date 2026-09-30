@extends('layouts.hrd')

@section('title', 'Edit Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Edit Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-8">Data Karyawan</p>

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hrd.karyawan.update', $karyawan) }}" class="max-w-lg">
        @csrf
        @method('PUT')

        <label class="block text-sm text-gray-500 mb-2">Nama</label>
        <input type="text" name="nama" required value="{{ old('nama', $karyawan->nama) }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-blue-400">

        <label class="block text-sm text-gray-500 mb-2">Divisi</label>
        <select name="divisi_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">- Pilih Divisi -</option>
            @foreach ($divisiList as $divisi)
                <option value="{{ $divisi->id }}" @selected(old('divisi_id', $karyawan->divisi_id) == $divisi->id)>{{ $divisi->nama }}</option>
            @endforeach
        </select>

        <label class="block text-sm text-gray-500 mb-2">Email</label>
        <input type="email" name="email" value="{{ old('email', $karyawan->email) }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-blue-400">

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-3 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('hrd.karyawan.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                Batal
            </a>
        </div>
    </form>
@endsection