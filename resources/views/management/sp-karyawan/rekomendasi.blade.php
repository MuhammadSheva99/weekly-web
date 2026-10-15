@extends('layouts.management')

@section('title', 'Rekomendasi SP')

@section('content')
<div class="max-w-6xl">
    @include('management.sp-karyawan._tabs')

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Rekomendasi SP dari Atasan</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase">
                        <th class="py-3 px-4">Tanggal Pengajuan</th>
                        <th class="py-3 px-4">Karyawan</th>
                        <th class="py-3 px-4">Divisi</th>
                        <th class="py-3 px-4">Pengaju (Atasan)</th>
                        <th class="py-3 px-4">Tingkat SP</th>
                        <th class="py-3 px-4">Alasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($rekomendasiList as $rek)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-4 px-4 text-gray-600">{{ \Carbon\Carbon::parse($rek->created_at)->format('d F Y') }}</td>
                            <td class="py-4 px-4 font-medium text-gray-800">{{ $rek->user->nama ?? '-' }}</td>
                            <td class="py-4 px-4 text-gray-600">{{ $rek->user->divisi->nama ?? '-' }}</td>
                            <td class="py-4 px-4 text-gray-600">{{ $rek->pengaju->nama ?? '-' }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 text-xs font-semibold text-red-700 bg-red-50 rounded-full">
                                    SP{{ $rek->tingkat }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-gray-600">{{ $rek->alasan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400 text-sm">
                                Belum ada rekomendasi SP dari atasan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection