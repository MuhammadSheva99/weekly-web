@extends('layouts.atasan')

@section('title', 'Notifikasi')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Notifikasi</h1>
    <p class="text-gray-500 mt-1 mb-8">Alert dan pembaruan dari tim</p>

    <div class="space-y-3">
        @forelse ($notifications as $notif)
            @php
                $isAlert = in_array($notif->type, ['alert_underperform_anggota', 'alert_belum_submit_anggota']);
            @endphp
            <div class="flex items-start gap-4 p-5 rounded-xl {{ $isAlert ? 'bg-red-50' : 'bg-white border border-gray-200' }}">
                <div class="w-10 h-10 rounded-full bg-red-700 text-white flex items-center justify-center font-semibold shrink-0 text-sm">
                    {{ strtoupper(substr($notif->message, 0, 2)) }}
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900">{{ $notif->message }}</p>
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