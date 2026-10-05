<x-app-layout>
    <x-slot name="title">Kasir (POS)</x-slot>

    <div class="flex flex-col lg:flex-row gap-4 h-full">

        {{-- ==================== KIRI: Grid Produk ==================== --}}
        <div class="flex-1 min-w-0">

            {{-- Error --}}
            @if($errors->any())
            <div class="mb-3 p-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm">{{ $errors->first() }}</div>
            @endif

            {{-- Bundle promo aktif (info untuk barista) --}}
            @if($bundleAktif->count())
            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                <p class="text-xs font-semibold text-amber-700 mb-2">💡 Promosi Bundle Aktif — Sarankan ke pelanggan:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($bundleAktif as $b)
                    <div class="bg-white border border-amber-200 rounded-lg px-3 py-1.5 text-xs">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <span class="text-[#08783F] font-medium">{{ implode(' + ', $b->nama_antecedent) }}</span>
                                <span class="text-gray-400 mx-1">→</span>
                                <span class="text-[#A56617] font-medium">{{ implode(' + ', $b->nama_consequent) }}</span>
                                @if($b->harga_bundle)
                                <span class="ml-1 text-green-600 font-semibold">
                                    Rp {{ number_format($b->harga_bundle, 0, ',', '.') }}
                                </span>
                                @endif
                            </div>
                            <div>
                                <button type="button" class="btn-kopi-secondary text-xs" onclick='tambahBundle({{ $b->id }}, {{ $b->harga_bundle ?? 'null' }}, @json(array_merge($b->antecedent ?? [], $b->consequent ?? [])))'>
                                    Tambah Bundle
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Pencarian & filter kategori --}}
            <div class="flex flex-wrap gap-2 mb-4">
                <input type="text" id="cari-produk" placeholder="🔍 Cari produk..."
                       class="input-kopi flex-1 min-w-48">

                <button onclick="filterKategori('')"
                        class="btn-kopi text-xs rounded-full"
                        data-kategori="">Semua</button>
                @foreach(config('produk.kategori') as $kat)
                <button onclick="filterKategori('{{ $kat }}')"
                        class="btn-kopi-secondary text-xs rounded-full capitalize"
                        data-kategori="{{ $kat }}">{{ ucfirst($kat) }}</button>
                @endforeach
            </div>

            {{-- Grid produk per kategori --}}
            @forelse($produk as $kategori => $items)
            <div class="mb-5 kategori-group" data-kategori="{{ $kategori }}">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 capitalize">
                    {{ $kategori }}
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2">
                    @foreach($items as $p)
                    <button type="button"
                            onclick="tambahItem({{ $p->id }}, '{{ addslashes($p->nama) }}', {{ $p->harga }}, {{ $p->stok }})"
                            class="produk-card border rounded-xl overflow-hidden text-left transition
                                   hover:border-[#08783F] hover:bg-[#F1FAF4]
                                   {{ $p->stok == 0 ? 'opacity-40 cursor-not-allowed' : 'bg-white' }}"
                            data-nama="{{ strtolower($p->nama) }}"
                            data-kategori="{{ $p->kategori }}"
                            {{ $p->stok == 0 ? 'disabled' : '' }}>
                        <div class="relative h-32 overflow-hidden bg-gray-100">
                            @if($p->gambar)
                            <img src="{{ asset('storage/' . $p->gambar) }}"
                                 alt="{{ $p->nama }}"
                                 class="h-full w-full object-cover">
                            @else
                            <div class="flex items-center justify-center h-full text-gray-400 text-xs uppercase tracking-widest">
                                Tanpa Gambar
                            </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <div class="text-sm font-semibold text-gray-800 leading-tight">{{ $p->nama }}</div>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $p->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                            <div class="mt-2 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-[#08783F] font-medium">{{ $p->harga_format }}</span>
                                    @if(!empty($p->promo_active) && !empty($p->promo_percent) && $p->promo_percent > 0)
                                    <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-semibold">-{{ $p->promo_percent }}%</span>
                                    @endif
                                </div>
                                <span class="text-xs {{ $p->stok < 5 ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                    Stok: {{ $p->stok }}
                                </span>
                            </div>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="text-center py-16 text-gray-400">
                <p class="text-4xl mb-3">🛒</p>
                <p>Belum ada produk aktif.</p>
            </div>
            @endforelse

        </div>

        {{-- ==================== KANAN: Keranjang ==================== --}}
        <div class="w-full lg:w-80 xl:w-96 flex-shrink-0">
            <div id="keranjang-card" class="card-kopi sticky top-20 flex flex-col max-h-[calc(100vh-6rem)]">

                <div class="px-4 py-3 border-b flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800">Keranjang</h2>
                    <button onclick="kosongkanKeranjang()"
                            class="text-xs text-red-400 hover:text-red-600">Kosongkan</button>
                </div>
                <div id="bundle-indicator" class="px-4 py-2 border-b bg-yellow-50 text-sm text-gray-700 hidden">
                    <div id="bundle-indicator-content"></div>
                </div>

                {{-- List item keranjang --}}
                <div id="keranjang-list" class="flex-1 overflow-y-auto px-4 py-2 min-h-32">
                    <p id="keranjang-kosong" class="text-sm text-gray-400 text-center py-10">
                        Klik produk untuk menambahkan
                    </p>
                </div>

                {{-- Checkout area --}}
                <form method="POST" action="{{ route('pos.store') }}" id="form-pos" class="px-4 py-4 border-t space-y-3">
                    @csrf

                    <div class="flex justify-between text-base font-bold text-gray-900">
                        <span>Total</span>
                        <span id="total-display">Rp 0</span>
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 font-medium">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" class="select-kopi">
                            <option value="cash">Tunai</option>
                            <option value="qris">QRIS</option>
                            <option value="debit">Kartu Debit</option>
                        </select>
                    </div>

                    <div id="bayar-container">
                        <label class="text-xs text-gray-500 font-medium">Bayar (Rp)</label>
                        <input type="number" name="bayar" id="input-bayar"
                               min="0" step="500" oninput="hitungKembalian()"
                               class="input-kopi"
                               placeholder="0">
                    </div>

                    <div id="qris-info" class="hidden p-3 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-700">
                        <p class="font-semibold text-slate-900 mb-2">Pembayaran QRIS</p>
                        <p class="mb-2">Silakan bayar melalui aplikasi QRIS pelanggan. Sistem akan otomatis menyelesaikan transaksi setelah pembayaran.</p>
                        <div class="rounded-xl bg-white border border-slate-200 p-3 text-center text-xs text-slate-500">
                            QRIS: Scan kode QRIS di layar kasir atau masukkan nominal Rp sudah otomatis.
                        </div>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Kembalian</span>
                        <span id="kembalian-display" class="font-semibold text-green-600">Rp 0</span>
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 font-medium">Catatan (opsional)</label>
                        <input type="text" name="catatan"
                               class="w-full border rounded-lg px-3 py-2 text-sm mt-1"
                               placeholder="...">
                    </div>

                    <button type="submit" id="btn-bayar"
                            class="btn-kopi w-full mt-2 disabled:opacity-40 disabled:cursor-not-allowed"
                            disabled>
                        Proses Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

<style>
.filter-btn { background: white; color: #6B7280; }
.filter-btn.aktif { background: #4F46E5; color: white; border-color: #4F46E5; }
/* Keranjang: pastikan scrollbar terlihat dan nyaman */
#keranjang-list { scrollbar-width: thin; scrollbar-color: rgba(107,114,128,.6) rgba(243,244,246,.6); }
#keranjang-list::-webkit-scrollbar { width: 10px; }
#keranjang-list::-webkit-scrollbar-track { background: rgba(243,244,246,.6); border-radius: 9999px; }
#keranjang-list::-webkit-scrollbar-thumb { background: rgba(107,114,128,.6); border-radius: 9999px; border: 2px solid rgba(243,244,246,.6); }
/* Card keranjang: tampilkan scrollbar pada container kartu jika isinya melebihi tinggi */
#keranjang-card { overflow-y: auto; }
#keranjang-card { scrollbar-width: thin; scrollbar-color: rgba(107,114,128,.6) rgba(243,244,246,.6); }
#keranjang-card::-webkit-scrollbar { width: 12px; }
#keranjang-card::-webkit-scrollbar-track { background: rgba(243,244,246,.6); border-radius: 8px; }
#keranjang-card::-webkit-scrollbar-thumb { background: rgba(107,114,128,.6); border-radius: 8px; border: 3px solid rgba(243,244,246,.6); }
</style>

<script>
let keranjang = {};
let totalHarga = 0;
let appliedBundle = null; // {id, price, productIds}

// ===== TAMBAH ITEM =====
function tambahItem(id, nama, harga, stok) {
    if (keranjang[id]) {
        if (keranjang[id].jumlah >= stok) {
            alert('Stok ' + nama + ' tidak mencukupi!');
            return;
        }
        keranjang[id].jumlah++;
    } else {
        keranjang[id] = { id, nama, harga, stok, jumlah: 1 };
    }
    renderKeranjang();
}

// ===== UBAH JUMLAH =====
function ubahJumlah(id, delta) {
    if (!keranjang[id]) return;
    keranjang[id].jumlah += delta;
    if (keranjang[id].jumlah <= 0) delete keranjang[id];
    renderKeranjang();
}

// ===== KOSONGKAN =====
function kosongkanKeranjang() {
    if (Object.keys(keranjang).length === 0) return;
    if (!confirm('Kosongkan keranjang?')) return;
    keranjang = {};
    renderKeranjang();
}

// ===== RENDER KERANJANG =====
function renderKeranjang() {
    const list   = document.getElementById('keranjang-list');
    const kosong = document.getElementById('keranjang-kosong');
    const keys   = Object.keys(keranjang);
    totalHarga   = 0;

    // Hapus hidden inputs lama
    document.querySelectorAll('.hidden-item').forEach(e => e.remove());
    // Hapus baris item lama
    list.querySelectorAll('.item-baris').forEach(e => e.remove());

    if (keys.length === 0) {
        kosong.style.display = 'block';
        updateTotal();
        return;
    }

    kosong.style.display = 'none';

    keys.forEach((id, idx) => {
        const item     = keranjang[id];
        const subtotal = item.harga * item.jumlah;
        totalHarga    += subtotal;

        const div       = document.createElement('div');
        div.className   = 'item-baris flex items-center gap-2 py-2 border-b last:border-0';
        div.innerHTML   = `
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">${item.nama}</p>
                <p class="text-xs text-gray-400">
                    Rp ${item.harga.toLocaleString('id')} &times; ${item.jumlah}
                    = <span class="font-medium text-gray-600">Rp ${subtotal.toLocaleString('id')}</span>
                </p>
            </div>
            <div class="flex items-center gap-1 flex-shrink-0">
                <button type="button" onclick="ubahJumlah(${id}, -1)"
                        class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-bold text-gray-600">−</button>
                <span class="w-6 text-center text-sm font-semibold">${item.jumlah}</span>
                <button type="button" onclick="ubahJumlah(${id}, 1)"
                        class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-bold text-gray-600">+</button>
            </div>`;
        list.appendChild(div);

        // Hidden inputs
        const form = document.getElementById('form-pos');
        ['produk_id', 'jumlah'].forEach(field => {
            const input   = document.createElement('input');
            input.type    = 'hidden';
            input.name    = `items[${idx}][${field}]`;
            input.value   = field === 'produk_id' ? item.id : item.jumlah;
            input.className = 'hidden-item';
            form.appendChild(input);
        });
    });

    // Jika ada bundle diterapkan, tambahkan hidden inputs untuk bundle
    if (appliedBundle) {
        const form = document.getElementById('form-pos');
        const bid = document.createElement('input');
        bid.type = 'hidden'; bid.name = 'bundle_id'; bid.value = appliedBundle.id; bid.id = 'hidden-bundle-id'; bid.className = 'hidden-item';
        form.appendChild(bid);

        const bprice = document.createElement('input');
        bprice.type = 'hidden'; bprice.name = 'bundle_price'; bprice.value = appliedBundle.price ?? '';
        bprice.id = 'hidden-bundle-price'; bprice.className = 'hidden-item';
        form.appendChild(bprice);
    }
    // Update bundle indicator UI
    updateBundleIndicator();


function updateBundleIndicator() {
    const el = document.getElementById('bundle-indicator');
    const content = document.getElementById('bundle-indicator-content');
    if (!appliedBundle) {
        el.classList.add('hidden');
        content.innerHTML = '';
        return;
    }

    const total = totalHarga;
    const desired = appliedBundle.price ? parseFloat(appliedBundle.price) : null;
    const wantDiscount = desired !== null ? Math.max(0, total - desired) : 0;

    // capacity = sum of 5% per item
    let capacity = 0;
    Object.values(keranjang).forEach(item => {
        capacity += item.harga * item.jumlah * 0.05;
    });
    capacity = Math.round(capacity * 100) / 100;

    const willApply = Math.min(wantDiscount, capacity);
    const expectedFinal = Math.round((total - willApply) * 100) / 100;

    let html = `<div class="flex items-center justify-between gap-3">`;
    html += `<div>Bundle aktif`; 
    if (desired !== null) html += ` — target Rp ${desired.toLocaleString('id')}`;
    html += `</div>`;
    html += `<div class="text-right">`;
    html += `<div class="text-sm text-gray-500">Potongan bundle yang bisa diterapkan: Rp ${willApply.toLocaleString('id')}</div>`;
    if (willApply < wantDiscount) {
        html += `<div class="text-xs text-red-600">Batas 5% per-produk mencegah mencapai harga bundle. Sisa belum terdiskon: Rp ${(Math.round((wantDiscount - willApply) * 100) / 100).toLocaleString('id')}</div>`;
    }
    html += `<div class="mt-1 font-semibold">Estimasi total setelah bundle: Rp ${expectedFinal.toLocaleString('id')}</div>`;
    html += `</div>`;
    html += `</div>`;
    html += `<div class="mt-2 text-right"><button type="button" class="text-xs text-red-500 hover:underline" onclick="batalBundle()">Batal Bundle</button></div>`;

    el.classList.remove('hidden');
    content.innerHTML = html;
}

function batalBundle() {
    appliedBundle = null;
    // Remove hidden inputs (renderKeranjang already removes .hidden-item)
    renderKeranjang();
}
    updateTotal();
}

// ===== UPDATE TOTAL =====
function updateTotal() {
    document.getElementById('total-display').textContent = 'Rp ' + totalHarga.toLocaleString('id');
    hitungKembalian();
    document.getElementById('btn-bayar').disabled = Object.keys(keranjang).length === 0;
}

// ===== TAMBAH BUNDLE =====
function tambahBundle(bundleId, bundlePrice, productIds) {
    // Reset any existing bundle (simple behavior)
    appliedBundle = { id: bundleId, price: bundlePrice, productIds: productIds };

    // Tambahkan setiap produk di bundle ke keranjang
    productIds.forEach(pid => {
        // Attempt to find a product card with matching data-id by querying buttons
        // Fallback: call tambahItem with unknown harga=0 — but best if product exists on page
        const card = document.querySelector(`.produk-card[onclick*="tambahItem(${pid},"]`);
        if (card) {
            // Extract harga and stok from onclick params is brittle; instead trigger click on the button
            card.click();
        } else {
            // If product not rendered (unlikely), still add placeholder — will fail server validation
            tambahItem(pid, 'Produk', 0, 9999);
        }
    });

    // Scroll to cart and show a small notice
    document.getElementById('keranjang-list').scrollIntoView({ behavior: 'smooth' });
    alert('Bundle ditambahkan ke keranjang. Harga bundle akan diterapkan saat checkout.');
    renderKeranjang();
}

// ===== KEMBALIAN =====
function hitungKembalian() {
    const bayar     = parseFloat(document.getElementById('input-bayar').value) || 0;
    const kembalian = bayar - totalHarga;
    const el        = document.getElementById('kembalian-display');
    el.textContent  = 'Rp ' + Math.max(0, kembalian).toLocaleString('id');
    el.className    = 'font-semibold ' + (kembalian < 0 ? 'text-red-500' : 'text-green-600');
}

// ===== PENCARIAN =====
document.getElementById('cari-produk').addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.produk-card').forEach(card => {
        card.style.display = card.dataset.nama.includes(q) ? '' : 'none';
    });
    // Tampilkan/sembunyikan header kategori jika semua tersembunyi
    document.querySelectorAll('.kategori-group').forEach(group => {
        const visible = [...group.querySelectorAll('.produk-card')].some(c => c.style.display !== 'none');
        group.style.display = visible ? '' : 'none';
    });
});

