<x-app-layout>
    <x-slot name="title">Daftar Produk</x-slot>

    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <div></div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('produk.create') }}"
               class="btn-kopi text-sm">
                + Tambah Produk
            </a>
            @endif
        </div>

        {{-- Filter --}}
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama / kode..."
                   class="input-kopi w-52">
            <select name="kategori" class="select-kopi">
                <option value="">Semua kategori</option>
                @foreach(config('produk.kategori') as $kat)
                <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ ucfirst($kat) }}</option>
                @endforeach
            </select>
            <select name="status" class="select-kopi">
                <option value="">Semua status</option>
                <option value="aktif"    {{ request('status') == 'aktif'    ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="btn-kopi text-sm">
                Filter
            </button>
            <a href="{{ route('produk.index') }}" class="btn-kopi-secondary text-sm">
                Reset
            </a>
        </form>

        {{-- Tabel --}}
        <div class="card-kopi overflow-hidden">
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
                                <div>{{ $item->nama }}</div>
                                @if(!empty($item->promo_active) && !empty($item->promo_percent) && $item->promo_percent > 0)
                                <div class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-semibold">-{{ $item->promo_percent }}%</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 capitalize text-gray-600">{{ $item->kategori }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ $item->harga_format }}</td>
                        <td class="px-4 py-3 text-right {{ $item->stok < 10 ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                            {{ $item->stok }} {{ $item->satuan }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('produk.edit', $item) }}"
                                   class="text-[#08783F] hover:text-[#064C32] text-xs font-medium">Edit</a>
                                <form method="POST" action="{{ route('produk.destroy', $item) }}"
                                      onsubmit="return confirm('Nonaktifkan produk ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-medium">
                                        Nonaktifkan
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">Tidak ada produk ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 border-t">{{ $produk->links() }}</div>
        </div>
    </div>
</x-app-layout>
