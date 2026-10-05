@php
    $statusStyles = [
        'pending' => 'bg-[#F8F0D8] text-[#8C6A2D]',
        'dibayar' => 'bg-[#EAF6ED] text-[#08783F]',
        'sedang_dibuat' => 'bg-[#E8F0FE] text-[#3159A5]',
        'siap_diambil' => 'bg-[#F2EAFE] text-[#7046A5]',
        'selesai' => 'bg-green-100 text-green-700',
        'cancel' => 'bg-[#FCEAEA] text-[#B64545]',
    ];
@endphp

<div class="space-y-5">
    @foreach($ordersByStatus as $status => $section)
    <section class="card-kopi overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#EAE6D8] bg-[#FAF7F3] px-4 py-3">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-[#1F2937]">{{ $statusLabels[$status] }}</h2>
                <span class="rounded-full bg-white px-2 py-0.5 text-xs text-[#5F6D63]">{{ $section['total'] }}</span>
            </div>
            @if($section['total'] > 5 && ! $viewAll)
            <a href="{{ route('pos.orders', array_filter(['status' => $status, 'view' => 'all', 'search' => request('search')])) }}"
                    class="inline-flex items-center rounded-xl border border-[#D9E2D1] bg-white px-3 py-2 text-sm font-semibold text-[#08783F] shadow-sm transition hover:bg-[#F3F8F1] hover:text-[#064C32]">View All</a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Nama Pelanggan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Kasir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Waktu</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($section['rows'] as $item)
                    <tr class="hover:bg-gray-50">
                        @php
                            $nextStatus = [
                                'dibayar' => 'sedang_dibuat',
                                'sedang_dibuat' => 'siap_diambil',
                                'siap_diambil' => 'selesai',
                            ][$item->order_status] ?? null;
                        @endphp
                        <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-gray-500">{{ $item->kode_transaksi }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama_pelanggan ?: 'Pelanggan Umum' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->user->name }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $item->tanggal_transaksi->format('d M Y, H:i') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center justify-center gap-2">
                                <a href="{{ route('pos.orders.show', $item) }}" class="text-xs font-medium text-[#08783F] hover:text-[#064C32]">Detail</a>
                                @if(auth()->user()->isBarista() && $nextStatus)
                                @include('pos._status-swipe', [
                                    'transaksi' => $item,
                                    'nextStatus' => $nextStatus,
                                    'nextLabel' => $statusLabels[$nextStatus],
                                ])
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada pesanan {{ strtolower($statusLabels[$status]) }}.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($viewAll && $section['rows'] instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div id="orders-pagination" class="border-t px-4 py-3">{{ $section['rows']->links() }}</div>
        @endif
    </section>
    @endforeach
</div>