@extends('layouts.management')

@section('title', 'Data Karyawan')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Data Karyawan</h1>
        <p class="text-gray-500 mt-1">Management Data Karyawan</p>
    </div>

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
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawan as $row)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $row->nama }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->divisi->nama ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $row->email ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-6 text-center text-gray-400">Belum ada karyawan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection