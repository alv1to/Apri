<x-app-layout>
    <x-slot name="title">Pesanan</x-slot>

    <div class="space-y-4">
        <div class="grid gap-4 lg:grid-cols-5">
            <div class="card-kopi p-4">
                <p class="text-xs uppercase tracking-wider text-[#6B7280]">Total Pesanan</p>
                <p class="mt-3 text-3xl font-semibold text-[#1F2937]">{{ $transaksi->total() }}</p>
            </div>
            <div class="card-kopi p-4">
                <p class="text-xs uppercase tracking-wider text-[#6B7280]">Pending</p>
                <p class="mt-3 text-3xl font-semibold text-[#C98A2C]">{{ $statusCounts['pending'] ?? 0 }}</p>
            </div>
            <div class="card-kopi p-4">
                <p class="text-xs uppercase tracking-wider text-[#6B7280]">Dibayar</p>
                <p class="mt-3 text-3xl font-semibold text-[#08783F]">{{ $statusCounts['dibayar'] ?? 0 }}</p>
            </div>
            <div class="card-kopi p-4">
                <p class="text-xs uppercase tracking-wider text-[#6B7280]">Selesai</p>
                <p class="mt-3 text-3xl font-semibold text-[#08783F]">{{ $statusCounts['selesai'] ?? 0 }}</p>
            </div>
            <div class="card-kopi p-4">
                <p class="text-xs uppercase tracking-wider text-[#6B7280]">Dibatalkan</p>
                <p class="mt-3 text-3xl font-semibold text-[#B64545]">{{ $statusCounts['batal'] ?? 0 }}</p>
            </div>
        </div>

        <div class="card-kopi p-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-lg font-semibold text-[#1F2937]">Daftar Pesanan</h1>
                <p class="text-sm text-[#6B7280]">Pantau status pesanan barista secara cepat.</p>
            </div>
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <select name="status" class="select-kopi">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="dibayar" {{ request('status')=='dibayar' ? 'selected' : '' }}>Dibayar</option>
                    <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="batal" {{ request('status')=='batal' ? 'selected' : '' }}>Batal</option>
                </select>
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="Cari kode transaksi"
                       class="input-kopi">
                <button type="submit" class="btn-kopi text-sm">Filter</button>
            </form>
        </div>

        <div class="card-kopi overflow-x-auto">
            <table class="w-full table-fixed divide-y divide-gray-100 text-sm">
                <colgroup>
                    <col style="width: 14%;">
                    <col style="width: 18%;">
                    <col style="width: 16%;">
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col style="width: 22%;">
                </colgroup>
                <thead class="bg-[#FAF7F3]">
                    <tr>
                        <th class="px-4 py-3 text-left align-middle text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left align-middle text-xs font-medium text-gray-500 uppercase">Kasir</th>
                        <th class="px-4 py-3 text-left align-middle text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left align-middle text-xs font-medium text-gray-500 uppercase">Metode</th>
                        <th class="px-4 py-3 text-left align-middle text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 pr-5 text-right align-middle text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transaksi as $item)
                    <tr class="hover:bg-gray-50 align-middle">
                        <td class="px-4 py-3 align-middle font-mono text-xs text-gray-500">{{ $item->kode_transaksi }}</td>
                        <td class="px-4 py-3 align-middle font-medium text-gray-900">{{ $item->user->name }}</td>
                        <td class="px-4 py-3 align-middle text-gray-700">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 align-middle capitalize text-gray-600">{{ $item->payment_method ?? 'cash' }}</td>
                        <td class="px-4 py-3 align-middle">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $item->order_status === 'pending' ? 'bg-[#F8F0D8] text-[#8C6A2D]' : '' }}
                                {{ $item->order_status === 'dibayar' ? 'bg-[#EAF6ED] text-[#08783F]' : '' }}
                                {{ $item->order_status === 'selesai' ? 'bg-[#EAF6ED] text-[#08783F]' : '' }}
                                {{ $item->order_status === 'batal' ? 'bg-[#FCEAEA] text-[#B64545]' : '' }}">
                                {{ ucfirst($item->order_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 pr-5 align-middle text-right">
                            <div class="flex justify-end items-center gap-3 whitespace-nowrap">
                                <a href="{{ route('pos.orders.show', $item) }}"
                                   class="text-[#08783F] hover:text-[#064C32] text-xs font-medium">Detail</a>
                                <form method="POST" action="{{ route('pos.orders.status', $item) }}" class="flex items-center gap-2">
                                    @csrf
                                    <select name="order_status" class="select-kopi text-xs min-w-[110px]">
                                        @foreach(['pending','dibayar','selesai','batal'] as $status)
                                        <option value="{{ $status }}" {{ $item->order_status === $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-kopi text-xs whitespace-nowrap">Perbarui</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">Belum ada pesanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $transaksi->links() }}</div>
    </div>
</x-app-layout>
