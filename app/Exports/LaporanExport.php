<?php

namespace App\Exports;

use App\Models\DetailTransaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected string $dari;
    protected string $sampai;

    public function __construct(string $dari, string $sampai)
    {
        $this->dari   = $dari;
        $this->sampai = $sampai;
    }

    public function collection()
    {
        return DetailTransaksi::with(['transaksi', 'produk'])
            ->whereHas('transaksi', function ($q) {
                $q->whereDate('tanggal_transaksi', '>=', $this->dari)
                  ->whereDate('tanggal_transaksi', '<=', $this->sampai)
                  ->where('status', 'selesai');
            })
            ->get()
            ->map(fn($d) => [
                'kode_transaksi' => $d->transaksi->kode_transaksi,
                'tanggal'        => $d->transaksi->tanggal_transaksi->format('d/m/Y H:i'),
                'nama_produk'    => $d->nama_produk,
                'kategori'       => $d->produk->kategori ?? '-',
                'jumlah'         => $d->jumlah,
                'harga_satuan'   => (float) $d->harga_satuan,
                'subtotal'       => (float) $d->subtotal,
            ]);
    }

    public function headings(): array
    {
        return ['Kode Transaksi', 'Tanggal', 'Nama Produk', 'Kategori', 'Jumlah', 'Harga Satuan (Rp)', 'Subtotal (Rp)'];
    }

    public function title(): string
    {
        return 'Laporan ' . $this->dari . ' sd ' . $this->sampai;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
