<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8F5EF]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex">

    <aside id="sidebar"
           class="w-64 bg-[#F8F5EF] text-[#1D2A1F] flex flex-col fixed inset-y-0 left-0 z-40 border-r border-[#E6E5D6] transform -translate-x-full lg:translate-x-0 transition-transform duration-200">
        <div class="flex items-center gap-3 px-5 py-5 border-b border-[#E6E5D6]">
            <img src="{{ asset('storage/produk/kamaijo.jpg') }}" alt="Kopi Kama" class="h-11 w-11 object-cover rounded-2xl border border-[#E6E5D6] bg-white shadow-sm">
            <div>
                <p class="font-semibold text-sm text-[#064C32]">Kopi Kama</p>
                <p class="text-xs text-[#5F6D63] uppercase tracking-wider">{{ ucfirst(auth()->user()->role ?? '') }}</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <x-nav-link-sidebar route="dashboard" icon="grid">Dashboard</x-nav-link-sidebar>
            <div class="pt-3 pb-1 px-2">
                <p class="text-xs text-[#5F6D63] uppercase font-medium tracking-wider">Menu</p>
            </div>
            <x-nav-link-sidebar route="produk.index" icon="box">Produk</x-nav-link-sidebar>
            <x-nav-link-sidebar route="transaksi.index" icon="shopping-cart">Transaksi</x-nav-link-sidebar>
            <x-nav-link-sidebar route="laporan.index" icon="bar-chart">Laporan</x-nav-link-sidebar>
            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="pt-3 pb-1 px-2">
                <p class="text-xs text-[#5F6D63] uppercase font-medium tracking-wider">Rekomendasi</p>
            </div>
            <x-nav-link-sidebar route="bundle.index" icon="zap">Generate Bundle</x-nav-link-sidebar>
            @endif
        </nav>

        <div class="px-3 py-4 border-t border-[#E6E5D6]">
            <div class="flex items-center gap-3 px-2 mb-3">
                <div class="w-9 h-9 bg-[#08783F] rounded-2xl flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate text-[#1D2A1F]">{{ auth()->user()->name ?? '' }}</p>
                    <p class="text-xs text-[#5F6D63] truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-3 py-2 text-sm text-[#5F6D63]
                               hover:text-[#064C32] hover:bg-[#F3F6EE] rounded-2xl transition">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div id="sidebar-overlay"
         class="hidden fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
         onclick="toggleSidebar()"></div>

    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
        <header class="bg-kopi-header text-white sticky top-0 z-20">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-2xl bg-white/10 hover:bg-white/20 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    {{ $header ?? '' }}
                </div>
                <a href="{{ route('transaksi.create') }}"
                   class="btn-kopi text-sm">
                    + Transaksi
                </a>
            </div>
        </header>

        @if(session('success'))
        <div class="mx-4 mt-4 p-3 bg-green-100 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div class="mx-4 mt-4 p-3 bg-red-100 border border-red-200 text-red-800 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <main class="flex-1 p-4 sm:p-6">
            {{ $slot }}
        </main>

        <footer class="px-6 py-3 text-xs text-[#5F6D63] text-center border-t border-[#E6E5D6] bg-white">
            Dashboard Promosi Penjualan &copy; {{ date('Y') }}
        </footer>
    </div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.toggle('hidden');
}
</script>
</body>
</html>