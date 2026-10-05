<x-app-layout>
    <x-slot name="title">Detail Transaksi</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card-kopi p-6">

            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Kode Transaksi</p>
                    <p class="font-mono font-semibold text-gray-800">{{ $transaksi->kode_transaksi }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Tanggal</p>
                    <p class="font-medium text-gray-800">{{ $transaksi->tanggal_transaksi->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Kasir</p>
                    <p class="font-medium text-gray-800">{{ $transaksi->user->name }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Status</p>
                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        {{ ucfirst($transaksi->status) }}
                    </span>
                </div>
            </div>

            <table class="w-full text-sm mb-6">
                <thead>
                    <tr class="border-b text-gray-400 text-xs">
                        <th class="text-left py-2">Produk</th>
                        <th class="text-center py-2">Jml</th>
                        <th class="text-right py-2">Harga</th>
                        <th class="text-right py-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi->details as $d)
                    <tr class="border-b">
                        <td class="py-2 font-medium text-gray-800">{{ $d->nama_produk }}</td>
                        <td class="py-2 text-center text-gray-600">{{ $d->jumlah }}</td>
                        <td class="py-2 text-right text-gray-600">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                        <td class="py-2 text-right font-semibold text-gray-800">
                            Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                            @if(!empty($d->diskon) && $d->diskon > 0)
                                <div class="text-xs text-red-600">Potongan: Rp {{ number_format($d->diskon, 0, ',', '.') }}</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="space-y-1.5 text-sm bg-[#FAF7F3] rounded-xl p-4">
                @php $totalDiskon = $transaksi->details->sum('diskon'); @endphp
                <div class="flex justify-between font-bold text-gray-900 text-base">
                    <span>Total</span>
                    <span>{{ $transaksi->total_format }}</span>
                </div>
                @if($totalDiskon > 0)
                <div class="flex justify-between text-gray-700">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($totalDiskon, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-gray-500">
                    <span>Bayar</span>
                    <span>Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-green-600 font-semibold">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <a href="{{ route('transaksi.index') }}"
                   class="btn-kopi-secondary text-sm">
                    ← Kembali
                </a>
                <a href="{{ route('pos.index') }}"
                   class="btn-kopi text-sm">
                    + Transaksi Baru
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
