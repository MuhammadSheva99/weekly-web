@extends('layouts.atasan')

@section('title', 'Weekly Commitment')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Weekly Commitment</h1>
    <p class="text-gray-500 mt-1 mb-8">{{ now()->translatedFormat('l - d F Y') }}</p>

    <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center">
        <p class="text-gray-600">Belum ada Target Bulanan/Mingguan yang di-assign untuk kamu bulan ini.</p>
        <p class="text-sm text-gray-400 mt-2">Hubungi Admin untuk mengatur Target Bulanan dan KPI kamu terlebih dahulu.</p>
    </div>
@endsection