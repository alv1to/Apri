<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="space-y-6">

        {{-- Kartu ringkasan --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card-kopi p-5">
                <p class="text-[11px] uppercase tracking-[0.14em] text-[#5F6D63] font-semibold">Transaksi Hari Ini</p>
                <p class="mt-3 text-3xl font-bold text-[#064C32]">{{ $hariIni->total_transaksi }}</p>
                <p class="mt-1 text-xs text-[#5F6D63]">transaksi</p>
            </div>
            <div class="card-kopi p-5">
                <p class="text-[11px] uppercase tracking-[0.14em] text-[#5F6D63] font-semibold">Omzet Hari Ini</p>
                <p class="mt-3 text-xl font-bold text-[#08783F]">Rp {{ number_format($hariIni->omzet, 0, ',', '.') }}</p>
            </div>
            <div class="card-kopi p-5">
                <p class="text-[11px] uppercase tracking-[0.14em] text-[#5F6D63] font-semibold">Transaksi Bulan Ini</p>
                <p class="mt-3 text-3xl font-bold text-[#A97828]">{{ $bulanIni->total_transaksi }}</p>
                <p class="mt-1 text-xs text-[#5F6D63]">transaksi</p>
            </div>
            <div class="card-kopi p-5">
                <p class="text-[11px] uppercase tracking-[0.14em] text-[#5F6D63] font-semibold">Omzet Bulan Ini</p>
                <p class="mt-3 text-xl font-bold text-[#0E7D46]">Rp {{ number_format($bulanIni->omzet, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Grafik 7 hari --}}
            <div class="lg:col-span-2 card-kopi p-5">
                <h3 class="mb-4 text-lg font-semibold text-[#064C32]">Transaksi 7 Hari Terakhir</h3>
                <canvas id="chart-tujuhhari" height="120"></canvas>
            </div>

            {{-- Produk terlaris --}}
            <div class="card-kopi p-5">
                <h3 class="mb-4 text-lg font-semibold text-[#064C32]">Produk Terlaris Bulan Ini</h3>
                <div class="space-y-3">
                    @forelse($produkTerlaris as $i => $item)
                    <div class="flex items-center gap-3">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-[#EAF7ED] text-[10px] font-bold text-[#064C32] flex-shrink-0">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="truncate text-sm font-medium text-[#1D2A1F]">{{ $item->produk->nama ?? '-' }}</p>
                                @if(!empty($item->produk->promo_active) && !empty($item->produk->promo_percent) && $item->produk->promo_percent > 0)
                                <span class="rounded-full bg-[#FDECEC] px-2 py-0.5 text-[10px] font-semibold text-[#B64545]">-{{ $item->produk->promo_percent }}%</span>
                                @endif
                            </div>
                            <div class="mt-1 h-1.5 w-full rounded-full bg-[#EEF0E5]">
                                <div class="h-1.5 rounded-full bg-[#08783F]"
                                     style="width: {{ $produkTerlaris->max('total_terjual') > 0 ? ($item->total_terjual / $produkTerlaris->max('total_terjual') * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <span class="flex-shrink-0 text-sm font-semibold text-[#1D2A1F]">{{ $item->total_terjual }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Bundle aktif (admin only) --}}
            @if($isAdmin)
            <div class="card-kopi p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-[#064C32]">Bundle Aktif</h3>
                    <a href="{{ route('bundle.index') }}" class="text-xs font-medium text-[#08783F] hover:text-[#064C32]">Kelola →</a>
                </div>
                @forelse($bundleAktif as $b)
                <div class="mb-3 rounded-2xl border border-[#E7F2E9] bg-[#F5FBF6] p-3">
                    <div class="mb-1 flex flex-wrap gap-1">
                        @foreach($b->nama_antecedent as $n)
                        <span class="rounded-full bg-[#E7F4EA] px-2 py-0.5 text-[10px] font-semibold text-[#064C32]">{{ $n }}</span>
                        @endforeach
                        <span class="self-center text-xs text-[#5F6D63]">→</span>
                        @foreach($b->nama_consequent as $n)
                        <span class="rounded-full bg-[#F4E9D7] px-2 py-0.5 text-[10px] font-semibold text-[#8A5A1D]">{{ $n }}</span>
                        @endforeach
                    </div>
                    <p class="text-xs text-[#5F6D63]">
                        Lift {{ number_format($b->lift, 2) }} &bull; Conf {{ $b->confidence_persen }}
                    </p>
                </div>
                @empty
                <div class="text-center py-6">
                    <p class="text-sm text-gray-400 mb-3">Belum ada bundle aktif.</p>
                    <a href="{{ route('bundle.index') }}"
                       class="btn-kopi text-sm">
                        Generate Sekarang
                    </a>
                </div>
                @endforelse
            </div>
            @endif

            {{-- Stok menipis --}}
            <div class="card-kopi p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-[#1F2937]">Stok Menipis</h3>
                    <a href="{{ route('produk.index') }}" class="text-xs text-[#A56A2A] hover:text-[#6F4A26]">Lihat →</a>
                </div>
                @forelse($stokMenipis as $p)
                <div class="flex justify-between items-center py-2 border-b last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            <div class="flex items-center gap-2">
                                <div>{{ $p->nama }}</div>
                                @if(!empty($p->promo_active) && !empty($p->promo_percent) && $p->promo_percent > 0)
                                <div class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-semibold">-{{ $p->promo_percent }}%</div>
                                @endif
                            </div>
                        </p>
                        <p class="text-xs text-gray-400 capitalize">{{ $p->kategori }}</p>
                    </div>
                    <span class="text-sm font-bold {{ $p->stok <= 3 ? 'text-red-600' : 'text-amber-500' }}">
                        {{ $p->stok }} {{ $p->satuan }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Semua stok aman ✓</p>
                @endforelse
            </div>

            {{-- Transaksi terbaru --}}
            <div class="card-kopi p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-[#1F2937]">Transaksi Terbaru</h3>
                    <a href="{{ route('transaksi.index') }}" class="text-xs text-[#A56A2A] hover:text-[#6F4A26]">Lihat →</a>
                </div>
                @forelse($transaksiTerbaru as $trx)
                <a href="{{ route('transaksi.show', $trx) }}"
                   class="flex justify-between items-center py-2 border-b last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded transition">
                    <div>
                        <p class="text-xs font-mono text-gray-500">{{ $trx->kode_transaksi }}</p>
                        <p class="text-xs text-gray-400">{{ $trx->tanggal_transaksi->diffForHumans() }}</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ $trx->total_format }}</span>
                </a>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Belum ada transaksi.</p>
                @endforelse
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chart-tujuhhari'), {
    type: 'line',
    data: {
        labels: @json($labels),
        datasets: [
            {
                label: 'Transaksi',
                data: @json($dataTransaksi),
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79,70,229,0.08)',
                tension: 0.4, fill: true, yAxisID: 'y',
            },
            {
                label: 'Omzet (Rp)',
                data: @json($dataOmzet),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                tension: 0.4, fill: true, yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'bottom' } },
        scales: {
            y:  { type: 'linear', position: 'left',  ticks: { stepSize: 1 }, title: { display: true, text: 'Transaksi' } },
            y1: { type: 'linear', position: 'right', grid: { drawOnChartArea: false },
                  ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k' }, title: { display: true, text: 'Omzet' } }
        }
    }
});
</script>
</x-app-layout>
