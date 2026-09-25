@extends('layouts.hrd')

@section('title', 'Riwayat Cuti Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Cuti Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Monitoring Cuti Karyawan</p>

    @include('hrd.cuti-karyawan._tabs')

    <form method="GET" class="mb-6 flex gap-3">
        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
               class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Divisi</option>
            @foreach ($divisiList as $divisi)
                <option value="{{ $divisi->id }}" {{ request('divisi_id') == $divisi->id ? 'selected' : '' }}>
                    {{ $divisi->nama }}
                </option>
            @endforeach
        </select>
    </form>

    <div class="space-y-3">
        @forelse ($data as $item)
            <div class="flex items-center gap-3 py-3 px-4 bg-white border border-gray-200 rounded-xl">
                <div class="w-9 h-9 rounded-full bg-blue-700 text-white flex items-center justify-center text-xs font-semibold">
                    {{ strtoupper(substr($item->user->nama, 0, 2)) }}
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ $item->user->nama }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $item->user->divisi->nama ?? '-' }} ·
                        Terpakai {{ $item->terpakai }} hari · Sisa {{ $item->sisa }} dari {{ $item->jatah }} hari/tahun
                    </p>
                </div>
                <a href="{{ route('hrd.cuti-karyawan.riwayat.detail', ['karyawan' => $item->user, 'periode' => $periode]) }}"
                   class="px-4 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                    Detail
                </a>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Belum ada data karyawan.
            </div>
        @endforelse
    </div>
@endsection