{{-- Tabel KPI satu orang. Param: $d (hasil App\Support\KpiTabel) --}}
@php
    $fmt = fn ($n, $desimal = 1) => rtrim(rtrim(number_format((float) $n, $desimal, ',', '.'), '0'), ',');
    $badgeStatus = fn ($s) => match ($s) {
        'Sangat Baik' => 'bg-green-50 text-green-700',
        'Baik' => 'bg-blue-50 text-blue-700',
        'Cukup' => 'bg-amber-50 text-amber-700',
        'Buruk' => 'bg-red-50 text-red-700',
        default => 'bg-gray-100 text-gray-500',
    };
    $badgeGrade = fn ($g) => match ($g) {
        'A' => 'bg-green-50 text-green-700',
        'B' => 'bg-blue-50 text-blue-700',
        'C' => 'bg-amber-50 text-amber-700',
        'D' => 'bg-red-50 text-red-700',
        default => 'bg-gray-100 text-gray-400',
    };
    $kraSebelumnya = null;
@endphp

<div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[1000px]">
        <thead>
            <tr class="text-left text-gray-500 border-b bg-gray-50">
                <th class="py-3 pl-4 pr-3 w-10">No.</th>
                <th class="py-3 pr-4">Key Result Area</th>
                <th class="py-3 pr-4">Indikator</th>
                <th class="py-3 pr-4">Pola</th>
                <th class="py-3 pr-4 text-right">Bobot</th>
                <th class="py-3 pr-4 text-right">Target</th>
                <th class="py-3 pr-4 text-right">Realisasi</th>
                <th class="py-3 pr-4 text-right">Pencapaian</th>
                <th class="py-3 pr-4 text-right">Skor</th>
                <th class="py-3 pr-4 text-center">Grade</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($d->rows as $i => $r)
                <tr class="border-b align-top">
                    <td class="py-3 pl-4 pr-3 text-gray-500">{{ $i + 1 }}</td>
                    <td class="py-3 pr-4 text-gray-700">
                        @if ($r->kpi->key_result_area !== $kraSebelumnya){{ $r->kpi->key_result_area }}@endif
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-semibold text-gray-900">
                            {{ $r->kpi->nama_kpi }}
                            @if ($r->kpi->target_fleksibel)
                                <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-normal bg-orange-50 text-orange-700">Fleksibel</span>
                            @endif
                        </div>
                        @if ($r->kpi->deskripsi)
                            <div class="text-xs text-gray-500 mt-0.5">{{ $r->kpi->deskripsi }}</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ strtolower($r->kpi->pola) === 'minimize' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">{{ ucfirst(strtolower($r->kpi->pola)) }}</span>
                    </td>
                    <td class="py-3 pr-4 text-right text-gray-900">{{ $fmt($r->bobot, 2) }}%</td>
                    <td class="py-3 pr-4 text-right text-gray-900 whitespace-nowrap">{{ \App\Support\KpiFormat::tampil($r->target, $r->kpi->satuan) }}</td>
                    <td class="py-3 pr-4 text-right text-gray-900 whitespace-nowrap">
                        @if ($r->realisasi !== null)
                            {{ \App\Support\KpiFormat::tampil($r->realisasi, $r->kpi->satuan) }}
                        @else
                            <span class="text-gray-300">Belum diisi</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4 text-right text-gray-900">
                        {{ $r->pencapaian !== null ? $fmt($r->pencapaian).'%' : '-' }}
                    </td>
                    <td class="py-3 pr-4 text-right text-gray-900">{{ $r->kontribusi !== null ? $fmt($r->kontribusi) : '-' }}</td>
                    <td class="py-3 pr-4 text-center">
                        <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $badgeGrade($r->grade) }}">{{ $r->grade ?? '-' }}</span>
                    </td>
                </tr>
                @php $kraSebelumnya = $r->kpi->key_result_area; @endphp
            @empty
                <tr>
                    <td colspan="10" class="py-8 text-center text-gray-400">Belum ada KPI yang ditetapkan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($d->rows->isNotEmpty())
            <tfoot>
                <tr class="bg-gray-50">
                    <td colspan="4" class="py-3 pl-4 pr-4 text-right font-semibold text-gray-700">Total</td>
                    <td class="py-3 pr-4 text-right font-bold {{ abs($d->bobot_total - 100) < 0.01 ? 'text-green-600' : 'text-red-600' }}">{{ $fmt($d->bobot_total, 2) }}%</td>
                    <td colspan="3" class="py-3 pr-4 text-xs text-gray-500 text-right">
                        {{ $d->terisi }} dari {{ $d->jumlah }} KPI sudah terisi
                    </td>
                    <td class="py-3 pr-4 text-right font-bold text-gray-900">{{ $d->skor_total !== null ? $fmt($d->skor_total) : '-' }}</td>
                    <td class="py-3 pr-4 text-center">
                        <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $badgeGrade($d->grade) }}">{{ $d->grade ?? '-' }}</span>
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>