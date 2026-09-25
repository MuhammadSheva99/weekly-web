<div class="flex gap-6 border-b border-gray-200 mb-8">
    <a href="{{ route('hrd.sp-karyawan.terbitkan') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('hrd.sp-karyawan.terbitkan') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-400' }}">
        Terbitkan SP
    </a>
    <a href="{{ route('hrd.sp-karyawan.riwayat') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('hrd.sp-karyawan.riwayat') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-400' }}">
        Riwayat Pelanggaran
    </a>
</div>