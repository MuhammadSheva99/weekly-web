<div class="flex gap-6 border-b border-gray-200 mb-8">
    @php
        $tabs = [
            ['label' => 'Dashboard', 'route' => 'hrd.cuti.dashboard'],
            ['label' => 'Ajukan Cuti', 'route' => 'hrd.cuti.ajukan'],
            ['label' => 'Riwayat Pengajuan', 'route' => 'hrd.cuti.riwayat'],
            ['label' => 'Notifikasi', 'route' => 'hrd.cuti.notifikasi'],
        ];
    @endphp
    @foreach ($tabs as $tab)
        <a href="{{ route($tab['route']) }}"
           class="pb-3 text-sm font-medium {{ request()->routeIs($tab['route']) ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>