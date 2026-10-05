<x-app-layout>
    <x-slot name="title">Detail Produk</x-slot>

    <div class="mx-auto max-w-3xl">
        <div class="card-kopi p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="font-mono text-xs text-[#5F6D63]">{{ $produk->kode }}</p>
                    <h1 class="mt-1 text-2xl font-bold text-[#064C32]">{{ $produk->nama }}</h1>
                    <p class="mt-1 text-sm text-[#5F6D63]">{{ $produk->kategori }}</p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $produk->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $produk->status_ketersediaan }}
                </span>
            </div>

            <dl class="mt-6 grid gap-4 border-t border-[#EAE6D8] pt-5 sm:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase text-[#5F6D63]">Harga</dt>
                    <dd class="mt-1 font-semibold text-[#1D2A1F]">{{ $produk->harga_format }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-[#5F6D63]">Stok</dt>
                    <dd class="mt-1 font-semibold text-[#1D2A1F]">{{ $produk->stok }} {{ $produk->satuan }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs uppercase text-[#5F6D63]">Deskripsi</dt>
                    <dd class="mt-1 whitespace-pre-line text-sm text-[#1D2A1F]">{{ $produk->deskripsi ?: 'Tidak ada deskripsi.' }}</dd>
                </div>
            </dl>

            <div class="mt-6 flex flex-wrap gap-3 border-t border-[#EAE6D8] pt-5">
                <a href="{{ route('produk.index') }}" class="btn-kopi-secondary text-sm">Kembali ke Produk</a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('produk.edit', $produk) }}" class="btn-kopi text-sm">Edit Produk</a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
