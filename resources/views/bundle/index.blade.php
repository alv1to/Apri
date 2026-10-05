<x-app-layout>
    <x-slot name="title">Generate Bundle Rekomendasi</x-slot>

    <div class="space-y-6">

        {{-- Form Generate --}}
        <div class="card-kopi p-6">
            <h3 class="font-semibold text-[#1F2937] mb-1">Parameter Analisis Apriori</h3>
            <p class="text-sm text-gray-500 mb-5">
                Sistem menganalisa pola pembelian dari transaksi untuk menemukan kombinasi produk yang sering dibeli bersamaan.
            </p>

            <form method="POST" action="{{ route('bundle.generate') }}" id="form-generate">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="dari"
                               value="{{ old('dari', now()->subDays(30)->toDateString()) }}"
                               class="input-kopi">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="sampai"
                               value="{{ old('sampai', now()->toDateString()) }}"
                               class="input-kopi">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Min. Support (%)
                            <span class="text-gray-400 font-normal" title="% transaksi yang memuat kombinasi ini">ⓘ</span>
                        </label>
                        <input type="number" name="min_support" value="{{ old('min_support', 5) }}"
                               min="1" max="100"
                               class="input-kopi">
                        <p class="text-xs text-gray-400 mt-1">Rekomendasi: 5–15%</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Min. Confidence (%)
                            <span class="text-gray-400 font-normal" title="Seberapa sering B dibeli jika A dibeli">ⓘ</span>
                        </label>
                        <input type="number" name="min_confidence" value="{{ old('min_confidence', 30) }}"
                               min="1" max="100"
                               class="input-kopi">
                        <p class="text-xs text-gray-400 mt-1">Rekomendasi: 30–60%</p>
                    </div>
                </div>

                <button type="submit" id="btn-generate"
                        class="btn-kopi flex items-center gap-2 justify-center">
                    <span id="btn-label">Jalankan Analisis Apriori</span>
                    <svg id="spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </button>
            </form>
        </div>

        {{-- Statistik --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card-kopi p-4 text-center">
                <p class="text-xs text-[#6B7280] uppercase tracking-wider">Total Rules</p>
                <p class="text-3xl font-bold text-[#08783F] mt-1">{{ $stats['total_bundle'] }}</p>
            </div>
            <div class="card-kopi p-4 text-center">
                <p class="text-xs text-[#6B7280] uppercase tracking-wider">Bundle Aktif</p>
                <p class="text-3xl font-bold text-[#064C32] mt-1">{{ $stats['bundle_aktif'] }}</p>
            </div>
            <div class="card-kopi p-4 text-center">
                <p class="text-xs text-[#6B7280] uppercase tracking-wider">Lift Tertinggi</p>
                <p class="text-3xl font-bold text-[#C98A2C] mt-1">{{ number_format($stats['lift_tertinggi'], 2) }}</p>
            </div>
            <div class="card-kopi p-4 text-center">
                <p class="text-xs text-[#6B7280] uppercase tracking-wider">Avg Confidence</p>
                <p class="text-3xl font-bold text-[#0E7D46] mt-1">{{ number_format($stats['avg_confidence'] * 100, 1) }}%</p>
            </div>
        </div>

        {{-- Tabel rules --}}
        <div class="card-kopi overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Hasil Association Rules</h3>
                <span class="text-xs text-gray-400">Diurutkan berdasarkan lift tertinggi</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jika beli</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rekomendasikan</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Support</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Confidence</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Lift</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Frekuensi</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($bundle as $item)
                        <tr class="hover:bg-gray-50 {{ $item->is_aktif ? 'bg-green-50' : '' }}">
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item->nama_antecedent as $n)
                                    <span class="bg-[#EAF7ED] text-[#0E7D46] text-xs px-2 py-0.5 rounded-full">{{ $n }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item->nama_consequent as $n)
                                    <span class="bg-[#F9F2E6] text-[#A56617] text-xs px-2 py-0.5 rounded-full">{{ $n }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="text-sm font-medium text-gray-700">{{ $item->support_persen }}</div>
                                <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                    <div class="bg-[#08783F] h-1 rounded-full" style="width: {{ min(100, $item->support * 500) }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="text-sm font-medium text-gray-700">{{ $item->confidence_persen }}</div>
                                <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                    <div class="bg-[#C98A2C] h-1 rounded-full" style="width: {{ $item->confidence * 100 }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $item->lift_color === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $item->lift_color === 'amber' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $item->lift_color === 'red'   ? 'bg-red-100 text-red-700'   : '' }}">
                                    {{ number_format($item->lift, 2) }} ({{ $item->lift_label }})
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $item->frekuensi }}x</td>
                            <td class="px-4 py-3 text-center">
                                @if($item->is_aktif)
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">Aktif</span>
                                @else
                                <span class="bg-gray-100 text-gray-500 text-xs px-2 py-1 rounded-full">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('bundle.show', $item) }}"
                                       class="text-[#08783F] hover:text-[#064C32] text-xs font-medium">Detail</a>

                                    <button type="button"
                                            onclick="toggleAktif({{ $item->id }}, {{ $item->is_aktif ? 'true' : 'false' }}, '{{ addslashes(implode(' + ', $item->nama_antecedent) . ' → ' . implode(' + ', $item->nama_consequent)) }}')"
                                            class="text-xs font-medium {{ $item->is_aktif ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' }}">
                                        {{ $item->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>

                                    @if(!$item->is_aktif)
                                    <form method="POST" action="{{ route('bundle.destroy', $item) }}"
                                          onsubmit="return confirm('Hapus rule ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-medium">Hapus</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center text-gray-400">
                                Belum ada hasil analisis. Jalankan Apriori di atas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t">{{ $bundle->links() }}</div>
        </div>
    </div>

{{-- Modal aktifkan --}}
<div id="modal-aktifkan" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="card-kopi p-6 w-full max-w-md mx-4">
        <h3 class="font-semibold text-[#1F2937] mb-1">Aktifkan Bundle Promosi</h3>
        <p class="text-sm text-[#6B7280] mb-5" id="modal-nama-bundle"></p>
        <form method="POST" id="form-aktifkan">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium text-kopi-dark mb-1">Harga Bundle (Rp) — opsional</label>
                <input type="number" name="harga_bundle" min="0" step="500"
                       class="input-kopi"
                       placeholder="Kosongkan untuk harga normal">
                <p class="text-xs text-[#6B7280] mt-1">Isi jika ingin memberikan harga spesial untuk bundle ini.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="btn-kopi">
                    Aktifkan
                </button>
                <button type="button" onclick="tutupModal()"
                        class="btn-kopi-secondary">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Spinner saat generate
document.getElementById('form-generate').addEventListener('submit', function () {
    document.getElementById('btn-label').textContent = 'Memproses...';
    document.getElementById('spinner').classList.remove('hidden');
    document.getElementById('btn-generate').disabled = true;
});

function toggleAktif(id, isAktif, nama) {
    if (isAktif) {
        if (!confirm('Nonaktifkan bundle "' + nama + '"?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/bundle/' + id + '/aktifkan';
        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
        document.body.appendChild(form);
        form.submit();
        return;
    }
    document.getElementById('modal-nama-bundle').textContent = nama;
    document.getElementById('form-aktifkan').action = '/bundle/' + id + '/aktifkan';
    document.getElementById('modal-aktifkan').classList.remove('hidden');
}

function tutupModal() {
    document.getElementById('modal-aktifkan').classList.add('hidden');
}

document.getElementById('modal-aktifkan').addEventListener('click', function (e) {
    if (e.target === this) tutupModal();
});
</script>
</x-app-layout>
