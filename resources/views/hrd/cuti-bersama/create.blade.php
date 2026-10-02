@extends('layouts.hrd')

@section('title', 'Cuti Bersama')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Cuti Bersama</h1>
    <p class="text-gray-500 mt-1 mb-8">Terapkan cuti bersama sekaligus ke banyak karyawan - otomatis tercatat &amp; motong kuota cuti masing-masing.</p>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.cuti-bersama.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" required
                           value="{{ old('tanggal_mulai') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" required
                           value="{{ old('tanggal_selesai') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Hari (motong kuota)</label>
                <input type="number" name="jumlah_hari" step="0.5" min="0.5" required
                       value="{{ old('jumlah_hari') }}"
                       class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <p class="text-xs text-gray-400 mt-1">Jumlah hari yang akan memotong kuota cuti tahunan tiap orang.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan</label>
                <input type="text" name="keterangan" required
                       value="{{ old('keterangan') }}"
                       placeholder="Misal: Cuti Bersama Hari Raya 2026"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Berlaku untuk Role</label>
                <div class="space-y-2">
                    @foreach ($roleTersedia as $role)
                        <label class="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50 cursor-pointer">
                            <input type="checkbox" name="roles[]" value="{{ $role }}"
                                   {{ in_array($role, old('roles', [])) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">{{ $role }}</span>
                            <span class="text-xs text-gray-400 ml-auto">{{ $jumlahPerRole[$role] ?? 0 }} orang</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition">
                Terapkan Cuti Bersama
            </button>
        </form>
    </div>
@endsection