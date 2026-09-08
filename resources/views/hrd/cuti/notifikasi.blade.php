@extends('layouts.hrd')

@section('title', 'Notifikasi Cuti')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Cuti</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan saldo cuti - Tahun {{ now()->year }}</p>

    @include('hrd.cuti._tabs')

    <div class="space-y-3">
        @forelse ($notifications as $notif)
            <div class="flex items-start justify-between gap-4 p-5 rounded-xl {{ $notif->is_read ? 'bg-white border border-gray-200' : 'bg-blue-50' }}">
                <p class="text-gray-800">{{ $notif->message }}</p>
                <span class="text-sm text-gray-400 shrink-0">{{ $notif->sent_at->diffForHumans() }}</span>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Belum ada notifikasi cuti.
            </div>
        @endforelse
    </div>
@endsection