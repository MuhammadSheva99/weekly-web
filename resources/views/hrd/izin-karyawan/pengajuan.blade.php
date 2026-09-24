@extends('layouts.hrd')

@section('title', 'Izin Karyawan')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900">Izin Karyawan</h1>
    <p class="text-gray-500 mt-1 mb-6">Approval final pengajuan izin seluruh karyawan</p>

    @include('hrd.izin-karyawan._tabs')

    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <div class="space-y-4">
        @forelse ($daftar as $izin)
            <div class="bg-white border border-gray-200 rounded-2xl p-5 flex items-center gap-6">
                <div class="flex items-center gap-3 w-40 shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-700 text-white flex items-center justify-center font-semibold">
                        {{ strtoupper(substr($izin->user->nama, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $izin->user->nama }}</p>
                        <p class="text-xs text-gray-400">{{ $izin->user->divisi->nama ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex-1 grid grid-cols-5 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Jenis Izin</p>
                        <p class="font-medium text-gray-800">{{ $izin->label_jenis_izin }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Tanggal</p>
                        <p class="font-medium text-gray-800">{{ $izin->tanggal->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Jam</p>
                        <p class="font-medium text-gray-800">
                            {{ \Carbon\Carbon::parse($izin->jam_mulai)->format('H:i') }}
                            @if ($izin->estimasi_kembali)–{{ \Carbon\Carbon::parse($izin->estimasi_kembali)->format('H:i') }}@endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Keterangan</p>
                        <p class="font-medium text-gray-800">{{ $izin->keterangan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Lampiran</p>
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
                    </div>
                </div>

                <div class="w-28 shrink-0 text-sm">
                    <p class="text-xs text-gray-400">Diteruskan oleh</p>
                    <p class="font-medium text-gray-800">{{ $izin->user->atasan->nama ?? '-' }}</p>
                </div>

                <div class="flex flex-col gap-2 shrink-0">
                    <form method="POST" action="{{ route('hrd.izin-karyawan.approve', $izin) }}">
                        @csrf
                        <button type="submit" class="w-24 px-3 py-1.5 border border-green-300 text-green-700 rounded text-xs font-medium hover:bg-green-50">Setuju</button>
                    </form>
                    <form method="POST" action="{{ route('hrd.izin-karyawan.reject', $izin) }}" onsubmit="return confirm('Yakin tolak pengajuan ini?')">
                        @csrf
                        <button type="submit" class="w-24 px-3 py-1.5 border border-red-300 text-red-700 rounded text-xs font-medium hover:bg-red-50">Tolak</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-400">
                Tidak ada pengajuan izin yang menunggu.
            </div>
        @endforelse
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