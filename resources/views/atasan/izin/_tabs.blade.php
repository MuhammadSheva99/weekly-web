<div class="flex gap-6 border-b border-gray-200 mb-8">
    @php
        $tabs = [
            ['label' => 'Dashboard', 'route' => 'atasan.izin.dashboard'],
            ['label' => 'Ajukan Izin', 'route' => 'atasan.izin.ajukan'],
            ['label' => 'Riwayat Pengajuan', 'route' => 'atasan.izin.riwayat'],
            ['label' => 'Notifikasi', 'route' => 'atasan.izin.notifikasi'],
        ];
    @endphp
    @foreach ($tabs as $tab)
        <a href="{{ route($tab['route']) }}"
           class="pb-3 text-sm font-medium {{ request()->routeIs($tab['route']) ? 'text-red-700 border-b-2 border-red-700' : 'text-gray-500' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>