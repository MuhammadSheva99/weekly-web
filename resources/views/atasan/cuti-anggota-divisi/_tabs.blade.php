<div class="flex gap-6 border-b border-gray-200 mb-8">
    <a href="{{ route('atasan.cuti-anggota-divisi.pengajuan') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('atasan.cuti-anggota-divisi.pengajuan') ? 'text-red-700 border-b-2 border-red-700' : 'text-gray-500' }}">
        Pengajuan Cuti Anggota
    </a>
    <a href="{{ route('atasan.cuti-anggota-divisi.riwayat') }}"
       class="pb-3 text-sm font-medium {{ request()->routeIs('atasan.cuti-anggota-divisi.riwayat') ? 'text-red-700 border-b-2 border-red-700' : 'text-gray-500' }}">
        Riwayat Cuti Anggota
    </a>
</div>