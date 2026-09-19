<div class="flex gap-6 border-b border-gray-200 mb-8">
    <a href="{{ route('karyawan.surat-peringatan.peraturan') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('karyawan.surat-peringatan.peraturan') ? 'text-amber-700 border-b-2 border-amber-700' : 'text-gray-500' }}">
        Peraturan & konsekuensi pelanggaran
    </a>
    <a href="{{ route('karyawan.surat-peringatan.riwayat') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('karyawan.surat-peringatan.riwayat') ? 'text-amber-700 border-b-2 border-amber-700' : 'text-gray-500' }}">
        Riwayat Pelanggaran
    </a>
</div>