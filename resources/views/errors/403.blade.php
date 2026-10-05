<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8F5EF]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <div class="card-kopi kama-grain p-8 text-center">
            <img src="{{ asset('storage/produk/kamaijo.jpg') }}" alt="Kopi Kama" class="mx-auto h-24 w-24 object-cover rounded-2xl border border-[#E6E5D6] shadow-sm mb-5">
            <div class="text-7xl font-black text-[#DCEFD9] mb-3 select-none">403</div>
            <h1 class="text-2xl font-bold text-[#064C32] mb-2">Akses Ditolak</h1>
            <p class="text-[#5F6D63] mb-7 text-sm leading-relaxed">
                Anda tidak memiliki izin untuk mengakses halaman ini.
                Silakan hubungi administrator jika ini adalah kesalahan.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                @auth
                <a href="{{ auth()->user()->isAdmin() ? route('dashboard') : route('pos.index') }}"
                   class="btn-kopi text-sm">
                    Kembali ke Halaman Utama
                </a>
                @else
                <a href="{{ route('login') }}"
                   class="btn-kopi text-sm">
                    Login
                </a>
                @endauth
                <button onclick="history.back()"
                        class="btn-kopi-secondary text-sm">
                    ← Kembali
                </button>
            </div>
        </div>
    </div>
</body>
</html>
