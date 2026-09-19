@extends('layouts.hrd')

@section('title', 'Surat Peringatan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Surat Peringatan</h1>
    <p class="text-gray-500 mt-1 mb-6">Peraturan perusahaan dan riwayat pelanggaran karyawan</p>

    @include('hrd.surat-peringatan._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-blue-50 rounded-2xl p-6 mb-10">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Terbitkan SP Baru</h2>
        <form method="POST" action="{{ route('hrd.surat-peringatan.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-600 mb-1">Karyawan</label>
                <select name="user_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                    <option value="">Pilih Karyawan</option>
                    @foreach ($allUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }} ({{ $u->divisi->nama ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Level SP</label>
                <select name="level" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                    <option value="">Pilih Level</option>
                    <option value="SP1">SP1</option>
                    <option value="SP2">SP2</option>
                    <option value="SP3">SP3</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Tanggal terbit</label>
                <input type="date" name="tanggal_terbit" required value="{{ now()->format('Y-m-d') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            </div>
            <div></div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">Alasan / kronologi pelanggaran</label>
                <textarea name="alasan" rows="3" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">Konsekuensi</label>
                <textarea name="konsekuensi" rows="2" placeholder="Contoh: Pemutusan hubungan kerja jika terjadi pelanggaran serupa"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm"></textarea>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg text-sm">
                    Terbitkan SP
                </button>
            </div>
        </form>
    </div>

    <div class="flex gap-3 mb-6">
        <form method="GET" class="flex gap-3">
            <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Semua Divisi</option>
                @foreach ($divisiList as $divisi)
                    <option value="{{ $divisi->id }}" @selected(request('divisi_id') == $divisi->id)>{{ $divisi->nama }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="berakhir" @selected(request('status') === 'berakhir')>Sudah Berakhir</option>
            </select>
        </form>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Karyawan</th>
                    <th class="py-3 pr-4">Level</th>
                    <th class="py-3 pr-4">Tanggal Terbit</th>
                    <th class="py-3 pr-4">Berakhir</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 pr-4">Diterbitkan oleh</th>
                    <th class="py-3 pr-4"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftar as $sp)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $sp->user->nama }}</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-700">{{ $sp->level }}</span>
                        </td>
                        <td class="py-3 pr-4 text-gray-600">{{ $sp->tanggal_terbit->translatedFormat('d M Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $sp->tanggal_berakhir->translatedFormat('d M Y') }}</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $sp->isAktif() ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $sp->isAktif() ? 'Aktif' : 'Berakhir' }}
                            </span>
                        </td>
                        <td class="py-3 pr-4 text-gray-600">{{ $sp->diterbitkanOleh->nama }}</td>
                        <td class="py-3 pr-4 whitespace-nowrap">
                            @if ($sp->file_path)
                                <a href="{{ route('hrd.surat-peringatan.download', $sp) }}" class="inline-block px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50 mr-2">
                                    Unduh
                                </a>
                            @endif
                            <form method="POST" action="{{ route('hrd.surat-peringatan.destroy', $sp) }}" class="inline" onsubmit="return confirm('Yakin hapus SP ini? Data dan file surat akan hilang permanen.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 border border-red-300 text-red-600 rounded text-xs hover:bg-red-50">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-6 text-center text-gray-400">Belum ada SP diterbitkan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection