<div class="flex gap-6 border-b border-gray-200 mb-8">
    <a href="{{ route('hrd.surat-peringatan.peraturan') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('hrd.surat-peringatan.peraturan') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500' }}">
        Peraturan & konsekuensi pelanggaran
    </a>
    <a href="{{ route('hrd.surat-peringatan.riwayat') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('hrd.surat-peringatan.riwayat') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500' }}">
        Riwayat Pelanggaran
    </a>
</div>