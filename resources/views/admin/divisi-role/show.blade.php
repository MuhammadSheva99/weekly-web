@extends('layouts.admin')

@section('title', 'Detail Divisi')

@section('content')
    <a href="{{ route('admin.divisi-role.index') }}" class="text-sm text-gray-500 hover:underline">← Kembali ke Divisi & Role</a>

    <h1 class="text-3xl font-bold text-gray-900 mt-3">{{ $divisi->nama }}</h1>
    <p class="text-gray-500 mt-1 mb-8">{{ $divisi->users->count() }} anggota terdaftar</p>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    @if (session('error'))
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="bg-orange-50 rounded-2xl p-8 mb-10">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Edit Divisi</h2>

        <div class="flex gap-4">
            <form method="POST" action="{{ route('admin.divisi-role.update', $divisi) }}" class="flex-1 flex gap-4">
                @csrf @method('PUT')
                <input type="text" name="nama" value="{{ $divisi->nama }}" required
                       class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-lg transition">
                    Simpan
                </button>
            </form>

            <form method="POST" action="{{ route('admin.divisi-role.destroy', $divisi) }}"
                  onsubmit="return confirm('Yakin hapus divisi {{ $divisi->nama }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="border border-red-300 text-red-600 hover:bg-red-50 font-semibold px-6 py-2.5 rounded-lg transition">
                    Hapus Divisi
                </button>
            </form>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Atasan</h2>
    <div class="bg-white rounded-2xl p-6 mb-10">
        @forelse ($atasanList as $atasan)
            <div class="flex items-center gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-gray-100' : '' }}">
                <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-semibold">
                    {{ strtoupper(substr($atasan->nama, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ $atasan->nama }}</p>
                    <p class="text-sm text-gray-500">{{ $atasan->divisi->nama ?? '-' }}</p>
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-sm">Belum ada Atasan untuk divisi ini.</p>
        @endforelse
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Anggota</h2>
    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Nama</th>
                    <th class="py-3 pr-4">Email</th>
                    <th class="py-3 pr-4">Role</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($divisi->users as $user)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $user->nama }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $user->email }}</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ $user->role->nama }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-6 text-center text-gray-400">Belum ada anggota di divisi ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection