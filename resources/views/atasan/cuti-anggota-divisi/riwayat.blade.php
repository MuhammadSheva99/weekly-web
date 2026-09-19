@extends('layouts.atasan')

@section('title', 'Riwayat Cuti Anggota Divisi')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Cuti Anggota Divisi</h1>
    <p class="text-gray-500 mt-1 mb-6">Monitoring dan approval cuti anggota divisi</p>

    @include('atasan.cuti-anggota-divisi._tabs')

    <div class="space-y-3">
        @forelse ($data as $item)
            <div class="flex items-center gap-3 py-3 px-4 bg-white border border-gray-200 rounded-xl">
                <div class="w-9 h-9 rounded-full bg-red-700 text-white flex items-center justify-center text-xs font-semibold">
                    {{ strtoupper(substr($item->user->nama, 0, 2)) }}
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ $item->user->nama }}</p>
                    <p class="text-xs text-gray-400">{{ $item->user->divisi->nama ?? '-' }}</p>
                </div>
                <a href="{{ route('atasan.cuti-anggota-divisi.riwayat.detail', $item->user) }}" class="px-4 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                    Detail
                </a>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Belum ada anggota tim.
            </div>
        @endforelse
    </div>
@endsection