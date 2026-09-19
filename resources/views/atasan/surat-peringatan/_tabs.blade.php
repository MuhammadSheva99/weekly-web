<div class="flex gap-6 border-b border-gray-200 mb-8">
    <a href="{{ route('atasan.surat-peringatan.peraturan') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('atasan.surat-peringatan.peraturan') ? 'text-red-700 border-b-2 border-red-700' : 'text-gray-500' }}">
        Peraturan & konsekuensi pelanggaran
    </a>
    <a href="{{ route('atasan.surat-peringatan.riwayat') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('atasan.surat-peringatan.riwayat') ? 'text-red-700 border-b-2 border-red-700' : 'text-gray-500' }}">
        Riwayat Pelanggaran Tim
    </a>
</div>