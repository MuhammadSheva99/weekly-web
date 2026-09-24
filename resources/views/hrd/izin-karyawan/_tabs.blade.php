<div class="flex gap-6 border-b border-gray-200 mb-8">
    <a href="{{ route('hrd.izin-karyawan.pengajuan') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('hrd.izin-karyawan.pengajuan') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500' }}">
        Pengajuan Izin Karyawan
    </a>
    <a href="{{ route('hrd.izin-karyawan.riwayat') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('hrd.izin-karyawan.riwayat') ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500' }}">
        Riwayat Izin Karyawan
    </a>
</div>