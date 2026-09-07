@extends('layouts.admin')

@section('title', 'Divisi & Role')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Divisi & Role</h1>
    <p class="text-gray-500 mt-1 mb-8">Struktur organisasi dan tingkatan akses sistem</p>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="bg-orange-50 rounded-2xl p-8 mb-10">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Tambah Divisi Baru</h2>

        <form method="POST" action="{{ route('admin.divisi-role.store') }}" class="flex gap-4 items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-sm text-gray-600 mb-1">Nama Divisi</label>
                <input type="text" name="nama" placeholder="Contoh: Customer Service" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-lg transition">
                Tambah
            </button>
        </form>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Divisi</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
        @forelse ($divisiList as $divisi)
            <a href="{{ route('admin.divisi-role.show', $divisi) }}"
            class="block bg-white border border-gray-200 rounded-xl p-5 hover:border-orange-400 hover:shadow-sm transition">
                <p class="font-semibold text-gray-900">{{ $divisi->nama }}</p>
                <p class="text-sm text-gray-400 mt-1">{{ $divisi->users_count }} anggota · {{ $divisi->head_count }} Head</p>
            </a>
        @empty
            <p class="text-gray-400 col-span-3">Belum ada divisi.</p>
        @endforelse
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Role Sistem</h2>
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        @foreach ($roleDescriptions as $role => $desc)
            <div class="flex px-6 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <div class="w-48 font-semibold text-gray-900 shrink-0">{{ $role }}</div>
                <div class="text-gray-500">{{ $desc }}</div>
            </div>
        @endforeach
    </div>
@endsection