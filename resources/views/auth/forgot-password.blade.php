<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - Weekly Performance Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">

    <div class="min-h-screen flex">

        {{-- KIRI: Foto — sama persis strukturnya dengan halaman Login --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-cover bg-center"
             style="background-image: url('{{ asset('images/login.png') }}');">
            <div class="absolute inset-0 bg-black/40"></div>
        </div>

        {{-- KANAN: Form --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-800 px-6 py-12">
            <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl p-8">

                <h1 class="text-2xl font-bold text-gray-900 text-center mb-1">Lupa Password?</h1>
                <p class="text-gray-500 text-center text-sm mb-6">
                    Masukkan email kamu, kami akan kirim kode OTP untuk reset password.
                </p>

                @if ($errors->any())
                    <div class="mb-4 text-sm text-red-600 text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </span>
                        <input type="email" name="email" placeholder="Email Address" required autofocus
                               value="{{ old('email') }}"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400">
                    </div>

                    <button type="submit"
                            class="w-full bg-gray-800 hover:bg-gray-900 text-white font-semibold py-2.5 rounded-lg transition">
                        Kirim Kode OTP
                    </button>
                </form>

            </div>
        </div>
    </div>

</body>
</html>