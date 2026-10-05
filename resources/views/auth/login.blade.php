<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F5EF] px-4 py-8 sm:px-6 lg:px-8">
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

                    @if($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                    @endif

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
                            <a href="#" class="font-medium text-[#08783F] hover:text-[#064C32] hover:underline">Lupa password?</a>
                        </div>

                        <button type="submit"
                                class="w-full rounded-2xl bg-[#08783F] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#064C32] focus:outline-none focus:ring-2 focus:ring-[#08783F]/20 disabled:cursor-not-allowed disabled:opacity-60">
                            Masuk
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
