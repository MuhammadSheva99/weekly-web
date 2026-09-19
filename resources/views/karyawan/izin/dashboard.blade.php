@extends('layouts.karyawan')

@section('title', 'Dashboard Izin')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Izin</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan pengajuan izin - Tahun {{ now()->year }}</p>

    @include('karyawan.izin._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <div class="bg-amber-50 rounded-xl p-5 mb-10 max-w-xs">
        <p class="text-sm text-gray-500">Izin Bulan ini</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $jumlahIzinBulanIni }} Kali Izin</p>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Izin Bulan Ini</h2>
    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Jenis izin</th>
                    <th class="py-3 pr-4">Tanggal</th>
                    <th class="py-3 pr-4">Jam</th>
                    <th class="py-3 pr-4">Keterangan</th>
                    <th class="py-3 pr-4">Disetujui oleh</th>
                    <th class="py-3 pr-4">Lampiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($izinBulanIni as $izin)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $izin->label_jenis_izin }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $izin->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">
                            @if ($izin->jenis_izin === 'pulang_cepat')
                                Pulang {{ \Carbon\Carbon::parse($izin->jam_mulai)->format('H:i') }}
                            @elseif ($izin->jenis_izin === 'keluar_sementara')
                                {{ \Carbon\Carbon::parse($izin->jam_mulai)->format('H:i') }}@if($izin->estimasi_kembali)–{{ \Carbon\Carbon::parse($izin->estimasi_kembali)->format('H:i') }}@endif
                            @else
                                Masuk {{ \Carbon\Carbon::parse($izin->jam_mulai)->format('H:i') }}
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-gray-600">{{ $izin->keterangan ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $izin->disetujuiOleh->nama ?? '-' }}</td>
                        <td class="py-3 pr-4">
                            @if ($izin->lampiran_path)
                                @php
                                    $url = Storage::url($izin->lampiran_path);
                                    $ext = strtolower(pathinfo($izin->lampiran_path, PATHINFO_EXTENSION));
                                    $isPdf = $ext === 'pdf';
                                @endphp
                                <button
                                    type="button"
                                    class="btn-lihat-file inline-block px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-50"
                                    data-url="{{ $url }}"
                                    data-type="{{ $isPdf ? 'pdf' : 'image' }}"
                                >
                                    Lihat File
                                </button>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">Tidak ada izin bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============ MODAL PREVIEW LAMPIRAN ============ --}}
    <div
        id="modalPreview"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4"
    >
        <div class="relative w-full max-w-2xl">
            <div class="absolute -top-12 right-0 flex items-center gap-5">
                <a
                    id="btnDownloadPreview"
                    href="#"
                    download
                    class="text-white/90 hover:text-white transition"
                    title="Download"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                </a>
                <button
                    type="button"
                    id="btnClosePreview"
                    class="text-white/90 hover:text-white transition"
                    title="Tutup"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="bg-white rounded-xl overflow-hidden shadow-2xl max-h-[85vh]">
                <img id="previewImage" src="" alt="Preview Lampiran" class="w-full h-auto max-h-[85vh] object-contain hidden">
                <iframe id="previewPdf" src="" class="w-full h-[85vh] hidden" frameborder="0"></iframe>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const modal       = document.getElementById('modalPreview');
        const imgEl       = document.getElementById('previewImage');
        const pdfEl       = document.getElementById('previewPdf');
        const downloadBtn = document.getElementById('btnDownloadPreview');
        const closeBtn    = document.getElementById('btnClosePreview');

        function openModal(url, type) {
            if (type === 'pdf') {
                pdfEl.src = url;
                pdfEl.classList.remove('hidden');
                imgEl.classList.add('hidden');
                imgEl.src = '';
            } else {
                imgEl.src = url;
                imgEl.classList.remove('hidden');
                pdfEl.classList.add('hidden');
                pdfEl.src = '';
            }
            downloadBtn.href = url;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            imgEl.src = '';
            pdfEl.src = '';
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-lihat-file');
            if (btn) {
                e.preventDefault();
                openModal(btn.dataset.url, btn.dataset.type);
                return;
            }
            if (e.target === modal) {
                closeModal();
            }
        });

        closeBtn.addEventListener('click', closeModal);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    })();
</script>
@endpush