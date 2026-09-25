@extends('layouts.hrd')

@section('title', 'Riwayat Cuti ' . $karyawan->nama)

@section('content')
    <a href="{{ route('hrd.cuti-karyawan.riwayat', ['periode' => $periode]) }}" class="text-sm text-gray-500 hover:underline">← Kembali</a>

    <h1 class="text-3xl font-bold text-gray-900 mt-3">Riwayat Cuti {{ $karyawan->nama }}</h1>
    <p class="text-gray-500 mt-1 mb-6">{{ $karyawan->divisi->nama ?? '-' }}</p>

    <div class="bg-amber-50 rounded-2xl p-6 mb-8 max-w-md">
        <p class="text-sm text-gray-600">Sisa kuota cuti tahunan</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $sisa }} hari tersisa dari {{ $jatah }} hari/tahun</p>
        <p class="text-sm text-gray-500 mt-1">Terpakai: {{ $terpakai }} hari</p>
    </div>

    <form method="GET" class="mb-4">
        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
               class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
    </form>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 border-b text-xs">
                    <th class="py-3 pr-4 pl-4">Jenis Cuti</th>
                    <th class="py-3 pr-4">Tanggal Mulai</th>
                    <th class="py-3 pr-4">Tanggal Selesai</th>
                    <th class="py-3 pr-4">Jumlah Hari</th>
                    <th class="py-3 pr-4">Keterangan</th>
                    <th class="py-3 pr-4">Lampiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $c)
                    <tr class="border-b">
                        <td class="py-3 pr-4 pl-4 font-medium text-gray-900">{{ $c->jenis_cuti }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->tanggal_mulai->format('d/m/Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->tanggal_selesai->format('d/m/Y') }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->jumlah_hari }} Hari</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $c->keterangan ?? '-' }}</td>
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
                    <tr><td colspan="6" class="py-6 text-center text-gray-400">Tidak ada cuti di bulan ini.</td></tr>
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