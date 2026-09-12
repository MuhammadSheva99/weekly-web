@extends('layouts.karyawan')
@section('title', 'Self Review')
@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Self Review</h1>
    <p class="text-gray-500 mt-1 mb-8">{{ now()->translatedFormat('l, d F Y') }}</p>
    <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center">
        <p class="text-gray-600">Kamu belum mengisi Weekly Progress minggu ini. Self Review baru bisa diisi setelah Progress tersedia.</p>
        <a href="{{ route('karyawan.weekly-progress.create') }}" class="inline-block mt-4 px-6 py-2.5 bg-amber-500 text-white rounded-lg text-sm font-semibold hover:bg-amber-600">
            Isi Weekly Progress dulu
        </a>
    </div>
@endsection