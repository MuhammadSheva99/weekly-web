@extends('layouts.karyawan')

@section('title', 'Detail Weekly')

@section('content')
    @php
        // Format nilai pakai helper proyek (satuan: %, Rupiah, Angka, dll).
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

    <a href="{{ url()->previous() === url()->current() ? route('karyawan.dashboard') : url()->previous() }}" style="font-size:14px;color:#b45309;text-decoration:none;">&larr; Kembali</a>
    <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $label }}</h1>
    <p class="text-gray-500 mt-1 mb-8">Weekly Commitment, Progress, Self Review, dan Feedback minggu ini.</p>

    {{-- 1. WEEKLY COMMITMENT --}}
    <h2 class="text-lg font-semibold text-gray-800 mb-3">1. Weekly Commitment</h2>
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
                <tr style="background:#fffbeb;text-align:left;">
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

    {{-- 2. WEEKLY PROGRESS --}}
    <h2 class="text-lg font-semibold text-gray-800 mb-3">2. Weekly Progress</h2>
    @if ($commitments->contains(fn ($c) => $c->weeklyProgress))
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4" style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <thead>
                    <tr style="background:#fffbeb;text-align:left;">
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

    {{-- 3. SELF REVIEW --}}
    <h2 class="text-lg font-semibold text-gray-800 mb-3">3. Self Review</h2>
    @if ($commitments->contains(fn ($c) => $c->selfReview || $c->actualMingguan))
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4" style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <thead>
                    <tr style="background:#fffbeb;text-align:left;">
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

    {{-- 4. FEEDBACK --}}
    <h2 class="text-lg font-semibold text-gray-800 mb-3">4. Feedback</h2>
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        @forelse ($komentar as $k)
            <div class="px-6 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $k->pemberi }}
                        @if ($k->peran)
                            <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">{{ $k->peran }}</span>
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
@endsection