<div class="flex gap-6 border-b border-gray-200 mb-8">
    @php
        $tabs = [
            ['label' => 'Dashboard', 'route' => 'atasan.cuti.dashboard'],
            ['label' => 'Ajukan Cuti', 'route' => 'atasan.cuti.ajukan'],
            ['label' => 'Riwayat Pengajuan', 'route' => 'atasan.cuti.riwayat'],
            ['label' => 'Notifikasi', 'route' => 'atasan.cuti.notifikasi'],
        ];
    @endphp
    @foreach ($tabs as $tab)
        <a href="{{ route($tab['route']) }}"
           class="pb-3 text-sm font-medium {{ request()->routeIs($tab['route']) ? 'text-red-700 border-b-2 border-red-700' : 'text-gray-500' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>