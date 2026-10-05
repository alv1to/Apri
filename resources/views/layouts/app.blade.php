<!DOCTYPE html>
<html lang="id" class="h-full bg-kopi-light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — {{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside id="sidebar"
           class="w-[250px] bg-[#0B6B3F] text-white flex flex-col fixed inset-y-0 left-0 z-40
                  border-r border-[#0A5C36] transform -translate-x-full lg:translate-x-0 transition-transform duration-200">

        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/15">
            <div class="w-12 h-12 overflow-hidden rounded-[20px] border border-white/20 bg-white/10 p-1 shadow-sm">
                <img src="{{ asset('storage/produk/kamaijo.jpg') }}" alt="Kopi Kama" class="h-full w-full rounded-[14px] object-cover">
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-sm leading-tight text-white">Kopi Kama</p>
                <p class="text-[10px] uppercase tracking-[0.22em] text-[#DCEFE1] font-medium">{{ strtoupper(auth()->user()->role ?? 'Admin') }}</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto">
            @if(auth()->user()->isAdmin())
            <x-nav-link-sidebar route="dashboard" icon="grid">Dashboard</x-nav-link-sidebar>
            @endif

            <div class="pt-3 pb-1 px-2">
                <p class="text-[10px] uppercase tracking-[0.2em] text-[#DDEFE0] font-semibold">Menu</p>
            </div>
            <x-nav-link-sidebar route="pos.index" icon="shopping-cart">Kasir (POS)</x-nav-link-sidebar>
            <x-nav-link-sidebar route="produk.index" icon="box">Produk</x-nav-link-sidebar>
            <x-nav-link-sidebar route="pos.orders" icon="clipboard-list">
                Orders
                @if($pendingOrderCount > 0)
                <span class="ml-auto inline-flex items-center rounded-full bg-[#F4D0A1] px-2 py-0.5 text-[11px] font-semibold text-[#0A3B2A]">
                    {{ $pendingOrderCount }}
                </span>
                @endif
            </x-nav-link-sidebar>
            <x-nav-link-sidebar route="transaksi.index" icon="list">Riwayat Transaksi</x-nav-link-sidebar>
            <x-nav-link-sidebar route="laporan.index" icon="bar-chart">Laporan</x-nav-link-sidebar>

            @if(auth()->user()->isAdmin())
            <div class="pt-3 pb-1 px-2">
                <p class="text-[10px] uppercase tracking-[0.2em] text-[#DDEFE0] font-semibold">Rekomendasi</p>
            </div>
            <x-nav-link-sidebar route="bundle.index" icon="zap">Apriori</x-nav-link-sidebar>
            @endif
        </nav>
    </aside>

    <div id="sidebar-overlay"
         class="hidden fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
         onclick="toggleSidebar()"></div>

    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen bg-[#F8F5EF] text-[#1D2A1F]">
        <header class="sticky top-0 z-30 border-b border-[#EAE6D8] bg-[#F8F5EF] backdrop-blur-sm">
            <div class="flex items-center justify-between gap-4 px-6 py-4 min-h-[72px]">
                <div class="flex items-center gap-3 min-w-0">
                    <button onclick="toggleSidebar()"
                            class="lg:hidden p-2 rounded-2xl bg-[#EEF6EE] text-[#0A6A3E] hover:bg-[#E3F0E4] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    @isset($header)
                        {{ $header }}
                    @else
                        <h1 class="truncate text-xl font-bold text-[#0A3B2A] sm:text-2xl">{{ $title ?? 'Dashboard' }}</h1>
                    @endisset
                </div>

                <div class="relative ml-auto">
                    <button type="button" id="profile-toggle"
                            class="flex items-center gap-3 rounded-2xl border border-[#E3E7DF] bg-white px-3 py-2 text-left shadow-sm transition hover:border-[#BFD7C5] hover:bg-[#F8FBF8] focus:outline-none focus:ring-2 focus:ring-[#08783F]/15">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0B6B3F] text-sm font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-sm font-semibold text-[#1D2A1F]">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] uppercase tracking-[0.14em] text-[#5F6D63]">{{ auth()->user()->role ?? 'Admin' }}</p>
                        </div>
                        <svg class="h-4 w-4 text-[#5F6D63]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="profile-menu" class="absolute right-0 top-[calc(100%+0.75rem)] z-50 hidden w-56 overflow-hidden rounded-2xl border border-[#EAE6D8] bg-white shadow-lg shadow-[#0B6B3F]/10">
                        <div class="border-b border-[#EEF0E5] px-4 py-3">
                            <p class="text-sm font-semibold text-[#1D2A1F]">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-[#5F6D63]">{{ auth()->user()->role ?? 'Admin' }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-3 text-left text-sm font-medium text-[#1D2A1F] transition hover:bg-[#F3F8F1] hover:text-[#0A5C36]">
                                <span aria-hidden="true">🚪</span>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        @if(session('success'))
        <div class="mx-4 mt-4 p-3 bg-green-100 border border-green-200 text-green-800
                    rounded-lg text-sm flex justify-between items-center" id="flash-success">
            <span>{{ session('success') }}</span>
            <button onclick="document.getElementById('flash-success').remove()"
                    class="text-green-600 hover:text-green-800 ml-3 font-bold">✕</button>
        </div>
        @endif

        @if($errors->any())
        <div class="mx-4 mt-4 p-3 bg-red-100 border border-red-200 text-red-800
                    rounded-lg text-sm flex justify-between items-center" id="flash-error">
            <span>{{ $errors->first() }}</span>
            <button onclick="document.getElementById('flash-error').remove()"
                    class="text-red-600 hover:text-red-800 ml-3 font-bold">✕</button>
        </div>
        @endif

        <main class="flex-1 p-4 sm:p-6">
            {{ $slot }}
        </main>

        <footer class="px-6 py-3 text-xs text-gray-400 text-center border-t bg-white">
            Dashboard Promosi Penjualan &copy; {{ date('Y') }}
        </footer>
    </div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.toggle('hidden');
}

const profileToggle = document.getElementById('profile-toggle');
const profileMenu = document.getElementById('profile-menu');

if (profileToggle && profileMenu) {
    profileToggle.addEventListener('click', function (event) {
        event.stopPropagation();
        profileMenu.classList.toggle('hidden');
    });

    document.addEventListener('click', function (event) {
        if (!profileMenu.contains(event.target) && !profileToggle.contains(event.target)) {
            profileMenu.classList.add('hidden');
        }
    });
}

setTimeout(() => {
    ['flash-success', 'flash-error'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.remove();
    });
}, 4000);
</script>
</body>
</html>
