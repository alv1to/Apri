<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — {{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex">

    {{-- SIDEBAR --}}
    <aside id="sidebar"
           class="w-64 nav-surface text-[#1D2A1F] flex flex-col
                  fixed inset-y-0 left-0 z-40 border-r border-[#E7E0D2]
                  transform -translate-x-full lg:translate-x-0 transition-transform duration-200">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-[#E7E0D2] bg-white/60">
            <img src="{{ asset('storage/produk/kamaijo.jpg') }}" alt="Kopi Kama" class="h-11 w-11 object-contain rounded-xl border border-[#E6E5D6] bg-white">
            <div>
                <p class="font-semibold text-sm leading-tight text-[#064C32]">Kopi Kama</p>
                <p class="text-[11px] uppercase tracking-[0.18em] text-[#5F6D63]">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            <x-nav-link-sidebar route="dashboard" icon="grid">
                Dashboard
            </x-nav-link-sidebar>

            <div class="pt-3 pb-1 px-2">
                <p class="text-xs text-gray-500 uppercase font-medium tracking-wider">
                    Menu Utama
                </p>
            </div>

            <x-nav-link-sidebar route="produk.index" icon="box">
                Produk
            </x-nav-link-sidebar>

            <x-nav-link-sidebar route="transaksi.index" icon="shopping-cart">
                Transaksi
            </x-nav-link-sidebar>

            <x-nav-link-sidebar route="laporan.index" icon="bar-chart">
                Laporan
            </x-nav-link-sidebar>

            @if(auth()->user()->isAdmin())
            <div class="pt-3 pb-1 px-2">
                <p class="text-xs text-gray-500 uppercase font-medium tracking-wider">
                    Rekomendasi
                </p>
            </div>

            <x-nav-link-sidebar route="bundle.index" icon="zap">
                Generate Bundle
            </x-nav-link-sidebar>
            @endif

        </nav>

        {{-- User info + logout --}}
        <div class="px-3 py-4 border-t border-[#E7E0D2] bg-white/60">
            <div class="flex items-center gap-3 px-2 mb-3">
                <div class="w-8 h-8 bg-[#08783F] rounded-full flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate text-[#1D2A1F]">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-[#5F6D63] truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-3 py-2 text-sm text-[#1D2A1F]
                               hover:text-[#064C32] hover:bg-[#F3F6EE] rounded-lg transition">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div id="sidebar-overlay"
         class="hidden fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
         onclick="toggleSidebar()">
    </div>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

        {{-- Topbar --}}
        <header class="bg-kopi-header text-white sticky top-0 z-20 shadow-sm shadow-[#0B6B3F]/10">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    {{-- Hamburger --}}
                    <button onclick="toggleSidebar()"
                            class="lg:hidden p-2 rounded-2xl bg-white/10 hover:bg-white/20 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('storage/produk/kamaijo.jpg') }}" alt="Kopi Kama" class="h-9 w-9 rounded-xl object-contain border border-white/20 bg-white/10">
                        <h1 class="font-semibold text-base">
                            {{ $title ?? 'Dashboard' }}
                        </h1>
                    </div>
                </div>

                {{-- Shortcut transaksi baru --}}
                <a href="{{ route('transaksi.create') }}"
                   class="btn-kopi hidden sm:inline-flex">
                    + Transaksi
                </a>
            </div>
        </header>

        {{-- Flash global --}}
        @if(session('success'))
        <div class="mx-4 mt-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div class="mx-4 mt-4 p-3 bg-red-100 text-red-800 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 p-4 sm:p-6">
            {{ $slot }}
        </main>

        <footer class="px-6 py-3 text-xs text-gray-400 text-center border-t">
            Dashboard Promosi Penjualan &copy; {{ date('Y') }}
        </footer>
    </div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
</script>
</body>
</html>