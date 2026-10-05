<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#064C32] leading-tight">Transaksi Baru</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-2xl border border-red-200">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('transaksi.store') }}" id="form-transaksi">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <div class="lg:col-span-2 card-kopi p-4">
                        <h3 class="font-medium text-[#064C32] mb-3">Pilih Produk</h3>
                        <input type="text" id="cari-produk" placeholder="Cari produk..."
                               class="input-kopi mb-4">

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="grid-produk">
                            @foreach($produk as $p)
                            <button type="button"
                                    onclick="tambahItem({{ $p->id }}, '{{ $p->nama }}', {{ $p->harga }}, {{ $p->stok }})"
                                    class="produk-card border border-[#EAE6D8] rounded-2xl p-3 text-left hover:border-[#08783F] hover:bg-[#F3F8F1] transition {{ $p->stok == 0 ? 'opacity-40 cursor-not-allowed' : 'bg-white' }}"
                                    data-nama="{{ strtolower($p->nama) }}"
                                    {{ $p->stok == 0 ? 'disabled' : '' }}>
                                <div class="text-sm font-medium text-gray-800">{{ $p->nama }}</div>
                                <div class="flex items-center gap-2">
                                    <div class="text-xs text-gray-500">{{ $p->harga_format }}</div>
                                    @if(!empty($p->promo_active) && !empty($p->promo_percent) && $p->promo_percent > 0)
                                    <div class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-semibold">-{{ $p->promo_percent }}%</div>
                                    @endif
                                </div>
                                <div class="text-xs {{ $p->stok < 5 ? 'text-red-500' : 'text-gray-400' }} mt-1">
                                    Stok: {{ $p->stok }}
                                </div>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="card-kopi p-4 flex flex-col">
                            <h3 class="font-medium text-[#064C32] mb-3">Keranjang</h3>

                        <div id="keranjang" class="flex-1 space-y-2 min-h-32">
                            <p id="keranjang-kosong" class="text-sm text-gray-400 text-center py-8">
                                Belum ada item
                            </p>
                        </div>

                        <div class="border-t pt-3 mt-3 space-y-2">
                            <div class="flex justify-between text-sm font-semibold text-gray-800">
                                <span>Total</span>
                                <span id="total-display">Rp 0</span>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Jumlah Bayar (Rp)</label>
                                <input type="number" name="bayar" id="input-bayar" min="0"
                                       oninput="hitungKembalian()"
                                       class="input-kopi"
                                       placeholder="0">
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Kembalian</span>
                                <span id="kembalian-display" class="font-medium">Rp 0</span>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Catatan (opsional)</label>
                                <input type="text" name="catatan"
                                       class="input-kopi"
                                       placeholder="...">
                            </div>
                            <button type="submit" id="btn-bayar"
                                    class="btn-kopi w-full mt-2 disabled:opacity-40 disabled:cursor-not-allowed"
                                    disabled>
                                Proses Transaksi
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
let keranjang = {};
let totalHarga = 0;

function tambahItem(id, nama, harga, stok) {
    if (keranjang[id]) {
        if (keranjang[id].jumlah >= stok) {
            alert('Stok ' + nama + ' tidak mencukupi.');
            return;
        }
        keranjang[id].jumlah++;
    } else {
        keranjang[id] = { id, nama, harga, stok, jumlah: 1 };
    }
    renderKeranjang();
}

function ubahJumlah(id, delta) {
    if (!keranjang[id]) return;
    keranjang[id].jumlah += delta;
    if (keranjang[id].jumlah <= 0) {
        delete keranjang[id];
    }
    renderKeranjang();
}

function renderKeranjang() {
    const container = document.getElementById('keranjang');
    const kosong    = document.getElementById('keranjang-kosong');
    const keys      = Object.keys(keranjang);
    totalHarga      = 0;

    document.querySelectorAll('.hidden-item').forEach(e => e.remove());

    if (keys.length === 0) {
        kosong.style.display = 'block';
        container.querySelectorAll('.item-baris').forEach(e => e.remove());
        updateTotal();
        return;
    }

    kosong.style.display = 'none';
    container.querySelectorAll('.item-baris').forEach(e => e.remove());

    keys.forEach((id, idx) => {
        const item     = keranjang[id];
        const subtotal = item.harga * item.jumlah;
        totalHarga    += subtotal;

        const div = document.createElement('div');
        div.className = 'item-baris flex items-center justify-between text-sm py-2 border-b border-[#EAE6D8]';
        div.innerHTML = `
            <div class="flex-1">
                <div class="font-medium text-gray-800">${item.nama}</div>
                <div class="text-xs text-gray-500">Rp ${item.harga.toLocaleString('id')} × ${item.jumlah}</div>
            </div>
            <div class="flex items-center gap-1 ml-2">
                <button type="button" onclick="ubahJumlah(${id}, -1)"
                        class="w-6 h-6 rounded bg-[#EEF0E5] hover:bg-[#E2E8D8] text-sm font-bold text-[#1D2A1F]">−</button>
                <span class="w-6 text-center text-[#1D2A1F]">${item.jumlah}</span>
                <button type="button" onclick="ubahJumlah(${id}, 1)"
                        class="w-6 h-6 rounded bg-[#EEF0E5] hover:bg-[#E2E8D8] text-sm font-bold text-[#1D2A1F]">+</button>
            </div>
        `;
        container.appendChild(div);

        const form = document.getElementById('form-transaksi');
        ['produk_id', 'jumlah'].forEach(field => {
            const input   = document.createElement('input');
            input.type    = 'hidden';
            input.name    = `items[${idx}][${field}]`;
            input.value   = field === 'produk_id' ? item.id : item.jumlah;
            input.className = 'hidden-item';
            form.appendChild(input);
        });
    });

    updateTotal();
}

function updateTotal() {
    document.getElementById('total-display').textContent =
        'Rp ' + totalHarga.toLocaleString('id');
    hitungKembalian();
    document.getElementById('btn-bayar').disabled = Object.keys(keranjang).length === 0;
}

function hitungKembalian() {
    const bayar     = parseFloat(document.getElementById('input-bayar').value) || 0;
    const kembalian = bayar - totalHarga;
    const el        = document.getElementById('kembalian-display');
    el.textContent  = 'Rp ' + Math.max(0, kembalian).toLocaleString('id');
    el.className    = 'font-medium ' + (kembalian < 0 ? 'text-red-500' : 'text-green-600');
}

document.getElementById('cari-produk').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.produk-card').forEach(card => {
        card.style.display = card.dataset.nama.includes(q) ? '' : 'none';
    });
});
</script>
</x-app-layout>