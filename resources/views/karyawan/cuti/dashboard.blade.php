@extends('layouts.karyawan')

@section('title', 'Dashboard Cuti')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Cuti</h1>
    <p class="text-gray-500 mt-1 mb-6">Ringkasan saldo cuti - Tahun {{ now()->year }}</p>

    @include('karyawan.cuti._tabs')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
        <div class="bg-amber-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Jatah cuti tahunan</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $jatah }} hari</p>
        </div>
        <div class="bg-amber-50 rounded-xl p-5">
            <p class="text-sm text-gray-500">Cuti terpakai</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $terpakai }} hari</p>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Cuti Bulan Ini</h2>
    <div class="bg-white rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-3 pr-4">Jenis Cuti</th>
                    <th class="py-3 pr-4">Tanggal</th>
                    <th class="py-3 pr-4">Jumlah Hari</th>
                    <th class="py-3 pr-4">Keterangan</th>
                    <th class="py-3 pr-4">Disetujui oleh</th>
                    <th class="py-3 pr-4">Lampiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cutiBulanIni as $c)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium text-gray-900">{{ $c->jenis_cuti }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->tanggal_mulai->translatedFormat('d M Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->jumlah_hari }} Hari</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->keterangan ?? '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->disetujuiOleh->nama ?? '-' }}</td>
                        <td class="py-3 pr-4">
                            @if ($c->lampiran_path)
                                @php
                                    $url = Storage::url($c->lampiran_path);
                                    $ext = strtolower(pathinfo($c->lampiran_path, PATHINFO_EXTENSION));
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
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">Tidak ada cuti bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============ MODAL SUKSES ============ --}}
    @if (session('status'))
        <div
            id="modalSukses"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4"
        >
            <div class="relative w-full max-w-sm">
                <button
                    type="button"
                    id="btnCloseSukses"
                    class="absolute -top-4 -right-4 w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-400 hover:text-gray-600 shadow"
                    title="Tutup"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="bg-white rounded-2xl border-4 border-green-500 shadow-xl px-8 py-10 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-green-500 flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-xl font-semibold text-gray-800">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif

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

    (function () {
        const modalSukses = document.getElementById('modalSukses');
        if (!modalSukses) return;

        const closeBtn = document.getElementById('btnCloseSukses');

        function closeSukses() {
            modalSukses.remove();
        }

        closeBtn.addEventListener('click', closeSukses);

        modalSukses.addEventListener('click', function (e) {
            if (e.target === modalSukses) closeSukses();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSukses();
        });

        setTimeout(closeSukses, 3000);
    })();
</script>
@endpush