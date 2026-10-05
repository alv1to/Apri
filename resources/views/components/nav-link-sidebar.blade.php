@props(['route', 'icon' => 'circle'])

@php
    $active = request()->routeIs($route) || request()->routeIs($route . '.*');
    $icons  = [
        'grid'          => 'M3 3h7v7H3zm11 0h7v7h-7zM3 14h7v7H3zm11 0h7v7h-7z',
        'box'           => 'M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z',
        'shopping-cart' => 'M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0',
        'list'          => 'M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01',
        'bar-chart'     => 'M18 20V10M12 20V4M6 20v-6',
        'zap'           => 'M13 2L3 14h9l-1 8 10-12h-9l1-8z',
        'clipboard-list' => 'M9 2h6a2 2 0 012 2v1h1a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2h1V4a2 2 0 012-2zM9 4v1h6V4M9 10h6M9 14h6',
        'circle'        => 'M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0-18 0',
    ];
    $path = $icons[$icon] ?? $icons['circle'];
@endphp

<a href="{{ route($route) }}"
   class="flex items-center gap-3 rounded-2xl border px-3 py-2.5 text-sm transition-all duration-200 ease-out
          {{ $active
              ? 'border border-[#CFEAD7] bg-[#128A4D] font-semibold text-white shadow-[0_8px_18px_rgba(11,107,63,0.22)] ring-1 ring-white/10'
              : 'border-transparent text-white/90 hover:bg-white/8 hover:text-white' }}">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
         viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="{{ $path }}"/>
    </svg>
    <span>{{ $slot }}</span>
</a>
