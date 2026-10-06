{{--
    Isi halaman detail KPI satu orang.
    Param: $data (hasil App\Support\KpiTabel), $periode ("Y-m"), $kembali (url), $excelUrl (url)
--}}
@php
    $fmt = fn ($n, $desimal = 1) => rtrim(rtrim(number_format((float) $n, $desimal, ',', '.'), '0'), ',');
@endphp

<a href="{{ $kembali }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-800 mb-4">&larr; Kembali ke daftar</a>

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $data->user->nama ?? '-' }}</h1>
        <p class="text-gray-500 mt-1">{{ $data->user->divisi->nama ?? '-' }} &middot; Detail KPI</p>
    </div>
    <a href="{{ $excelUrl }}"
       style="background:#16a34a;color:#ffffff;display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none"
       onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        Download Excel
    </a>
</div>

<form method="GET" class="flex items-center gap-3 mb-6">
    <label class="text-sm text-gray-500">Periode</label>
    <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
           class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
</form>

@if ($data->skor_total !== null)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5">
            <p class="text-xs text-gray-500">Skor KPI bulan ini</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $fmt($data->skor_total) }}%</p>
        </div>
        <div class="bg-white rounded-2xl p-5">
            <p class="text-xs text-gray-500">Status</p>
            <p class="text-xl font-bold text-gray-900 mt-2">{{ $data->status }} <span class="text-gray-400 font-normal">(Grade {{ $data->grade }})</span></p>
        </div>
        <div class="bg-white rounded-2xl p-5">
            <p class="text-xs text-gray-500">KPI terisi</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $data->terisi }}<span class="text-lg text-gray-400">/{{ $data->jumlah }}</span></p>
        </div>
    </div>
@endif

<div class="bg-white rounded-2xl overflow-hidden">
    @include('partials.kpi.tabel', ['d' => $data])
</div>
<p class="text-xs text-gray-400 mt-3">Skor dihitung dari pencapaian tiap KPI dikalikan bobotnya. Realisasi terisi setelah Self Review mingguan disimpan.</p>