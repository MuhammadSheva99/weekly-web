@extends('layouts.admin')

@section('title', 'Assign Target KPI')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Assign Target KPI</h1>
    <p class="text-gray-500 mt-1 mb-8">Tetapkan target bulanan per divisi atau per orang</p>

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

    <div x-data="{ mode: 'divisi' }" class="mb-8">
        <div class="flex gap-3 mb-6">
            <button type="button" @click="mode = 'divisi'"
                    :class="mode === 'divisi' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600'"
                    class="px-5 py-2 rounded-lg text-sm font-medium">Assign per Divisi</button>
            <button type="button" @click="mode = 'individu'"
                    :class="mode === 'individu' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600'"
                    class="px-5 py-2 rounded-lg text-sm font-medium">Assign per Orang</button>
        </div>

        {{-- MODE PER DIVISI --}}
        <div x-show="mode === 'divisi'">
            <form method="GET" class="flex gap-3 mb-6">
                <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                    <option value="">Pilih Divisi</option>
                    @foreach ($divisiList as $divisi)
                        <option value="{{ $divisi->id }}" @selected($selectedDivisiId == $divisi->id)>{{ $divisi->nama }}</option>
                    @endforeach
                </select>
                <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
                       class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            </form>

            @if ($selectedDivisiId)
                @if ($kpiDivisi->isEmpty())
                    <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center text-gray-400">
                        Divisi ini belum punya KPI. Tambahkan dulu di halaman KPI Master.
                    </div>
                @else
                    <div class="bg-orange-50 rounded-xl p-4 mb-4 text-sm text-gray-700">
                        Target ini akan diterapkan ke <strong>{{ $userDivisi->count() }} orang</strong> di divisi {{ $divisiList->firstWhere('id', $selectedDivisiId)->nama }}: {{ $userDivisi->pluck('nama')->join(', ') }}
                    </div>

                    <form method="POST" action="{{ route('admin.assign-kpi.store-divisi') }}">
                        @csrf
                        <input type="hidden" name="periode" value="{{ $periode }}">
                        <input type="hidden" name="divisi_id" value="{{ $selectedDivisiId }}">

                        <div class="bg-white rounded-2xl overflow-hidden mb-4">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 border-b">
                                        <th class="py-3 pr-4 pl-4">Nama KPI</th>
                                        <th class="py-3 pr-4">Bobot (%)</th>
                                        <th class="py-3 pr-4">Target Angka</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kpiDivisi as $i => $kpi)
                                        <tr class="border-b">
                                            <td class="py-3 pr-4 pl-4 font-medium text-gray-900">
                                                {{ $kpi->nama_kpi }} <span class="text-xs text-gray-400">({{ $kpi->satuan }})</span>
                                                <input type="hidden" name="kpi[{{ $i }}][kpi_id]" value="{{ $kpi->id }}">
                                            </td>
                                            <td class="py-3 pr-4">
                                                <input type="number" name="kpi[{{ $i }}][bobot]" step="0.01" required value="{{ $kpi->prefill_bobot }}"
                                                    class="w-24 px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
                                            </td>
                                            <td class="py-3 pr-4">
                                                <input type="number" name="kpi[{{ $i }}][target]" step="0.01" required value="{{ $kpi->prefill_target }}"
                                                    class="w-40 px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg text-sm">
                            Assign ke Semua Karyawan Divisi Ini
                        </button>
                    </form>
                @endif
            @endif
        </div>

        {{-- MODE PER ORANG --}}
        <div x-show="mode === 'individu'" x-cloak>
            <form method="POST" action="{{ route('admin.assign-kpi.store-individu') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
                @csrf
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Periode</label>
                    <input type="month" name="periode" value="{{ $periode }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                </div>
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
                    <label class="block text-sm text-gray-600 mb-1">KPI</label>
                    <select name="kpi_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                        <option value="">Pilih KPI</option>
                        @foreach ($allKpi as $kpi)
                            <option value="{{ $kpi->id }}">{{ $kpi->nama_kpi }} ({{ $kpi->divisi->nama ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Bobot (%)</label>
                    <input type="number" name="bobot" step="0.01" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Target Angka</label>
                    <input type="number" name="target" step="0.01" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg text-sm">
                        Assign ke Orang Ini
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection