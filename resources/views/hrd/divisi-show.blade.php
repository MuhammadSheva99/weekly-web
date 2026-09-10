@extends('layouts.hrd')

@section('title', $divisi->nama)

@section('content')
    <a href="{{ route('hrd.dashboard') }}" class="text-sm text-gray-500 hover:underline">← Kembali ke Dashboard</a>

    <h1 class="text-3xl font-bold text-gray-900 mt-3">{{ $divisi->nama }}</h1>
    <p class="text-gray-500 mt-1 mb-8">{{ $picList->count() }} PIC · Achievement bulan berjalan</p>

    <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        @forelse ($picList as $user)
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-blue-700 text-white flex items-center justify-center font-semibold shrink-0">
                    {{ strtoupper(substr($user->nama, 0, 2)) }}
                </div>
                <div class="w-40 font-medium text-gray-800">{{ $user->nama }}</div>
                <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full {{ $user->achievement !== null && $user->achievement < 80 ? 'bg-red-600' : 'bg-blue-600' }}"
                         style="width: {{ $user->achievement ?? 0 }}%"></div>
                </div>
                <div class="w-14 text-right font-semibold text-gray-900">
                    {{ $user->achievement !== null ? $user->achievement.'%' : '-' }}
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-sm">Belum ada PIC di divisi ini.</p>
        @endforelse
    </div>
@endsection