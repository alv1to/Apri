<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body        { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; margin: 20px; }
        h1          { font-size: 16px; margin: 0 0 2px; }
        .sub        { color: #6b7280; font-size: 10px; margin-bottom: 16px; }
        .cards      { display: flex; gap: 10px; margin-bottom: 16px; }
        .card       { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; }
        .card-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        .card-val   { font-size: 14px; font-weight: bold; color: #4F46E5; margin-top: 2px; }
        table       { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th          { background: #4F46E5; color: white; padding: 7px 8px; text-align: left; font-size: 10px; }
        td          { padding: 7px 8px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .text-right { text-align: right; }
        .footer     { margin-top: 24px; font-size: 9px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        strong.title { font-size: 11px; color: #374151; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan</h1>
    <p class="sub">
        Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} —
        {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}
    </p>

    <div class="cards">
        <div class="card">
            <div class="card-label">Total Transaksi</div>
            <div class="card-val">{{ number_format($ringkasan->total_transaksi) }}</div>
        </div>
        <div class="card">
            <div class="card-label">Total Omzet</div>
            <div class="card-val" style="color:#059669">
                Rp {{ number_format($ringkasan->total_omzet, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <strong class="title">10 Produk Terlaris</strong>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th class="text-right">Terjual</th>
                <th class="text-right">Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produkTerlaris as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->produk->nama ?? '-' }}</td>
                <td>{{ ucfirst($item->produk->kategori ?? '-') }}</td>
                <td class="text-right">{{ number_format($item->total_terjual) }}</td>
                <td class="text-right">{{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#9ca3af">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d M Y H:i') }} — Dashboard Promosi Penjualan
    </div>
</body>
</html>
