<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Karyawan') - Weekly Performance Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <div class="flex min-h-screen">
        <aside class="w-72 shrink-0 bg-amber-400 text-amber-950 flex flex-col">
            <div class="px-8 py-10">
                <h1 class="text-2xl font-bold tracking-wide">INDOBOGA</h1>
                <p class="text-xs text-amber-900 tracking-widest mt-1">PT INDOBOGA MAKMUR PRATAMA</p>
            </div>

            <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                @php
                    $menus = [
                        ['label' => 'Dashboard', 'route' => 'karyawan.dashboard'],
                        ['label' => 'Weekly Commitment', 'route' => 'karyawan.weekly-commitment.create'],
                        ['label' => 'Weekly Progress', 'route' => 'karyawan.weekly-progress.create'],
                        ['label' => 'Self Review', 'route' => 'karyawan.self-review.create'],
                        ['label' => 'History Weekly', 'route' => null],
                        ['label' => 'Trend Performance', 'route' => null],
                        ['label' => 'Notifikasi', 'route' => null],
                        ['label' => 'Cuti', 'route' => null],
                        ['label' => 'Izin', 'route' => null],
                    ];
                @endphp

                @foreach ($menus as $menu)
                    @php $active = $menu['route'] && request()->routeIs($menu['route']); @endphp
                    <a href="{{ $menu['route'] ? route($menu['route']) : '#' }}"
                       class="block px-4 py-3 rounded-lg font-medium transition {{ $active ? 'bg-white text-amber-800' : 'text-amber-950/80 hover:bg-white/30' }}">
                        {{ $menu['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="px-6 py-6 border-t border-amber-900/20 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-950/20 flex items-center justify-center font-semibold">
                    {{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold leading-tight">{{ auth()->user()->nama }}</p>
                    <p class="text-xs text-amber-900">{{ auth()->user()->divisi->nama ?? auth()->user()->role->nama }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="px-6 pb-6">
                @csrf
                <button type="submit" class="text-sm text-amber-950/70 hover:text-amber-950 underline">Logout</button>
            </form>
        </aside>

        <main class="flex-1 p-10">
            @yield('content')
        </main>
    </div>
</body>
</html>