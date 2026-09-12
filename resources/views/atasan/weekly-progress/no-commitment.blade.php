@extends('layouts.atasan')
@section('title', 'Weekly Progress')
@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Weekly Progress</h1>
    <p class="text-gray-500 mt-1 mb-8">{{ now()->translatedFormat('l, d F Y') }}</p>
    <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center">
        <p class="text-gray-600">Kamu belum mengisi Weekly Commitment minggu ini.</p>
        <a href="{{ route('atasan.weekly-commitment.create') }}" class="inline-block mt-4 px-6 py-2.5 bg-red-700 text-white rounded-lg text-sm font-semibold hover:bg-red-800">
            Isi Weekly Commitment dulu
        </a>
    </div>
@endsection