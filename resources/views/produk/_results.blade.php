<div class="card-kopi overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-[#FAF7F3]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    @if(auth()->user()->isAdmin())
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($produk as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $item->kode }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">
                        <div class="flex items-center gap-2">
                            <span>{{ $item->nama }}</span>
                            @if($item->promo_active && $item->promo_percent > 0)
                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-600">-{{ $item->promo_percent }}%</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->kategori }}</td>
                    <td class="px-4 py-3 text-right text-gray-700">{{ $item->harga_format }}</td>
                    <td class="px-4 py-3 text-right {{ $item->stok < 10 ? 'font-semibold text-red-600' : 'text-gray-700' }}">{{ $item->stok }} {{ $item->satuan }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $item->status_ketersediaan }}
                        </span>
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="px-4 py-3 text-center">
                        <div class="flex flex-wrap items-center justify-center gap-3">
                            <a href="{{ route('produk.edit', $item) }}" class="text-xs font-medium text-[#08783F] hover:text-[#064C32]">Edit</a>
                            @if($item->is_active)
                            <form method="POST" action="{{ route('produk.destroy', $item) }}" onsubmit="return confirm('Nonaktifkan produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700">Nonaktifkan</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('produk.availability', $item) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="is_active" value="1">
                                <button type="submit" class="text-xs font-medium text-[#08783F] hover:text-[#064C32]">Jadikan Tersedia</button>
                            </form>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="px-4 py-12 text-center text-gray-400">Tidak ada produk ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="product-pagination" class="border-t px-4 py-3">{{ $produk->links() }}</div>
</div>