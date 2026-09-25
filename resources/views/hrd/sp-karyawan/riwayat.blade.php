@extends('layouts.hrd')

@section('title', 'SP Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Surat Peringatan Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Management SP Karyawan</p>

    @include('hrd.sp-karyawan._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Pelanggaran Karyawan</h2>

    <form method="GET" class="mb-6 flex gap-3">
        <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Divisi</option>
            @foreach ($divisiList as $divisi)
                <option value="{{ $divisi->id }}" {{ request('divisi_id') == $divisi->id ? 'selected' : '' }}>
                    {{ $divisi->nama }}
                </option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="berakhir" {{ request('status') === 'berakhir' ? 'selected' : '' }}>Berakhir</option>
        </select>
    </form>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 border-b text-xs">
                    <th class="py-3 pr-4 pl-5">No.SP</th>
                    <th class="py-3 pr-4">Jenis</th>
                    <th class="py-3 pr-4">Tanggal Terbit</th>
                    <th class="py-3 pr-4">Berlaku Sampai</th>
                    <th class="py-3 pr-4">Nama Karyawan</th>
                    <th class="py-3 pr-4"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftar as $sp)
                    @php
                        $badgeColor = match ($sp->level) {
                            'SP1' => 'bg-orange-50 text-orange-600',
                            'SP2' => 'bg-orange-100 text-orange-700',
                            'SP3' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr class="border-b">
                        <td class="py-4 pr-4 pl-5 text-gray-700">{{ $sp->no_sp ?? '-' }}</td>
                        <td class="py-4 pr-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">{{ $sp->level }}</span>
                        </td>
                        <td class="py-4 pr-4 text-gray-700">{{ $sp->tanggal_terbit->translatedFormat('d F Y') }}</td>
                        <td class="py-4 pr-4 text-gray-700">{{ $sp->tanggal_berakhir->translatedFormat('d F Y') }}</td>
                        <td class="py-4 pr-4 text-gray-700">{{ $sp->user->nama }}</td>
                        <td class="py-4 pr-4 text-right">
                            @if ($sp->file_path)
                                <a href="{{ route('hrd.sp-karyawan.download', $sp) }}"
                                   class="inline-block px-4 py-1.5 border border-gray-300 rounded-lg text-xs font-medium hover:bg-gray-50">
                                    Lihat Surat
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">Dokumen belum tersedia</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-gray-400">Belum ada riwayat pelanggaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection