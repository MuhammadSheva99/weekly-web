@extends('layouts.management')

@section('title', 'History Weekly')

@section('content')
@if (($mode ?? 'daftar') === 'detail')
    @php
        $fmt = function ($satuan, $nilai) {
            if ($nilai === null || $nilai === '') {
                return '-';
            }
            return $satuan
                ? \App\Support\KpiFormat::tampil($nilai, $satuan)
                : number_format((float) $nilai, 0, ',', '.');
        };
        $th = 'padding:12px 20px;';
        $td = 'padding:12px 20px;';
    @endphp

    <a href="{{ $kembali }}" class="text-sm font-semibold hover:underline" style="color:#07B151;">&larr; Kembali</a>
    <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $pic->nama }} &middot; {{ $label }}</h1>
    <p class="text-gray-500 mt-1 mb-8">Weekly Commitment, Progress, Self Review, dan Feedback.</p>

    <style>
        .hw-acc-head{display:flex;align-items:center;justify-content:space-between;cursor:pointer;list-style:none;padding:14px 20px;background:#fff;border:1px solid #e5e7eb;border-radius:12px;}
        .hw-acc-head::-webkit-details-marker{display:none;}
        .hw-acc-chev{color:#6b7280;transition:transform .15s;}
        .hw-acc[open] > .hw-acc-head{border-bottom-left-radius:0;border-bottom-right-radius:0;}
        .hw-acc[open] .hw-acc-chev{transform:rotate(180deg);}
    </style>

    {{-- 1. WEEKLY COMMITMENT --}}
    <details class="hw-acc" open style="margin-bottom:16px;">
        <summary class="hw-acc-head">
            <span class="text-lg font-semibold text-gray-800">1. Weekly Commitment</span>
            <span class="hw-acc-chev">&#9662;</span>
        </summary>
        <div style="padding-top:12px;">
            <div class="bg-white border border-gray-200 rounded-xl mb-8" style="padding:20px 24px;">
                <p style="font-size:12px;color:#6b7280;">Big Goal</p>
                <p class="text-gray-800 font-semibold">{{ $first->big_goal ?: '-' }}</p>

                @if (count($prioritas))
                    <p style="font-size:12px;color:#6b7280;margin-top:14px;">Prioritas</p>
                    <ol style="margin:4px 0 0 20px;list-style:decimal;" class="text-gray-700">
                        @foreach ($prioritas as $p)
                            <li>{{ $p }}</li>
                        @endforeach
                    </ol>
                @endif

                @if ($first->output_deliverable)
                    <p style="font-size:12px;color:#6b7280;margin-top:14px;">Output / Deliverable</p>
                    <p class="text-gray-700 whitespace-pre-line">{{ $first->output_deliverable }}</p>
                @endif

                @if ($first->submitted_at)
                    <p style="font-size:12px;color:#9ca3af;margin-top:14px;">Disubmit {{ \Illuminate\Support\Carbon::parse($first->submitted_at)->translatedFormat('d M Y, H:i') }}</p>
                @endif
            </div>

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-8" style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    <thead>
                        <tr style="background:#f0fdf4;text-align:left;">
                            <th style="{{ $th }}">KPI</th>
                            <th style="{{ $th }}">Target minggu ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($commitments as $c)
                            @php $kpi = $c->targetMingguan->targetBulanan->kpi ?? null; @endphp
                            <tr style="border-top:1px solid #f3f4f6;">
                                <td style="{{ $td }}font-weight:600;">{{ $kpi->nama_kpi ?? '-' }}</td>
                                <td style="{{ $td }}">{{ $fmt($kpi->satuan ?? null, $c->target) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </details>

    {{-- 2. WEEKLY PROGRESS --}}
    <details class="hw-acc" style="margin-bottom:16px;">
        <summary class="hw-acc-head">
            <span class="text-lg font-semibold text-gray-800">2. Weekly Progress</span>
            <span class="hw-acc-chev">&#9662;</span>
        </summary>
        <div style="padding-top:12px;">
            @if ($commitments->contains(fn ($c) => $c->weeklyProgress))
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4" style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                        <thead>
                            <tr style="background:#f0fdf4;text-align:left;">
                                <th style="{{ $th }}">KPI</th>
                                <th style="{{ $th }}">Target</th>
                                <th style="{{ $th }}">Actual sementara</th>
                                <th style="{{ $th }}">Pencapaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($commitments as $c)
                                @php
                                    $kpi = $c->targetMingguan->targetBulanan->kpi ?? null;
                                    $sat = $kpi->satuan ?? null;
                                    $pr = $c->weeklyProgress;
                                @endphp
                                <tr style="border-top:1px solid #f3f4f6;">
                                    <td style="{{ $td }}font-weight:600;">{{ $kpi->nama_kpi ?? '-' }}</td>
                                    <td style="{{ $td }}">{{ $fmt($sat, $c->target) }}</td>
                                    <td style="{{ $td }}">{{ $pr ? $fmt($sat, $pr->actual_sementara) : '-' }}</td>
                                    <td style="{{ $td }}">{{ $pr && $pr->achievement_pct !== null ? round($pr->achievement_pct).'%' : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($catatan && ($catatan->problem || $catatan->analysis || $catatan->solution || $catatan->action_plan))
                    <div class="bg-white border border-gray-200 rounded-xl mb-8" style="padding:20px 24px;">
                        @foreach (['problem' => 'Problem', 'analysis' => 'Analysis', 'solution' => 'Solution', 'action_plan' => 'Action Plan'] as $f => $judul)
                            @if ($catatan->{$f})
                                <p style="font-size:12px;color:#6b7280;margin-top:8px;">{{ $judul }}</p>
                                <p class="text-gray-700 whitespace-pre-line">{{ $catatan->{$f} }}</p>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div style="margin-bottom:32px;"></div>
                @endif
            @else
                <div class="bg-white border border-gray-200 rounded-xl px-6 py-6 text-gray-400 mb-8">Weekly Progress belum diisi.</div>
            @endif
        </div>
    </details>

    {{-- 3. SELF REVIEW --}}
    <details class="hw-acc" style="margin-bottom:16px;">
        <summary class="hw-acc-head">
            <span class="text-lg font-semibold text-gray-800">3. Self Review</span>
            <span class="hw-acc-chev">&#9662;</span>
        </summary>
        <div style="padding-top:12px;">
            @if ($commitments->contains(fn ($c) => $c->selfReview || $c->actualMingguan))
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4" style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                        <thead>
                            <tr style="background:#f0fdf4;text-align:left;">
                                <th style="{{ $th }}">KPI</th>
                                <th style="{{ $th }}">Target</th>
                                <th style="{{ $th }}">Actual final</th>
                                <th style="{{ $th }}">Pencapaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($commitments as $c)
                                @php
                                    $kpi = $c->targetMingguan->targetBulanan->kpi ?? null;
                                    $sat = $kpi->satuan ?? null;
                                    $final = $c->selfReview->actual ?? $c->actualMingguan->nilai_actual_final ?? null;
                                    $achFinal = $c->actualMingguan->achievement_pct ?? $c->selfReview->achievement_pct ?? null;
                                @endphp
                                <tr style="border-top:1px solid #f3f4f6;">
                                    <td style="{{ $td }}font-weight:600;">{{ $kpi->nama_kpi ?? '-' }}</td>
                                    <td style="{{ $td }}">{{ $fmt($sat, $c->target) }}</td>
                                    <td style="{{ $td }}">{{ $fmt($sat, $final) }}</td>
                                    <td style="{{ $td }}">{{ $achFinal !== null ? round($achFinal).'%' : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if (count($reviewCatatan))
                    <div class="bg-white border border-gray-200 rounded-xl mb-8" style="padding:20px 24px;">
                        @foreach ($reviewCatatan as $judul => $isi)
                            <p style="font-size:12px;color:#6b7280;margin-top:8px;">{{ $judul }}</p>
                            <p class="text-gray-700 whitespace-pre-line">{{ $isi }}</p>
                        @endforeach
                    </div>
                @else
                    <div style="margin-bottom:32px;"></div>
                @endif
            @else
                <div class="bg-white border border-gray-200 rounded-xl px-6 py-6 text-gray-400 mb-8">Self Review belum diisi.</div>
            @endif
        </div>
    </details>

    {{-- 4. FEEDBACK --}}
    <details class="hw-acc" style="margin-bottom:16px;">
        <summary class="hw-acc-head">
            <span class="text-lg font-semibold text-gray-800">4. Feedback</span>
            <span class="hw-acc-chev">&#9662;</span>
        </summary>
        <div style="padding-top:12px;">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4">
                @forelse ($komentar as $k)
                    <div class="px-6 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $k->pemberi }}
                                @if ($k->peran)
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">{{ $k->peran }}</span>
                                @endif
                            </p>
                            <span class="text-xs text-gray-400">{{ $k->waktu->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        @if ($k->kpi)
                            <p class="text-xs text-gray-500 mt-1">KPI: {{ $k->kpi }}</p>
                        @endif
                        <p class="text-gray-700 mt-2 whitespace-pre-line">{{ $k->isi }}</p>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400">Belum ada komentar.</div>
                @endforelse
            </div>
        </div>
    </details>
@else
    <h1 class="text-3xl font-bold text-gray-900">History Weekly</h1>
    <p class="text-gray-500 mt-1 mb-8">Riwayat weekly karyawan, satu baris per minggu</p>

    <form method="GET" class="flex gap-3 mb-8" style="flex-wrap:wrap;">
        <select name="user" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
            <option value="">Semua Karyawan</option>
            @foreach ($daftarUser as $u)
                <option value="{{ $u->id }}" @selected((string) $userId === (string) $u->id)>{{ $u->nama }}{{ (string) $u->id === (string) auth()->id() ? ' (Saya)' : '' }}</option>
            @endforeach
        </select>

        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
               class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">

        <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
            <option value="">Semua Status</option>
            <option value="selesai" @selected($status === 'selesai')>Selesai</option>
            <option value="berjalan" @selected($status === 'berjalan')>Berjalan</option>
        </select>
    </form>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100" style="overflow-x:auto;">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b uppercase text-xs bg-gray-50">
                    <th class="py-3 px-4">Nama</th>
                    <th class="py-3 pr-4">Minggu</th>
                    <th class="py-3 pr-4">Big Goal</th>
                    <th class="py-3 pr-4">KPI (Target &rarr; Actual)</th>
                    <th class="py-3 pr-4">Achievement</th>
                    <th class="py-3 pr-4">Feedback</th>
                    <th class="py-3 pr-4"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $r)
                    <tr class="border-b hover:bg-gray-50/50" style="vertical-align:top;">
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $r->nama }}</td>
                        <td class="py-3 pr-4 text-gray-700">{{ $r->minggu_label }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $r->big_goal }}</td>
                        <td class="py-3 pr-4 text-gray-600">
                            @foreach ($r->kpi as $k)
                                <div style="margin-bottom:6px;">
                                    <span class="font-medium text-gray-800">{{ $k->nama }}</span><br>
                                    <span style="font-size:13px;">{{ $k->target }} &rarr; {{ $k->actual }}</span>
                                </div>
                            @endforeach
                        </td>
                        <td class="py-3 pr-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ ! $r->is_final ? 'bg-gray-100 text-gray-500' : (($r->achievement ?? 0) >= 100 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700') }}">
                                {{ $r->status_label }}
                            </span>
                        </td>
                        <td class="py-3 pr-4 text-gray-600">{{ $r->jumlah_komentar ? $r->jumlah_komentar.' komentar' : '-' }}</td>
                        <td class="py-3 pr-4">
                            <a href="{{ route('management.history-weekly.show', [$r->user_id, $r->key]) }}"
                               style="font-size:14px;font-weight:600;color:#07B151;text-decoration:none;">Detail &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-400">Belum ada data weekly untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
@endsection