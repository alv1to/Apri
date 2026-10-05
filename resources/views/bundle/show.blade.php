<x-app-layout>
    <x-slot name="title">Detail Association Rule</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        <div class="card-kopi p-6">
            <p class="text-xs text-[#5F6D63] uppercase tracking-wider font-medium mb-4">Association Rule</p>

            <div class="flex flex-wrap items-center gap-3 mb-4">
                <div class="flex flex-wrap gap-2">
                    @foreach($bundle->nama_antecedent as $nama)
                    <span class="bg-[#EAF6ED] text-[#08783F] px-4 py-2 rounded-xl text-sm font-semibold">{{ $nama }}</span>
                    @endforeach
                </div>
                <span class="text-3xl text-[#D7DCC9] font-light">→</span>
                <div class="flex flex-wrap gap-2">
                    @foreach($bundle->nama_consequent as $nama)
                    <span class="bg-[#F8F0D8] text-[#8C6A2D] px-4 py-2 rounded-xl text-sm font-semibold">{{ $nama }}</span>
                    @endforeach
                </div>
            </div>

            <p class="text-sm text-[#5F6D63] bg-[#F8F5EF] rounded-2xl p-3">
                Jika pelanggan membeli <strong class="text-[#1D2A1F]">{{ implode(' dan ', $bundle->nama_antecedent) }}</strong>,
                ada kemungkinan <strong class="text-[#08783F]">{{ $bundle->confidence_persen }}</strong>
                mereka juga akan membeli <strong class="text-[#1D2A1F]">{{ implode(' dan ', $bundle->nama_consequent) }}</strong>.
            </p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card-kopi p-5 text-center">
                <p class="text-xs text-[#5F6D63] uppercase font-medium mb-1">Support</p>
                <p class="text-2xl font-bold text-[#08783F]">{{ $bundle->support_persen }}</p>
                <p class="text-xs text-[#5F6D63] mt-1">{{ $bundle->frekuensi }}x dari {{ $bundle->total_transaksi_dianalisa }} trx</p>
            </div>
            <div class="card-kopi p-5 text-center">
                <p class="text-xs text-[#5F6D63] uppercase font-medium mb-1">Confidence</p>
                <p class="text-2xl font-bold text-[#C98A2C]">{{ $bundle->confidence_persen }}</p>
                <p class="text-xs text-[#5F6D63] mt-1">Tingkat kepercayaan</p>
            </div>
            <div class="card-kopi p-5 text-center">
                <p class="text-xs text-[#5F6D63] uppercase font-medium mb-1">Lift</p>
                <p class="text-2xl font-bold {{ $bundle->lift_color === 'green' ? 'text-[#08783F]' : ($bundle->lift_color === 'amber' ? 'text-[#C98A2C]' : 'text-[#B64545]') }}">
                    {{ number_format($bundle->lift, 4) }}
                </p>
                <p class="text-xs text-[#5F6D63] mt-1">{{ $bundle->lift_label }}</p>
            </div>
            <div class="card-kopi p-5 text-center">
                <p class="text-xs text-[#5F6D63] uppercase font-medium mb-1">Status</p>
                <p class="text-2xl font-bold {{ $bundle->is_aktif ? 'text-[#08783F]' : 'text-[#5F6D63]' }}">
                    {{ $bundle->is_aktif ? 'Aktif' : 'Nonaktif' }}
                </p>
                @if($bundle->harga_bundle)
                <p class="text-xs text-[#5F6D63] mt-1">Rp {{ number_format($bundle->harga_bundle, 0, ',', '.') }}</p>
                @endif
            </div>
        </div>

        <div class="card-kopi p-6">
            <h3 class="font-semibold text-[#1F2937] mb-4">Interpretasi</h3>
            <div class="space-y-3 text-sm">
                <div class="flex gap-3 p-3 bg-[#EEF8ED] rounded-2xl">
                    <span class="text-[#08783F] font-black text-lg w-6 flex-shrink-0">S</span>
                    <div>
                        <strong class="text-gray-800">Support {{ $bundle->support_persen }}</strong>
                        — Kombinasi ini muncul pada {{ $bundle->support_persen }} dari semua transaksi
                        ({{ $bundle->frekuensi }} kali dalam {{ $bundle->total_transaksi_dianalisa }} transaksi).
                    </div>
                </div>
                <div class="flex gap-3 p-3 bg-[#F8F0D8] rounded-2xl">
                    <span class="text-[#C98A2C] font-black text-lg w-6 flex-shrink-0">C</span>
                    <div>
                        <strong class="text-gray-800">Confidence {{ $bundle->confidence_persen }}</strong>
                        — Dari semua transaksi yang mengandung <em>{{ implode(' + ', $bundle->nama_antecedent) }}</em>,
                        sebanyak {{ $bundle->confidence_persen }} juga mengandung
                        <em>{{ implode(' + ', $bundle->nama_consequent) }}</em>.
                    </div>
                </div>
                <div class="flex gap-3 p-3 {{ $bundle->lift >= 1 ? 'bg-[#EEF8ED]' : 'bg-[#FCEAEA]' }} rounded-2xl">
                    <span class="{{ $bundle->lift >= 1 ? 'text-[#08783F]' : 'text-[#B64545]' }} font-black text-lg w-6 flex-shrink-0">L</span>
                    <div>
                        <strong class="text-gray-800">Lift {{ number_format($bundle->lift, 4) }}</strong>
                        @if($bundle->lift > 1)
                        — Kombinasi ini <strong>{{ $bundle->lift_label }}</strong>.
                          Produk saling mendorong penjualan. Bundle ini layak dijadikan promosi.
                        @elseif($bundle->lift == 1)
                        — Tidak ada hubungan antara kedua produk ini.
                        @else
                        — Kedua produk cenderung tidak dibeli bersamaan. Tidak direkomendasikan untuk bundle.
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-kopi p-6">
            <h3 class="font-semibold text-[#1F2937] mb-4">Info Analisis</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs font-medium">Rentang data</p>
                    <p class="font-medium text-gray-700 mt-0.5">
                        {{ \Carbon\Carbon::parse($bundle->rentang_dari)->format('d M Y') }} —
                        {{ \Carbon\Carbon::parse($bundle->rentang_sampai)->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium">Total transaksi dianalisa</p>
                    <p class="font-medium text-gray-700 mt-0.5">{{ $bundle->total_transaksi_dianalisa }} transaksi</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium">Min. support dipakai</p>
                    <p class="font-medium text-gray-700 mt-0.5">{{ $bundle->min_support }}%</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium">Min. confidence dipakai</p>
                    <p class="font-medium text-gray-700 mt-0.5">{{ $bundle->min_confidence }}%</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium">Dianalisa oleh</p>
                    <p class="font-medium text-gray-700 mt-0.5">{{ $bundle->pembuatBundle->name }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium">Waktu analisis</p>
                    <p class="font-medium text-gray-700 mt-0.5">{{ $bundle->dianalisa_pada->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <form method="POST" action="{{ route('bundle.aktifkan', $bundle) }}" id="bundle-aktif-form"> 
                @csrf
                @if($bundle->is_aktif)
                <button type="submit" onclick="return confirm('Nonaktifkan bundle ini?')"
                        class="btn-kopi-danger text-sm">
                    Nonaktifkan Bundle
                </button>
                @else
                <div class="flex items-center gap-3">
                    <div class="text-sm">
                        <label class="block text-gray-600 text-xs">Harga bundle (opsional)</label>
                        <input type="number" name="harga_bundle" step="0.01" min="0"
                               value="{{ old('harga_bundle') }}"
                               placeholder="Rp {{ number_format($estimatedHargaBundle ?? 0, 0, ',', '.') }}"
                               class="mt-1 block w-52 border rounded px-3 py-2 text-sm" />
                        <p class="text-xs text-gray-400 mt-1">Kosongkan untuk menggunakan harga terbaik: <strong>Rp {{ number_format($estimatedHargaBundle ?? 0, 0, ',', '.') }}</strong></p>
                    </div>
                    <button type="button" id="open-activate-modal" class="btn-kopi text-sm">Aktifkan sebagai Promosi</button>
                </div>
                @endif
            </form>

            <div id="activate-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                <div class="bg-white rounded-[26px] border border-[#EAE6D8] z-10 max-w-lg w-full p-6">
                    <h3 class="text-lg font-semibold mb-2 text-[#064C32]">Konfirmasi Aktivasi Bundle</h3>
                    <p class="text-sm text-gray-600 mb-4">Periksa rincian harga bundle sebelum mengaktifkan. Jika Anda tetap kosongkan kolom harga, sistem akan menggunakan harga terbaik:</p>

                    <div class="space-y-2 mb-4">
                        @foreach($estimatedBreakdown ?? [] as $item)
                        <div class="flex justify-between text-sm">
                            <div class="font-medium text-[#1D2A1F]">{{ $item['nama'] }}</div>
                            <div class="text-right text-gray-600">
                                @if($item['promo_percent'] > 0)
                                    <div>Rp {{ number_format($item['harga'], 0, ',', '.') }} → <span class="text-[#08783F]">-{{ $item['promo_percent'] }}%</span></div>
                                    <div class="font-semibold">Rp {{ number_format($item['harga_efektif'], 0, ',', '.') }}</div>
                                @else
                                    <div class="font-semibold">Rp {{ number_format($item['harga'], 0, ',', '.') }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between border-t pt-3">
                        <div class="text-sm text-gray-700">Estimasi total</div>
                        <div class="text-lg font-bold text-[#064C32]">Rp {{ number_format($estimatedHargaBundle ?? 0, 0, ',', '.') }}</div>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" id="cancel-activate" class="btn-kopi-secondary text-sm">Batal</button>
                        <button type="button" id="confirm-activate" class="btn-kopi text-sm">Aktifkan Bundle</button>
                    </div>
                </div>
            </div>

            <script>
                (function(){
                    const openBtn = document.getElementById('open-activate-modal');
                    const modal = document.getElementById('activate-modal');
                    const cancel = document.getElementById('cancel-activate');
                    const confirm = document.getElementById('confirm-activate');
                    const form = document.getElementById('bundle-aktif-form');

                    if (openBtn) {
                        openBtn.addEventListener('click', function(){
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');
                        });
                    }
                    if (cancel) {
                        cancel.addEventListener('click', function(){
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        });
                    }
                    if (confirm) {
                        confirm.addEventListener('click', function(){
                            form.submit();
                        });
                    }
                })();
            </script>
            <a href="{{ route('bundle.index') }}"
               class="btn-kopi-secondary text-sm">
                ← Kembali
            </a>
        </div>

    </div>
</x-app-layout>
