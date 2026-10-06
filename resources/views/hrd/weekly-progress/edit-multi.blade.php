@extends('layouts.hrd')

@section('title', 'Weekly Progress')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Weekly Progress</h1>
    <p class="text-gray-500 mt-1 mb-2">Rabu - {{ now()->translatedFormat('d F Y') }}</p>
    <p class="text-sm text-green-600 mb-8">Sudah disubmit, kamu masih bisa mengubahnya di bawah ini.</p>

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="bg-blue-50 rounded-2xl p-6 mb-8">
        <p class="text-sm text-gray-500 mb-1">Big Goal</p>
        <p class="font-bold text-gray-900">{{ $commitments->first()->big_goal }}</p>
    </div>

    <form method="POST" action="{{ route('hrd.weekly-progress.update') }}" id="formWeeklyProgress">
        @csrf
        @method('PUT')

        <label class="block text-sm text-gray-700 font-semibold mb-3">Actual sementara per KPI</label>
        <div class="border border-gray-200 rounded-xl overflow-hidden mb-8">
            <div class="grid grid-cols-3 px-5 py-3 bg-gray-50 text-xs text-gray-400 font-medium">
                <div>KPI</div>
                <div>Target</div>
                <div>Actual Sementara</div>
            </div>
            @foreach ($commitments as $c)
                @php
                    $satuan = $c->targetMingguan?->targetBulanan?->kpi?->satuan;
                    $isPersen = $satuan === '%';
                    // nilai awal: hasil old() kalau validasi gagal, kalau tidak nilai dari DB (% berupa pecahan)
                    $nilaiAwal = old('actual.' . $c->weeklyProgress->id, $c->weeklyProgress->actual_sementara);
                    if ($nilaiAwal === null || $nilaiAwal === '') {
                        $displayAwal = '';
                    } elseif ($isPersen) {
                        // 0.05 -> tampil "5"
                        $displayAwal = rtrim(rtrim(number_format((float) $nilaiAwal * 100, 2, ',', ''), '0'), ',');
                    } else {
                        $displayAwal = number_format((float) $nilaiAwal, 0, ',', '.');
                    }
                @endphp
                <div class="grid grid-cols-3 gap-4 px-5 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }} items-center">
                    <input type="hidden" name="weekly_progress_ids[]" value="{{ $c->weeklyProgress->id }}">
                    <div class="text-sm font-medium text-gray-800">
                        {{ $c->targetMingguan?->targetBulanan?->kpi?->nama_kpi ?? 'Target Manual' }}
                    </div>
                    <div class="text-sm text-gray-600">
                        @if ($satuan)
                            {{ \App\Support\KpiFormat::tampil($c->target, $satuan) }}
                        @else
                            {{ number_format($c->target, 0, ',', '.') }}
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            @if ($satuan === 'Rupiah')
                                <span class="text-sm text-gray-500">Rp</span>
                            @endif
                            <input
                                type="text"
                                inputmode="{{ $isPersen ? 'decimal' : 'numeric' }}"
                                autocomplete="off"
                                placeholder="0"
                                data-satuan="{{ $satuan }}"
                                class="actual-display px-3 py-2 border border-gray-300 rounded-lg text-sm w-full"
                                value="{{ $displayAwal }}"
                            >
                            @if ($satuan && ! in_array($satuan, ['Rupiah', 'Angka']))
                                <span class="text-sm text-gray-500">{{ $satuan }}</span>
                            @endif
                        </div>
                        {{-- nilai yang dikirim ke server: % dikirim sebagai pecahan (5 -> 0.05) --}}
                        <input type="hidden" name="actual[{{ $c->weeklyProgress->id }}]" class="actual-hidden" required
                               value="{{ $nilaiAwal }}">
                    </div>
                </div>
            @endforeach
        </div>

        @php $firstProgress = $commitments->first()->weeklyProgress; @endphp

        <label class="block text-sm text-gray-700 font-semibold mb-2">Problem / Kendala</label>
        <textarea name="problem" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('problem', $firstProgress->problem) }}</textarea>

        <label class="block text-sm text-gray-700 font-semibold mb-2">Analysis / Penyebab</label>
        <textarea name="analysis" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('analysis', $firstProgress->analysis) }}</textarea>

        <label class="block text-sm text-gray-700 font-semibold mb-2">Solution</label>
        <textarea name="solution" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-6 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('solution', $firstProgress->solution) }}</textarea>

        <label class="block text-sm text-gray-700 font-semibold mb-2">Action Plan sampai akhir minggu</label>
        <textarea name="action_plan" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm mb-8 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('action_plan', $firstProgress->action_plan) }}</textarea>

        <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3.5 rounded-lg transition">
            Update weekly progress
        </button>
    </form>

    <script>
        (function () {
            // Non-persen: ketik "50000000" -> tampil "50.000.000", kirim "50000000"
            function formatRibuan(angka) {
                const bersih = angka.replace(/\D/g, '');
                if (!bersih) return '';
                return bersih.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Persen: boleh desimal pakai koma (maks 2 digit). "5" / "12,5"
            function bersihkanPersen(teks) {
                let t = teks.replace(/[^\d,\.]/g, '').replace(/\./g, ',');
                const idx = t.indexOf(',');
                if (idx !== -1) {
                    t = t.slice(0, idx + 1) + t.slice(idx + 1).replace(/,/g, '').slice(0, 2);
                }
                return t;
            }

            document.addEventListener('input', function (e) {
                if (!e.target.classList.contains('actual-display')) return;

                const display = e.target;
                const hidden  = display.parentElement.nextElementSibling;

                if (display.dataset.satuan === '%') {
                    display.value = bersihkanPersen(display.value);
                    const angka = parseFloat(display.value.replace(',', '.'));
                    // 5 -> 0.05 (disimpan sebagai pecahan, sama seperti target)
                    hidden.value = isNaN(angka) ? '' : String(Math.round(angka * 100) / 10000);
                    return;
                }

                const posisiKursorDariBelakang = display.value.length - display.selectionStart;
                const formatted = formatRibuan(display.value);
                display.value = formatted;
                hidden.value = formatted.replace(/\./g, '');

                const posisiBaru = Math.max(0, display.value.length - posisiKursorDariBelakang);
                display.setSelectionRange(posisiBaru, posisiBaru);
            });

            document.getElementById('formWeeklyProgress').addEventListener('submit', function (e) {
                let ada_kosong = false;
                document.querySelectorAll('.actual-hidden').forEach(function (hidden) {
                    if (!hidden.value) ada_kosong = true;
                });
                if (ada_kosong) {
                    e.preventDefault();
                    alert('Actual sementara untuk setiap KPI wajib diisi.');
                }
            });
        })();
    </script>
@endsection