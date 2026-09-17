@extends('layouts.hrd')

@section('title', 'Ajukan Izin')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Izin</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan pengajuan izin - Tahun {{ now()->year }}</p>

    @include('hrd.izin._tabs')

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('hrd.izin.store') }}" enctype="multipart/form-data" class="space-y-6 max-w-xl">
        @csrf

        <div>
            <label class="block text-sm text-gray-600 mb-3">Jenis izin</label>
            <div class="grid grid-cols-2 gap-3">
                @foreach ($jenisIzin as $value => $label)
                    <label class="flex items-center gap-2 px-4 py-3 border border-gray-300 rounded-lg text-sm cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                        <input type="radio" name="jenis_izin" value="{{ $value }}" required class="accent-blue-600">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Tanggal</label>
            <input type="date" name="tanggal" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label id="label_jam_mulai" class="block text-sm text-gray-600 mb-1">Jam mulai izin</label>
                <input type="time" name="jam_mulai" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            </div>
            <div id="field_estimasi">
                <label class="block text-sm text-gray-600 mb-1">Estimasi jam kembali/masuk</label>
                <input type="time" name="estimasi_kembali" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Keterangan / alasan</label>
            <textarea name="keterangan" rows="3" required placeholder="Jelaskan alasan pengajuan izin"
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm"></textarea>
        </div>

        <div class="px-4 py-3 bg-blue-50 text-blue-700 rounded-lg text-sm">
            Akun Anda (HRD) — pengajuan otomatis disetujui.
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Lampiran (opsional)</label>
            <input type="file" name="lampiran" class="w-full text-sm">
        </div>

        <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition">
            Ajukan Izin
        </button>
    </form>

    <script>
        document.querySelectorAll('input[name="jenis_izin"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                const labelJamMulai = document.getElementById('label_jam_mulai');
                const fieldEstimasi = document.getElementById('field_estimasi');
                const inputEstimasi = fieldEstimasi.querySelector('input');

                if (this.value === 'pulang_cepat') {
                    labelJamMulai.textContent = 'Jam pulang';
                    fieldEstimasi.style.display = 'none';
                    inputEstimasi.removeAttribute('required');
                    inputEstimasi.value = '';
                } else {
                    labelJamMulai.textContent = 'Jam mulai izin';
                    fieldEstimasi.style.display = 'block';
                }
            });
        });
    </script>
@endsection