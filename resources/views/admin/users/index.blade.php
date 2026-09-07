@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Manajemen User</h1>
    <p class="text-gray-500 mt-1 mb-8">Menetapkan role, divisi, dan atasan untuk setiap akun</p>

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

    <div class="bg-orange-50 rounded-2xl p-8 mb-10">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Tambah User Baru</h2>

        <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @csrf

            <div>
                <label class="block text-sm text-gray-600 mb-1">Nama</label>
                <input type="text" name="nama" placeholder="Nama lengkap" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Email</label>
                <input type="email" name="email" placeholder="nama@perusahaan.com" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Role</label>
                <select name="role_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">Pilih Role</option>
                    @foreach ($roleList as $role)
                        <option value="{{ $role->id }}">{{ $role->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Divisi</label>
                <select name="divisi_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">- Tidak ada -</option>
                    @foreach ($divisiList as $divisi)
                        <option value="{{ $divisi->id }}">{{ $divisi->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Atasan Langsung</label>
                <select name="atasan_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">- Tidak ada -</option>
                    @foreach ($atasanOptions as $opt)
                        <option value="{{ $opt->id }}">{{ $opt->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 rounded-lg transition">
                    Simpan User
                </button>
            </div>
        </form>
    </div>

    <form method="GET" class="flex gap-4 mb-6">
        <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Divisi</option>
            @foreach ($divisiList as $divisi)
                <option value="{{ $divisi->id }}" @selected(request('divisi_id') == $divisi->id)>{{ $divisi->nama }}</option>
            @endforeach
        </select>

        <select name="role_id" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Role</option>
            @foreach ($roleList as $role)
                <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>{{ $role->nama }}</option>
            @endforeach
        </select>
    </form>

    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Nama</th>
                    <th class="py-3 pr-4">Email</th>
                    <th class="py-3 pr-4">Role</th>
                    <th class="py-3 pr-4">Divisi</th>
                    <th class="py-3 pr-4">Atasan</th>
                    <th class="py-3 pr-4"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $user->nama }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $user->email }}</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ $user->role->nama }}</span>
                        </td>
                        <td class="py-3 pr-4 text-gray-600">{{ $user->divisi->nama ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $user->atasan->nama ?? '-' }}</td>
                        <td class="py-3 pr-4 whitespace-nowrap">
                            <button type="button" onclick="document.getElementById('edit-{{ $user->id }}').classList.toggle('hidden')"
                                    class="px-3 py-1 border border-gray-300 rounded text-xs mr-2 hover:bg-gray-50">Edit</button>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1 border border-red-300 text-red-600 rounded text-xs hover:bg-red-50">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <tr id="edit-{{ $user->id }}" class="hidden">
                        <td colspan="6" class="bg-gray-50 p-6">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @csrf @method('PUT')
                                <input type="text" name="nama" value="{{ $user->nama }}" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <input type="email" name="email" value="{{ $user->email }}" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <select name="role_id" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    @foreach ($roleList as $role)
                                        <option value="{{ $role->id }}" @selected($user->role_id === $role->id)>{{ $role->nama }}</option>
                                    @endforeach
                                </select>
                                <select name="divisi_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="">- Tidak ada -</option>
                                    @foreach ($divisiList as $divisi)
                                        <option value="{{ $divisi->id }}" @selected($user->divisi_id === $divisi->id)>{{ $divisi->nama }}</option>
                                    @endforeach
                                </select>
                                <select name="atasan_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="">- Tidak ada -</option>
                                    @foreach ($atasanOptions as $opt)
                                        @if ($opt->id !== $user->id)
                                            <option value="{{ $opt->id }}" @selected($user->atasan_id === $opt->id)>{{ $opt->nama }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium py-2 rounded-lg">Simpan</button>
                                    <button type="button" onclick="document.getElementById('edit-{{ $user->id }}').classList.add('hidden')" class="flex-1 bg-gray-200 hover:bg-gray-300 text-sm font-medium py-2 rounded-lg">Batal</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-gray-400">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection