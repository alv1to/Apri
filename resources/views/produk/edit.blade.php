<x-app-layout>
    <x-slot name="title">Edit Produk — {{ $produk->nama }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card-kopi p-6">
            <form method="POST" action="{{ route('produk.update', $produk) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('produk._form')
                <div class="flex gap-3 mt-6">
                    <button type="submit"
                            class="btn-kopi text-sm">
                        Perbarui
                    </button>
                    <a href="{{ route('produk.index') }}"
                       class="btn-kopi-secondary text-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
