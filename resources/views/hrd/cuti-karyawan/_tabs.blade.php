<div class="flex gap-6 border-b border-gray-200 mb-8">
    <a href="{{ route('hrd.cuti-karyawan.pengajuan') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('hrd.cuti-karyawan.pengajuan') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500' }}">
        Pengajuan Cuti Karyawan
    </a>
    <a href="{{ route('hrd.cuti-karyawan.riwayat') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('hrd.cuti-karyawan.riwayat') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500' }}">
        Riwayat Cuti Karyawan
    </a>
</div>