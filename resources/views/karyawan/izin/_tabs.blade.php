<div class="flex gap-6 border-b border-gray-200 mb-8">
    @php
        $tabs = [
            ['label' => 'Dashboard', 'route' => 'karyawan.izin.dashboard'],
            ['label' => 'Ajukan Izin', 'route' => 'karyawan.izin.ajukan'],
            ['label' => 'Riwayat Pengajuan', 'route' => 'karyawan.izin.riwayat'],
            ['label' => 'Notifikasi', 'route' => 'karyawan.izin.notifikasi'],
        ];
    @endphp
    @foreach ($tabs as $tab)
        <a href="{{ route($tab['route']) }}"
           class="pb-3 text-sm font-medium {{ request()->routeIs($tab['route']) ? 'text-amber-700 border-b-2 border-amber-700' : 'text-gray-500' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>