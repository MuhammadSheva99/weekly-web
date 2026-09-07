<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi OTP - Weekly Performance Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">

    <div class="min-h-screen flex">

        {{-- KIRI: Foto --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-cover bg-center"
             style="background-image: url('{{ asset('images/login.png') }}');">
            <div class="absolute inset-0 bg-black/40"></div>
        </div>

        {{-- KANAN: Form Verifikasi OTP --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-800 px-6 py-12">
            <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl p-8">

                <h1 class="text-2xl font-bold text-gray-900 text-center mb-1">Verifikasi Kode</h1>
                <p class="text-gray-500 text-center text-sm mb-6">
                    Kode OTP telah dikirim ke<br>
                    <strong class="text-gray-700">{{ $email }}</strong>
                </p>

                @if ($errors->any())
                    <div class="mb-4 text-sm text-red-600 text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-4">
                    @csrf

                    <input type="text"
                           name="otp_code"
                           maxlength="6"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           placeholder="000000"
                           required
                           autofocus
                           class="w-full text-center text-3xl font-bold tracking-[0.5em] py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500">

                    <button type="submit"
                            class="w-full bg-gray-800 hover:bg-gray-900 text-white font-semibold py-2.5 rounded-lg transition">
                        Verifikasi
                    </button>
                </form>

                <p class="text-center text-xs text-gray-400 mt-6">
                    Kode berlaku 10 menit. Tidak menerima email? Cek folder spam,
                    atau <a href="{{ route('password.request') }}" class="text-gray-600 hover:underline">kirim ulang</a>.
                </p>

            </div>
        </div>
    </div>

</body>
</html>