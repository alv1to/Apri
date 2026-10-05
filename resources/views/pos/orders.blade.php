<x-app-layout>
    <x-slot name="title">Pesanan</x-slot>

    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
            @foreach($statusLabels as $status => $label)
            <div class="card-kopi p-3">
                <p class="text-xs uppercase tracking-wider text-[#6B7280]">{{ $label }}</p>
                <p class="mt-2 text-2xl font-semibold text-[#064C32]">{{ $statusCounts[$status] ?? 0 }}</p>
            </div>
            @endforeach
        </div>

        <form id="orders-filters" action="{{ route('pos.orders') }}" method="GET" class="card-kopi flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
            <input type="search" name="search" value="{{ request('search') }}"
                   placeholder="Cari kode, pelanggan, atau kasir" autocomplete="off"
                   class="input-kopi min-w-0 flex-1">
            <select name="status" class="select-kopi sm:max-w-xs">
                <option value="">Semua status</option>
                @foreach($statusLabels as $status => $label)
                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @if($viewAll && request('status'))
            <input type="hidden" name="view" value="all">
            @endif
            <button type="reset" class="btn-kopi-secondary text-sm">Reset</button>
            <span id="orders-filter-status" class="sr-only" aria-live="polite"></span>
        </form>

        <div id="orders-results" aria-live="polite">
            @include('pos._orders')
        </div>
    </div>

<script>
(() => {
    const form = document.getElementById('orders-filters');
    const results = document.getElementById('orders-results');
    const status = document.getElementById('orders-filter-status');
    let debounceTimer;
    let activeRequest;

    async function loadOrders(url = null) {
        activeRequest?.abort();
        activeRequest = new AbortController();
        const requestUrl = url ? new URL(url, window.location.origin) : new URL(form.action, window.location.origin);
        if (!url) requestUrl.search = new URLSearchParams(new FormData(form)).toString();

        status.textContent = 'Memuat pesanan';
        results.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(requestUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                signal: activeRequest.signal,
            });
            if (!response.ok) throw new Error('Pesanan gagal dimuat.');

            const data = await response.json();
            results.innerHTML = data.html;
            history.replaceState({}, '', requestUrl);
            status.textContent = 'Daftar pesanan diperbarui';
        } catch (error) {
            if (error.name !== 'AbortError') status.textContent = error.message;
        } finally {
            results.removeAttribute('aria-busy');
        }
    }

    function scheduleLoad() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => loadOrders(), 300);
    }

    form.addEventListener('input', scheduleLoad);
    form.addEventListener('change', scheduleLoad);
    form.addEventListener('submit', event => {
        event.preventDefault();
        clearTimeout(debounceTimer);
        loadOrders();
    });
    form.addEventListener('reset', () => setTimeout(loadOrders, 0));

    results.addEventListener('click', event => {
        const link = event.target.closest('#orders-pagination a');
        if (!link) return;

        event.preventDefault();
        loadOrders(link.href);
    });
})();
</script>
</x-app-layout>
