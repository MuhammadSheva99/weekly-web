@extends('layouts.atasan')

@section('title', 'KPI Tim')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">KPI Tim</h1>
    <p class="text-gray-500 mt-1 mb-8">KPI semua bawahan kamu</p>

    @include('partials.kpi.daftar', [
        'daftar' => $daftar,
        'periode' => $periode,
        'cari' => $cari,
    ])
@endsection