@extends('layouts.atasan')

@section('title', 'Ajukan Izin')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Izin</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan pengajuan izin - Tahun {{ now()->year }}</p>

    @include('atasan.izin._tabs')

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('atasan.izin.store') }}" enctype="multipart/form-data" class="space-y-6 max-w-xl">
        @csrf

        <div>
            <label class="block text-sm text-gray-600 mb-3">Jenis izin</label>
            <div class="grid grid-cols-2 gap-3">
                @foreach ($jenisIzin as $value => $label)
                    <label class="flex items-center gap-2 px-4 py-3 border border-gray-300 rounded-lg text-sm cursor-pointer has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                        <input type="radio" name="jenis_izin" value="{{ $value }}" required class="accent-red-600">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Tanggal</label>
            <input type="date" name="tanggal" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>

        {{--
            Jam pakai 2 <select> (Jam 00-23 + Menit 00-59) digabung ke hidden input,
            bukan <input type="time"> native — karena tampilan AM/PM vs 24-jam pada
            <input type="time"> ikut locale browser/OS user, di luar kendali HTML/CSS.
            Value yang dikirim ke server tetap format "HH:mm", sama seperti sebelumnya.
        --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label id="label_jam_mulai" class="block text-sm text-gray-600 mb-1">Jam mulai izin</label>
                <div class="flex items-center gap-2">
                    <select id="jam_mulai_jam" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                        @foreach (range(0, 23) as $j)
                            <option value="{{ sprintf('%02d', $j) }}">{{ sprintf('%02d', $j) }}</option>
                        @endforeach
                    </select>
                    <span class="text-gray-400">.</span>
                    <select id="jam_mulai_menit" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                        @foreach (range(0, 59) as $m)
                            <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="jam_mulai" id="jam_mulai" required>
            </div>
            <div id="field_estimasi">
                <label class="block text-sm text-gray-600 mb-1">Estimasi jam kembali/masuk</label>
                <div class="flex items-center gap-2">
                    <select id="estimasi_kembali_jam" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                        @foreach (range(0, 23) as $j)
                            <option value="{{ sprintf('%02d', $j) }}">{{ sprintf('%02d', $j) }}</option>
                        @endforeach
                    </select>
                    <span class="text-gray-400">.</span>
                    <select id="estimasi_kembali_menit" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                        @foreach (range(0, 59) as $m)
                            <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="estimasi_kembali" id="estimasi_kembali">
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Keterangan / alasan</label>
            <textarea name="keterangan" rows="3" required placeholder="Jelaskan alasan pengajuan izin"
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm"></textarea>
        </div>

        <div class="px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
            Pengajuan akan langsung diteruskan ke HRD untuk persetujuan.
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Lampiran (opsional)</label>
            <input type="file" name="lampiran" class="w-full text-sm">
        </div>

        <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-3 rounded-lg transition">
            Ajukan Izin
        </button>
    </form>

    <script>
        // Label + field estimasi menyesuaikan jenis izin yang dipilih.
        // 'pulang_cepat', 'berangkat_siang', 'berangkat_terlambat' cuma butuh 1 jam
        // (jam pulang / jam berangkat), estimasi disembunyikan.
        // 'keluar_sementara' (default) butuh jam mulai + estimasi kembali.
        const labelPerJenis = {
            pulang_cepat: 'Jam pulang',
            berangkat_siang: 'Jam berangkat',
            berangkat_terlambat: 'Jam berangkat',
        };

        document.querySelectorAll('input[name="jenis_izin"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                const labelJamMulai = document.getElementById('label_jam_mulai');
                const fieldEstimasi = document.getElementById('field_estimasi');
                const selectEstimasiJam = document.getElementById('estimasi_kembali_jam');
                const selectEstimasiMenit = document.getElementById('estimasi_kembali_menit');

                if (labelPerJenis[this.value]) {
                    labelJamMulai.textContent = labelPerJenis[this.value];
                    fieldEstimasi.style.display = 'none';
                    selectEstimasiJam.disabled = true;
                    selectEstimasiMenit.disabled = true;
                } else {
                    labelJamMulai.textContent = 'Jam mulai izin';
                    fieldEstimasi.style.display = 'block';
                    selectEstimasiJam.disabled = false;
                    selectEstimasiMenit.disabled = false;
                }
            });
        });

        // Gabungkan 2 <select> (jam + menit) jadi hidden input format "HH:mm"
        // sebelum form di-submit.
        function pasangGabungan(idJam, idMenit, idHidden) {
            const selJam = document.getElementById(idJam);
            const selMenit = document.getElementById(idMenit);
            const hidden = document.getElementById(idHidden);

            function update() {
                if (selJam.disabled) {
                    hidden.value = '';
                    return;
                }
                hidden.value = selJam.value + ':' + selMenit.value;
            }

            selJam.addEventListener('change', update);
            selMenit.addEventListener('change', update);
            update();
        }

        pasangGabungan('jam_mulai_jam', 'jam_mulai_menit', 'jam_mulai');
        pasangGabungan('estimasi_kembali_jam', 'estimasi_kembali_menit', 'estimasi_kembali');

        document.querySelector('form').addEventListener('submit', function () {
            // Pastikan hidden input ter-update terakhir kali sebelum submit
            ['jam_mulai', 'estimasi_kembali'].forEach(function (nama) {
                const jam = document.getElementById(nama + '_jam');
                const menit = document.getElementById(nama + '_menit');
                const hidden = document.getElementById(nama);
                hidden.value = jam.disabled ? '' : (jam.value + ':' + menit.value);
            });
        });
    </script>
@endsection