// ===== FILTER KATEGORI =====
function filterKategori(kat) {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.toggle('aktif', btn.dataset.kategori === kat);
    });
    document.getElementById('cari-produk').value = '';
    document.querySelectorAll('.kategori-group').forEach(group => {
        group.style.display = (!kat || group.dataset.kategori === kat) ? '' : 'none';
    });
    document.querySelectorAll('.produk-card').forEach(card => {
        card.style.display = '';
    });
}

// ===== PEMBAYARAN UI =====
document.getElementById('payment_method').addEventListener('change', updatePaymentUI);

function updatePaymentUI() {
    const method = document.getElementById('payment_method').value;
    const bayarContainer = document.getElementById('bayar-container');
    const inputBayar = document.getElementById('input-bayar');
    const form = document.getElementById('form-pos');

    // Remove any existing hidden bayar
    const existingHidden = document.getElementById('hidden-bayar');
    if (existingHidden) existingHidden.remove();

    const qrisInfo = document.getElementById('qris-info');

    if (method === 'cash') {
        bayarContainer.style.display = '';
        inputBayar.disabled = false;
        qrisInfo.classList.add('hidden');
    } else {
        // Hide bayar input and add hidden input with exact total
        bayarContainer.style.display = 'none';
        inputBayar.disabled = true;
        qrisInfo.classList.toggle('hidden', method !== 'qris');
        // create hidden bayar so server receives the amount
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'bayar';
        hidden.id = 'hidden-bayar';
        hidden.value = totalHarga;
        form.appendChild(hidden);
    }
    hitungKembalian();
}

// Adjust hitungKembalian to respect payment method
function hitungKembalian() {
    const method = document.getElementById('payment_method').value;
    const input = document.getElementById('input-bayar');
    const bayar = method === 'cash' ? (parseFloat(input.value) || 0) : totalHarga;
    const kembalian = bayar - totalHarga;
    const el = document.getElementById('kembalian-display');
    el.textContent = 'Rp ' + Math.max(0, kembalian).toLocaleString('id');
    el.className = 'font-semibold ' + (kembalian < 0 ? 'text-red-500' : 'text-green-600');
}

// Ensure UI is synced after rendering cart
const origUpdateTotal = updateTotal;
updateTotal = function() {
    origUpdateTotal();
    // Update hidden bayar value if present
    const hidden = document.getElementById('hidden-bayar');
    if (hidden) hidden.value = totalHarga;
    updatePaymentUI();
}

// Initialize payment UI on load
updatePaymentUI();
</script>
</x-app-layout>
