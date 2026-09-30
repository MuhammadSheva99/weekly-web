@extends('layouts.hrd')

@section('title', 'Data Karyawan')

@section('content')
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Data Karyawan</h1>
            <p class="text-gray-500 mt-1">Management Data Karyawan</p>
        </div>
        <a href="{{ route('hrd.karyawan.create') }}"
           class="inline-block px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-lg transition">
            + Tambah Karyawan
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="GET" class="mb-6">
        <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Divisi</option>
            @foreach ($divisiList as $divisi)
                <option value="{{ $divisi->id }}" @selected(request('divisi_id') == $divisi->id)>{{ $divisi->nama }}</option>
            @endforeach
        </select>
    </form>

    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Nama</th>
                    <th class="py-3 pr-4">Divisi</th>
                    <th class="py-3 pr-4">Email</th>
                    <th class="py-3 pr-4"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawan as $row)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $row->nama }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->divisi->nama ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->email ?? '-' }}</td>
                        <td class="py-3 pr-4">
                            <div class="flex gap-2">
                                <a href="{{ route('hrd.karyawan.edit', $row) }}"
                                   class="inline-block px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('hrd.karyawan.destroy', $row) }}"
                                      onsubmit="return confirm('Hapus {{ $row->nama }} dari data karyawan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 border border-red-300 text-red-600 rounded text-xs hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-gray-400">Belum ada karyawan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection