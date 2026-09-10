<div class="flex gap-6 border-b border-gray-200 mb-8">
    <a href="{{ route('management.cuti-karyawan.pengajuan') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('management.cuti-karyawan.pengajuan') ? 'text-green-700 border-b-2 border-green-700' : 'text-gray-500' }}">
        Pengajuan Cuti Karyawan
    </a>
    <a href="{{ route('management.cuti-karyawan.riwayat') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('management.cuti-karyawan.riwayat') ? 'text-green-700 border-b-2 border-green-700' : 'text-gray-500' }}">
        Riwayat Cuti Karyawan
    </a>
</div>