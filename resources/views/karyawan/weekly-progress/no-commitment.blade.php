@extends('layouts.karyawan')
@section('title', 'Weekly Progress')
@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Weekly Progress</h1>
    <p class="text-gray-500 mt-1 mb-8">{{ now()->translatedFormat('l, d F Y') }}</p>
    <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center">
        <p class="text-gray-600">Kamu belum mengisi Weekly Commitment minggu ini.</p>
        <a href="{{ route('karyawan.weekly-commitment.create') }}" class="inline-block mt-4 px-6 py-2.5 bg-amber-500 text-white rounded-lg text-sm font-semibold hover:bg-amber-600">
            Isi Weekly Commitment dulu
        </a>
    </div>
@endsection