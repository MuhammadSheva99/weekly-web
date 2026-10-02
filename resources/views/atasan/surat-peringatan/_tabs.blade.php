<div class="flex gap-6 border-b border-gray-200 mb-8">
    <a href="{{ route('atasan.surat-peringatan.peraturan') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('atasan.surat-peringatan.peraturan') ? 'text-red-700 border-b-2 border-red-700' : 'text-gray-500' }}">
        Peraturan & konsekuensi pelanggaran
    </a>
    <a href="{{ route('atasan.surat-peringatan.riwayat-saya') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('atasan.surat-peringatan.riwayat-saya') ? 'text-red-700 border-b-2 border-red-700' : 'text-gray-500' }}">
        Riwayat SP Saya
    </a>
    <a href="{{ route('atasan.surat-peringatan.riwayat') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('atasan.surat-peringatan.riwayat') ? 'text-red-700 border-b-2 border-red-700' : 'text-gray-500' }}">
        Riwayat Pelanggaran Tim
    </a>
    <a href="{{ route('atasan.surat-peringatan.rekomendasi') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('atasan.surat-peringatan.rekomendasi') ? 'text-red-700 border-b-2 border-red-700' : 'text-gray-500' }}">
        Rekomendasikan SP
    </a>
    <a href="{{ route('atasan.surat-peringatan.rekomendasi.riwayat') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('atasan.surat-peringatan.rekomendasi.riwayat') ? 'text-red-700 border-b-2 border-red-700' : 'text-gray-500' }}">
        Riwayat Rekomendasi Saya
    </a>
</div>