<x-app-layout>
    <x-slot name="title">Detail Pesanan</x-slot>
    @php($orderStatusLabels = [
        'pending' => 'Pending',
        'dibayar' => 'Sudah Dibayar',
        'sedang_dibuat' => 'Sedang Dibuat',
        'siap_diambil' => 'Siap Diambil',
        'selesai' => 'Selesai',
        'cancel' => 'Cancel',
    ])
    @php($nextStatus = [
        'dibayar' => 'sedang_dibuat',
        'sedang_dibuat' => 'siap_diambil',
        'siap_diambil' => 'selesai',
    ][$transaksi->order_status] ?? null)

    <div class="max-w-5xl mx-auto space-y-4">
        <div class="card-kopi p-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-[#1F2937]">Detail Pesanan</h1>
                <p class="text-sm text-[#6B7280]">Kode: {{ $transaksi->kode_transaksi }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(auth()->user()->isBarista() && $transaksi->order_status !== 'cancel')
                <form method="POST" action="{{ route('pos.orders.cancel', $transaksi) }}"
                      onsubmit="return confirm('Cancel pesanan {{ $transaksi->kode_transaksi }}? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    <button type="submit" aria-label="Cancel pesanan" title="Cancel pesanan"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-red-200 bg-white text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/>
                        </svg>
                    </button>
                </form>
                @endif
                <a href="{{ route('pos.orders') }}"
                   class="btn-kopi-secondary text-sm">
                    ← Kembali ke Pesanan
                </a>
                <a href="{{ route('pos.struk', $transaksi) }}"
                   class="btn-kopi text-sm">
                    Lihat Struk
                </a>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="card-kopi p-4">
                <h2 class="text-sm font-semibold text-[#1F2937] uppercase tracking-wider mb-3">Informasi Pesanan</h2>
                <div class="space-y-3 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <span>No. Transaksi</span>
                        <span class="font-medium">{{ $transaksi->kode_transaksi }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kasir</span>
                        <span class="font-medium">{{ $transaksi->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Nama Pelanggan</span>
                        <span class="font-medium">{{ $transaksi->nama_pelanggan ?: 'Pelanggan Umum' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal</span>
                        <span class="font-medium">{{ $transaksi->tanggal_transaksi->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Metode Pembayaran</span>
                        <span class="font-medium">{{ $transaksi->payment_method_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status Pesanan</span>
                        <span class="font-semibold uppercase text-xs tracking-wide px-2 py-1 rounded-full
                                     {{ $transaksi->order_status === 'pending' ? 'bg-[#F8F0D8] text-[#8C6A2D]' : '' }}
                                     {{ in_array($transaksi->order_status, ['dibayar', 'selesai']) ? 'bg-[#EAF6ED] text-[#08783F]' : '' }}
                                     {{ $transaksi->order_status === 'sedang_dibuat' ? 'bg-[#E8F0FE] text-[#3159A5]' : '' }}
                                     {{ $transaksi->order_status === 'siap_diambil' ? 'bg-[#F2EAFE] text-[#7046A5]' : '' }}
                                     {{ $transaksi->order_status === 'cancel' ? 'bg-[#FCEAEA] text-[#B64545]' : '' }}">
                            {{ $orderStatusLabels[$transaksi->order_status] ?? $transaksi->order_status }}
                        </span>
                    </div>
                    @if($transaksi->catatan)
                    <div>
                        <span class="block text-gray-500 text-xs uppercase tracking-wider mb-1">Catatan</span>
                        <p class="rounded-2xl bg-[#F8F5EF] border border-[#EAE6D8] p-3 text-sm text-gray-700">{{ $transaksi->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 card-kopi p-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-[#1F2937] uppercase tracking-wider">Rincian Item</h2>
                    <span class="text-xs text-[#6B7280]">{{ $transaksi->details->count() }} item</span>
                </div>

                <div class="space-y-3">
                    @foreach($transaksi->details as $detail)
                    <div class="grid grid-cols-12 gap-3 items-center rounded-2xl border border-[#EAE6D8] bg-[#F8F5EF] p-3">
                        <div class="col-span-8">
                            <p class="font-medium text-gray-800">{{ $detail->nama_produk }}</p>
                            <p class="text-xs text-gray-500">{{ $detail->jumlah }} × Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-span-4 text-right font-semibold text-gray-900">
                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6 border-t pt-4 text-sm text-gray-700 space-y-3">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Bayar</span>
                        <span class="font-medium">Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kembalian</span>
                        <span class="font-medium text-[#08783F]">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->isBarista() && $nextStatus)
        <div class="card-kopi p-4">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-[#1F2937]">Perbarui Status Pesanan</h2>
            @include('pos._status-swipe', [
                'transaksi' => $transaksi,
                'nextStatus' => $nextStatus,
                'nextLabel' => $orderStatusLabels[$nextStatus],
            ])
        </div>
        @endif
    </div>
</x-app-layout>
