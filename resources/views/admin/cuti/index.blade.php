@extends('layouts.admin')

@section('title', 'Kuota Cuti')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Kuota Cuti</h1>
    <p class="text-gray-500 mt-1 mb-8">Atur jatah &amp; sisa cuti Karyawan, Atasan, dan HRD</p>

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

    <form method="GET" class="mb-6">
        <select name="tahun" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            @foreach (range(now()->year, now()->year - 3) as $y)
                <option value="{{ $y }}" @selected($tahun == $y)>Tahun {{ $y }}</option>
            @endforeach
        </select>
    </form>

    <p class="text-xs text-gray-400 mb-4">
        "Terpakai Manual" = cuti yang sudah dipakai sebelum sistem ini dipakai (data lama).
        "Dari Sistem" = otomatis dari pengajuan cuti yang sudah disetujui lewat sistem ini.
        Sisa = Jatah + Carry-over − Manual − Dari Sistem.
    </p>

    {{--
        Tiap baris punya <form> tersendiri (id unik), tapi <form> TIDAK dipasang
        di dalam <tr>/<td> karena itu invalid HTML (browser akan "membetulkan"
        strukturnya dan form-nya lepas dari tabel, submit jadi tidak jalan).
        Sebagai gantinya: <form> kosong ditaruh sebelum <tr>, lalu input/button
        di dalam <td> menunjuk ke form itu lewat atribut form="...".
    --}}
    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4 pl-4">Nama</th>
                    <th class="py-3 pr-4">Role</th>
                    <th class="py-3 pr-4">Divisi</th>
                    <th class="py-3 pr-4">Jatah Tahun Ini</th>
                    <th class="py-3 pr-4">Carry-over</th>
                    <th class="py-3 pr-4">Terpakai Manual</th>
                    <th class="py-3 pr-4">Dari Sistem {{ $tahun }}</th>
                    <th class="py-3 pr-4">Sisa</th>
                    <th class="py-3 pr-4"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    @php $formId = 'form-cuti-'.$user->id; @endphp
                    <form id="{{ $formId }}" method="POST" action="{{ route('admin.cuti.update', $user) }}"></form>
                    <tr class="border-b">
                        <td class="py-3 pr-4 pl-4 font-medium text-gray-900">{{ $user->nama }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $user->role->nama ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $user->divisi->nama ?? '-' }}</td>
                        <td class="py-3 pr-4">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" form="{{ $formId }}">
                            <input type="hidden" name="_method" value="PUT" form="{{ $formId }}">
                            <input type="number" name="jatah_cuti_tahunan" step="0.5" min="0" required
                                   value="{{ old('jatah_cuti_tahunan', $user->jatah_cuti_tahunan) }}"
                                   form="{{ $formId }}"
                                   class="w-20 px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
                        </td>
                        <td class="py-3 pr-4">
                            <input type="number" name="sisa_cuti_tahun_lalu" step="0.5" min="0" required
                                   value="{{ old('sisa_cuti_tahun_lalu', $user->sisa_cuti_tahun_lalu) }}"
                                   form="{{ $formId }}"
                                   class="w-20 px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
                        </td>
                        <td class="py-3 pr-4">
                            <input type="number" name="cuti_terpakai_manual" step="0.5" min="0" required
                                   value="{{ old('cuti_terpakai_manual', $user->cuti_terpakai_manual) }}"
                                   form="{{ $formId }}"
                                   class="w-20 px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
                        </td>
                        <td class="py-3 pr-4 text-gray-600">{{ rtrim(rtrim(number_format($user->terpakai_sistem, 1), '0'), '.') }} hari</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $user->sisa_cuti > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ rtrim(rtrim(number_format($user->sisa_cuti, 1), '0'), '.') }} hari
                            </span>
                        </td>
                        <td class="py-3 pr-4">
                            <button type="submit" form="{{ $formId }}"
                                    class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-xs font-semibold">
                                Simpan
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="py-6 text-center text-gray-400">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection