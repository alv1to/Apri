<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F5EF] px-4 py-8 sm:px-6 lg:px-8">
    @php($loginMode = request('mode') === 'barista' ? 'barista' : 'admin')
    <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-5xl items-center justify-center">
        <div class="grid w-full overflow-hidden rounded-[30px] border border-[#EAE6D8] bg-white shadow-[0_20px_45px_rgba(18,52,35,0.08)] lg:grid-cols-[1.05fr_1fr]">
            <div class="flex items-center justify-center bg-[#F2F7F0] p-6 sm:p-10 lg:p-12">
                <div class="flex flex-col items-center text-center">
                    <div class="mb-5 flex h-28 w-28 items-center justify-center overflow-hidden rounded-[28px] border border-[#DDE8D8] bg-white shadow-sm sm:h-32 sm:w-32">
                        <img src="{{ asset('storage/produk/kamaijo.jpg') }}" alt="Kopi Kama" class="h-full w-full object-contain p-2">
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#0F6A41]">Kopi Kama</p>
                        <h1 class="font-kopi-display text-3xl text-[#0B3C2D] sm:text-4xl">Hai, Sobat Kama!</h1>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center p-6 sm:p-8 lg:p-10">
                <div class="w-full max-w-md">
                    <div class="mb-6">
                        <p class="text-sm font-medium text-[#5F6D63]">Selamat datang</p>
                        <h2 class="mt-1 text-2xl font-bold text-[#1D2A1F]">Masuk ke akun</h2>
                    </div>

                    <div class="mb-6 grid grid-cols-2 gap-2 rounded-2xl bg-[#F2F7F0] p-1">
                        <a href="{{ route('login', ['mode' => 'admin']) }}"
                           @class(['rounded-xl px-3 py-2.5 text-center text-sm font-semibold transition', 'bg-white text-[#064C32] shadow-sm' => $loginMode === 'admin', 'text-[#5F6D63] hover:text-[#064C32]' => $loginMode !== 'admin'])>
                            Login Sebagai Admin
                        </a>
                        <a href="{{ route('login', ['mode' => 'barista']) }}"
                           @class(['rounded-xl px-3 py-2.5 text-center text-sm font-semibold transition', 'bg-white text-[#064C32] shadow-sm' => $loginMode === 'barista', 'text-[#5F6D63] hover:text-[#064C32]' => $loginMode !== 'barista'])>
                            Login Sebagai Barista
                        </a>
                    </div>

                    @if(session('status'))
                    <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                    @endif

                    @if($loginMode === 'admin')
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-[#1D2A1F]">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus autocomplete="username"
                                   class="input-kopi focus:ring-2 focus:ring-[#08783F]/20 focus:border-[#08783F] transition"
                                   placeholder="email@kafe.com">
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-[#1D2A1F]">Password</label>
                            <input id="password" type="password" name="password" autocomplete="current-password"
                                   class="input-kopi focus:ring-2 focus:ring-[#08783F]/20 focus:border-[#08783F] transition"
                                   placeholder="••••••••">
                        </div>

                        <div class="flex items-center justify-between gap-3 text-sm text-[#4B5A52]">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-[#B7C6B2] text-[#08783F] focus:ring-[#08783F]">
                                <span>Ingat saya</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="font-medium text-[#08783F] hover:text-[#064C32] hover:underline">Lupa password?</a>
                        </div>

                        <button type="submit"
                                class="w-full rounded-2xl bg-[#08783F] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#064C32] focus:outline-none focus:ring-2 focus:ring-[#08783F]/20 disabled:cursor-not-allowed disabled:opacity-60">
                            Login Sebagai Admin
                        </button>
                    </form>
                    @elseif(session('barista_otp_phone') && !request()->boolean('new_phone'))
                    <p class="mb-4 text-sm text-[#5F6D63]">Jika nomor terdaftar, kode OTP akan dikirim. Masukkan kode yang Anda terima.</p>
                    <form method="POST" action="{{ route('barista.otp.verify') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="no_telepon" value="{{ session('barista_otp_phone') }}">
                        <div class="space-y-2">
                            <label for="otp" class="block text-sm font-medium text-[#1D2A1F]">Kode OTP</label>
                            <input id="otp" type="text" name="otp" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required autofocus
                                   class="input-kopi text-center text-lg tracking-[0.3em] focus:ring-2 focus:ring-[#08783F]/20 focus:border-[#08783F] transition"
                                   placeholder="000000">
                        </div>
                        <button type="submit" class="w-full rounded-2xl bg-[#08783F] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#064C32]">
                            Verifikasi OTP
                        </button>
                    </form>
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <form method="POST" action="{{ route('barista.otp.send') }}">
                            @csrf
                            <input type="hidden" name="no_telepon" value="{{ session('barista_otp_phone') }}">
                            <button type="submit" class="font-medium text-[#08783F] hover:underline">Kirim ulang OTP</button>
                        </form>
                        <a href="{{ route('login', ['mode' => 'barista', 'new_phone' => 1]) }}" class="font-medium text-[#5F6D63] hover:text-[#064C32]">Ganti nomor</a>
                    </div>
                    @else
                    <form method="POST" action="{{ route('barista.otp.send') }}" class="space-y-5">
                        @csrf
                        <div class="space-y-2">
                            <label for="no_telepon" class="block text-sm font-medium text-[#1D2A1F]">Nomor telepon</label>
                            <input id="no_telepon" type="tel" name="no_telepon" value="{{ old('no_telepon') }}" required autofocus autocomplete="tel"
                                   class="input-kopi focus:ring-2 focus:ring-[#08783F]/20 focus:border-[#08783F] transition"
                                   placeholder="08xxxxxxxxxx">
                        </div>
                        <button type="submit" class="w-full rounded-2xl bg-[#08783F] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#064C32]">
                            Login Sebagai Barista
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
