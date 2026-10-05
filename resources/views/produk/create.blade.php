<x-app-layout>
    <x-slot name="title">Tambah Produk</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card-kopi p-6">
            <form method="POST" action="{{ route('produk.store') }}" enctype="multipart/form-data">
                @csrf
                @include('produk._form')
                <div class="flex gap-3 mt-6">
                    <button type="submit"
                            class="btn-kopi">
                        Simpan Produk
                    </button>
                    <a href="{{ route('produk.index') }}"
                       class="btn-kopi-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
