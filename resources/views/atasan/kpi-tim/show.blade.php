@extends('layouts.atasan')

@section('title', 'Detail KPI')

@section('content')
    @include('partials.kpi.detail', [
        'data' => $data,
        'periode' => $periode,
        'kembali' => $kembali,
        'excelUrl' => $excelUrl,
    ])
@endsection