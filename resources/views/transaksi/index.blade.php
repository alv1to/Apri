<x-app-layout>
    <x-slot name="title">Riwayat Transaksi</x-slot>

    <div class="space-y-4">
        {{-- Filter --}}
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Kode transaksi..."
                   class="input-kopi w-48">
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                   class="input-kopi">
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                   class="input-kopi">
            <button type="submit" class="btn-kopi text-sm">
                Filter
            </button>
            <a href="{{ route('transaksi.index') }}"
               class="btn-kopi-secondary text-sm">
                Reset
            </a>
        </form>

        <div class="card-kopi overflow-x-auto">
            <table class="w-full table-fixed divide-y divide-gray-100 text-sm">
                <colgroup>
                    <col style="width: 18%;">
                    <col style="width: 18%;">
                    <col style="width: 18%;">
                    <col style="width: 10%;">
                    <col style="width: 18%;">
                    <col style="width: 18%;">
                </colgroup>
                <thead class="bg-[#FAF7F3]">
                    <tr>
                        <th class="px-4 py-3 text-left align-middle text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-4 py-3 text-left align-middle text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left align-middle text-xs font-medium text-gray-500 uppercase">Kasir</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-4 py-3 text-right align-middle text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transaksi as $trx)
                    <tr class="hover:bg-gray-50 align-middle">
                        <td class="px-4 py-3 align-middle font-mono text-xs text-gray-500">{{ $trx->kode_transaksi }}</td>
                        <td class="px-4 py-3 align-middle text-gray-700">{{ $trx->tanggal_transaksi->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3 align-middle text-gray-700">{{ $trx->user->name }}</td>
                        <td class="px-4 py-3 align-middle text-center text-gray-700">{{ $trx->details->count() }}</td>
                        <td class="px-4 py-3 align-middle text-right font-semibold text-gray-900">{{ $trx->total_format }}</td>
                        <td class="px-4 py-3 align-middle text-center">
                            <a href="{{ route('transaksi.show', $trx) }}"
                               class="text-[#08783F] hover:text-[#064C32] text-xs font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">Belum ada transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 border-t">{{ $transaksi->links() }}</div>
        </div>
    </div>
</x-app-layout>
