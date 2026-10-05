<x-app-layout>
    <x-slot name="title">Daftar Produk</x-slot>

    <div class="space-y-4">
        <div class="flex justify-end">
            @if(auth()->user()->isAdmin())
            <a href="{{ route('produk.create') }}" class="btn-kopi text-sm">+ Tambah Produk</a>
            @endif
        </div>

        <form id="product-filters" action="{{ route('produk.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <input type="search" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama / kode..." autocomplete="off" class="input-kopi w-52">
            <select name="kategori" class="select-kopi">
                <option value="">Semua kategori</option>
                @foreach(config('produk.kategori') as $kat)
                <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                @endforeach
            </select>
            <select name="status" class="select-kopi">
                <option value="">Semua status</option>
                <option value="tersedia" {{ in_array(request('status'), ['tersedia', 'aktif'], true) ? 'selected' : '' }}>Tersedia</option>
                <option value="tidak_tersedia" {{ in_array(request('status'), ['tidak_tersedia', 'nonaktif'], true) ? 'selected' : '' }}>Tidak Tersedia</option>
            </select>
            <button type="reset" class="btn-kopi-secondary text-sm">Reset</button>
            <span id="filter-status" class="sr-only" aria-live="polite"></span>
        </form>

        <div id="product-results" aria-live="polite">
            @include('produk._results')
        </div>
    </div>

<script>
(() => {
    const form = document.getElementById('product-filters');
    const results = document.getElementById('product-results');
    const status = document.getElementById('filter-status');
    let debounceTimer;
    let activeRequest;

    async function loadProducts(url = null) {
        activeRequest?.abort();
        activeRequest = new AbortController();

        const requestUrl = url ? new URL(url, window.location.origin) : new URL(form.action, window.location.origin);
        if (!url) requestUrl.search = new URLSearchParams(new FormData(form)).toString();

        status.textContent = 'Memuat produk';
        results.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(requestUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                signal: activeRequest.signal,
            });
            if (!response.ok) throw new Error('Produk gagal dimuat.');

            const data = await response.json();
            results.innerHTML = data.html;
            history.replaceState({}, '', requestUrl);
            status.textContent = 'Daftar produk diperbarui';
        } catch (error) {
            if (error.name !== 'AbortError') status.textContent = error.message;
        } finally {
            results.removeAttribute('aria-busy');
        }
    }

    function scheduleLoad() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => loadProducts(), 300);
    }

    form.addEventListener('input', scheduleLoad);
    form.addEventListener('change', scheduleLoad);
    form.addEventListener('submit', event => {
        event.preventDefault();
        clearTimeout(debounceTimer);
        loadProducts();
    });
    form.addEventListener('reset', () => setTimeout(loadProducts, 0));

    results.addEventListener('click', event => {
        const link = event.target.closest('#product-pagination a');
        if (!link) return;

        event.preventDefault();
        loadProducts(link.href);
    });
})();
</script>
</x-app-layout>
