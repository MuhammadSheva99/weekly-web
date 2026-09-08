@extends('layouts.hrd')

@section('title', 'Notifikasi')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Notifikasi</h1>
    <p class="text-gray-500 mt-1 mb-8">Pemberitahuan karyawan mengisi Weekly Commitment</p>

    <div class="space-y-3">
        @forelse ($notifications as $notif)
            <div class="flex items-start gap-4 p-5 rounded-xl {{ $notif->is_read ? 'bg-white border border-gray-200' : 'bg-blue-50' }}">
                <div class="w-10 h-10 rounded-full bg-blue-700 text-white flex items-center justify-center font-semibold shrink-0">
                    ✓
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-gray-900">{{ $notif->message }}</p>
                        <span class="text-sm text-gray-400 shrink-0 ml-4">{{ $notif->sent_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Belum ada notifikasi.
            </div>
        @endforelse
    </div>
@endsection