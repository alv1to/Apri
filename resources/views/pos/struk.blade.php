<x-app-layout>
    <x-slot name="title">Struk Transaksi</x-slot>

    <div class="max-w-sm mx-auto">

        {{-- Struk --}}
        <div class="card-kopi rounded-[28px] p-6" id="struk">

            <div class="text-center mb-5 pb-4 border-b border-dashed">
                <h1 class="text-lg font-bold text-gray-800">KOPI KAMA</h1>
                <p class="text-xs text-gray-400 mt-1">{{ now()->format('d M Y, H:i') }}</p>
            </div>

            <div class="space-y-1 text-sm mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-500">No. Transaksi</span>
                    <span class="font-mono text-xs font-medium">{{ $transaksi->kode_transaksi }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Kasir</span>
                    <span class="font-medium">{{ $transaksi->user->name }}</span>
                </div>
            </div>

            <div class="border-t border-dashed pt-4 mb-4">
                @foreach($transaksi->details as $d)
                <div class="flex justify-between items-start py-1.5 text-sm">
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $d->nama_produk }}</p>
                        <p class="text-xs text-gray-400">
                            Rp {{ number_format($d->harga_satuan, 0, ',', '.') }} &times; {{ $d->jumlah }}
                        </p>
                        @if(!empty($d->diskon) && $d->diskon > 0)
                        <p class="text-xs text-red-600">Potongan: Rp {{ number_format($d->diskon, 0, ',', '.') }}</p>
                        @endif
                    </div>
                    <span class="font-medium text-gray-700 ml-3">
                        Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                    </span>
                </div>
                @endforeach
            </div>

            <div class="border-t border-dashed pt-4 space-y-1 text-sm">
                @php
                    $totalDiskon = $transaksi->details->sum('diskon');
                @endphp
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
                    <span>Metode Pembayaran</span>
                    <span>{{ $transaksi->payment_method_label }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Bayar</span>
                    <span>Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-green-600 font-semibold">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-xs text-gray-500">Scan barcode di bawah untuk menampilkan jumlah yang harus dibayar.</p>
            </div>

            <div class="mt-4 text-center">
                <canvas id="qrcode-canvas" class="mx-auto" aria-label="QR Code"></canvas>
                <p class="text-xs text-gray-400 mt-2">{{ $transaksi->kode_transaksi }}</p>
            </div>

            <!-- <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                <label class="block text-xs text-slate-500 mb-1">Scan QR Code di sini</label>
                <input id="scan-input" type="text" autocomplete="off"
                       class="w-full border rounded-lg px-3 py-2 text-sm"
                       placeholder="Arahkan scanner ke sini" autofocus>
                <div id="scan-result" class="mt-3 text-sm text-slate-700"></div>
            </div> -->

            <div class="text-center mt-5 pt-4 border-t border-dashed">
                <p class="text-xs text-gray-400">Terima kasih telah berkunjung!</p>
            </div>
        </div>

        {{-- Aksi --}}
        <div class="flex gap-3 mt-4">
            <a href="{{ route('pos.index') }}"
               class="flex-1 bg-[#08783F] text-white py-3 rounded-xl font-semibold text-sm
                      text-center hover:bg-[#064C32] transition">
                + Transaksi Baru
            </a>
            <button onclick="window.print()"
                    class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold text-sm
                           hover:bg-gray-300 transition">
                Print Struk
            </button>
        </div>
    </div>

<style>
@media print {
    body * { visibility: hidden; }
    #struk, #struk * { visibility: visible; }
    #struk { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const qrcodeCanvas = document.getElementById('qrcode-canvas');
        const scanInput = document.getElementById('scan-input');
        const scanResult = document.getElementById('scan-result');
        const total = {{ $transaksi->total_harga }};
        const code = '{{ $transaksi->kode_transaksi }}';

        QRCode.toCanvas(qrcodeCanvas, code, {
            width: 200,
            margin: 1,
            color: {
                dark: '#111827',
                light: '#F8FAFC'
            }
        }, function (error) {
            if (error) {
                qrcodeCanvas.parentNode.innerHTML = '<p class="text-xs text-red-600">Gagal membuat QR code.</p>';
                console.error(error);
            }
        });

        scanInput.addEventListener('input', function () {
            const value = this.value.trim();
            if (!value) {
                scanResult.textContent = '';
                return;
            }
            if (value === code) {
                scanResult.innerHTML = `<span class="font-semibold text-green-700">Kode valid.</span> Jumlah yang harus dibayar: <strong>Rp ${total.toLocaleString('id')}</strong>`;
            } else {
                scanResult.innerHTML = `<span class="font-semibold text-red-600">Kode tidak dikenal.</span>`;
            }
        });

        scanInput.focus();
    });
</script>
</x-app-layout>
