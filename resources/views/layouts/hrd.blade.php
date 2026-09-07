<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HRD') - Weekly Performance Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <div class="flex min-h-screen">

        <aside class="w-72 shrink-0 bg-blue-700 text-white flex flex-col">
            <div class="px-8 py-10">
                <h1 class="text-2xl font-bold tracking-wide">INDOBOGA</h1>
                <p class="text-xs text-blue-100 tracking-widest mt-1">PT INDOBOGA MAKMUR PRATAMA</p>
            </div>

            <nav class="flex-1 px-4 space-y-1">
                @php
                    $menus = [
                        ['label' => 'Dashboard', 'route' => 'hrd.dashboard'],
                        ['label' => 'Monitoring PIC', 'route' => 'hrd.monitoring-pic.index'],
                        ['label' => 'Underperform & Alert', 'route' => null],
                        ['label' => 'Trend Performance', 'route' => null],
                        ['label' => 'Report & Export', 'route' => null],
                        ['label' => 'Notifikasi', 'route' => null],
                        ['label' => 'Cuti', 'route' => null],
                    ];
                @endphp

                @foreach ($menus as $menu)
                    @php $active = $menu['route'] && request()->routeIs($menu['route']); @endphp
                    <a href="{{ $menu['route'] ? route($menu['route']) : '#' }}"
                       class="block px-4 py-3 rounded-lg font-medium transition {{ $active ? 'bg-white text-blue-700' : 'text-white/90 hover:bg-white/10' }}">
                        {{ $menu['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="px-6 py-6 border-t border-white/20 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-semibold">
                    {{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold leading-tight">{{ auth()->user()->nama }}</p>
                    <p class="text-xs text-blue-100">{{ auth()->user()->role->nama }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="px-6 pb-6">
                @csrf
                <button type="submit" class="text-sm text-white/80 hover:text-white underline">Logout</button>
            </form>
        </aside>

        <main class="flex-1 p-10">
            @yield('content')
        </main>
    </div>
</body>
</html>