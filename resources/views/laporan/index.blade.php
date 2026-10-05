<x-app-layout>
    <x-slot name="title">Laporan Penjualan</x-slot>

    <div class="space-y-6">

        {{-- Filter & Export --}}
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Dari</label>
                <input type="date" name="dari" value="{{ $dari }}"
                       class="input-kopi">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Sampai</label>
                <input type="date" name="sampai" value="{{ $sampai }}"
                       class="input-kopi">
            </div>
            <button type="submit" class="btn-kopi text-sm">
                Tampilkan
            </button>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('laporan.export.excel', ['dari' => $dari, 'sampai' => $sampai]) }}"
               class="btn-kopi-secondary text-sm">
                Export Excel
            </a>
            <a href="{{ route('laporan.export.pdf', ['dari' => $dari, 'sampai' => $sampai]) }}"
               class="btn-kopi-danger text-sm">
                Export PDF
            </a>
            @endif
        </form>

        {{-- Kartu ringkasan --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card-kopi p-5">
                <p class="text-xs text-[#6B7280] uppercase font-medium">Total Transaksi</p>
                <p class="text-3xl font-bold text-[#08783F] mt-1">{{ number_format($ringkasan->total_transaksi) }}</p>
            </div>
            <div class="card-kopi p-5">
                <p class="text-xs text-[#6B7280] uppercase font-medium">Total Omzet</p>
                <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($ringkasan->total_omzet, 0, ',', '.') }}</p>
            </div>
            <div class="card-kopi p-5">
                <p class="text-xs text-[#6B7280] uppercase font-medium">Rata-rata / Transaksi</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">Rp {{ number_format($ringkasan->rata_transaksi, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Grafik harian --}}
            <div class="card-kopi p-5">
                <h3 class="font-semibold text-[#1F2937] mb-4">Omzet Harian</h3>
                <canvas id="chart-harian" height="200"></canvas>
            </div>
            {{-- Kategori --}}
            <div class="card-kopi p-5">
                <h3 class="font-semibold text-[#1F2937] mb-4">Penjualan per Kategori</h3>
                <canvas id="chart-kategori" height="200"></canvas>
            </div>
        </div>

        {{-- Produk terlaris --}}
        <div class="card-kopi p-5">
            <h3 class="font-semibold text-[#1F2937] mb-4">10 Produk Terlaris</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-gray-400 text-xs">
                        <th class="text-left py-2">#</th>
                        <th class="text-left py-2">Produk</th>
                        <th class="text-left py-2">Kategori</th>
                        <th class="text-right py-2">Terjual</th>
                        <th class="text-right py-2">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produkTerlaris as $i => $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 text-gray-400 font-medium">{{ $i + 1 }}</td>
                        <td class="py-2 font-semibold text-gray-800">{{ $item->produk->nama ?? '-' }}</td>
                        <td class="py-2 capitalize text-gray-500">{{ $item->produk->kategori ?? '-' }}</td>
                        <td class="py-2 text-right text-gray-700">{{ number_format($item->total_terjual) }} pcs</td>
                        <td class="py-2 text-right font-semibold text-green-700">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-gray-400">Belum ada data pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chart-harian'), {
    type: 'bar',
    data: {
        labels: @json($harian->pluck('tanggal')),
        datasets: [{ label: 'Omzet (Rp)', data: @json($harian->pluck('omzet')->map(fn($v) => (float)$v)),
            backgroundColor: 'rgba(79,70,229,0.7)', borderRadius: 6 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } },
        scales: { y: { ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k' } } } }
});

new Chart(document.getElementById('chart-kategori'), {
    type: 'doughnut',
    data: {
        labels: @json($perKategori->pluck('kategori')),
        datasets: [{ data: @json($perKategori->pluck('total_pendapatan')->map(fn($v) => (float)$v)),
            backgroundColor: ['#4F46E5','#10B981','#F59E0B','#EF4444'], borderWidth: 2 }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' },
        tooltip: { callbacks: { label: ctx => ' Rp ' + ctx.raw.toLocaleString('id') } } } }
});
</script>
</x-app-layout>
