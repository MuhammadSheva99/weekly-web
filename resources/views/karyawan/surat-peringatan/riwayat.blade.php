@extends('layouts.karyawan')

@section('title', 'Surat Peringatan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Surat Peringatan</h1>
    <p class="text-gray-500 mt-1 mb-6">Peraturan perusahaan, konsekuensi pelanggaran, dan riwayat SP Anda</p>

    @include('karyawan.surat-peringatan._tabs')

    @if ($daftar->isEmpty())
        <div class="bg-green-50 border border-green-100 rounded-2xl p-10 text-center">
            <p class="text-green-700 font-medium">Tidak ada SP aktif untuk anda</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-gray-100">
                        <th class="font-normal pb-3 pr-4">NO.SP</th>
                        <th class="font-normal pb-3 pr-4">Jenis</th>
                        <th class="font-normal pb-3 pr-4">Tanggal Terbit</th>
                        <th class="font-normal pb-3 pr-4">Berlaku Sampai</th>
                        <th class="font-normal pb-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftar as $sp)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-4 pr-4 text-gray-900">{{ $sp->no_sp }}</td>
                            <td class="py-4 pr-4">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-600">
                                    {{ $sp->level }}
                                </span>
                            </td>
                            <td class="py-4 pr-4 text-gray-700">{{ $sp->tanggal_terbit->translatedFormat('d F Y') }}</td>
                            <td class="py-4 pr-4 text-gray-700">
                                {{ $sp->tanggal_berakhir ? $sp->tanggal_berakhir->translatedFormat('d F Y') : '-' }}
                            </td>
                            <td class="py-4 text-right">
                                @if ($sp->file_path)
                                    <a href="{{ route('karyawan.surat-peringatan.download', $sp) }}"
                                       target="_blank"
                                       class="inline-block px-4 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                                        Lihat Surat
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection