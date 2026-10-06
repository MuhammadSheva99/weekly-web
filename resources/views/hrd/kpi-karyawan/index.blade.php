@extends('layouts.hrd')

@section('title', 'KPI Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">KPI Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-8">Semua karyawan yang memiliki KPI</p>

    @include('partials.kpi.daftar', [
        'daftar' => $daftar,
        'periode' => $periode,
        'cari' => $cari,
        'divisiList' => $divisiList,
        'divisiId' => $divisiId,
        'routeDetail' => 'hrd.kpi-karyawan.show',
    ])
@endsection