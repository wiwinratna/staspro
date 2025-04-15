<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $transaksis;
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $metodePembayaran;
    protected $kategoriPendanaan;
    protected $timPeneliti;

    public function __construct($transaksis, $tanggalAwal, $tanggalAkhir, $metodePembayaran = null, $kategoriPendanaan = null, $timPeneliti = null)
    {
        $this->transaksis = $transaksis;
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->metodePembayaran = $metodePembayaran;
        $this->kategoriPendanaan = $kategoriPendanaan;
        $this->timPeneliti = $timPeneliti;
    }

    public function collection()
    {
        $saldo = 0;
        $totalDebit = 0;
        $totalKredit = 0;
        $data = $this->transaksis->sortBy('created_at')->values();
        $result = collect([]);

        $rows = $data->map(function ($transaksi, $index) use (&$saldo, &$totalDebit, &$totalKredit) {
            $debit = $transaksi->jenis_transaksi === 'pemasukan' ? $transaksi->jumlah_transaksi : 0;
            $kredit = $transaksi->jenis_transaksi === 'pengeluaran' ? $transaksi->jumlah_transaksi : 0;

            $totalDebit += $debit;
            $totalKredit += $kredit;
            $saldo += ($debit - $kredit);

            return [
                $index + 1,
                $transaksi->created_at->format('d-m-Y H:i'),
                $transaksi->project->nama_project ?? '-',
                ucfirst($transaksi->deskripsi_transaksi),
                ucfirst($transaksi->metode_pembayaran),
                ucwords($transaksi->project->sumberDana->jenis_pendanaan ?? '-'),
                $debit > 0 ? 'Rp ' . number_format($debit, 0, ',', '.') : '',
                $kredit > 0 ? 'Rp ' . number_format($kredit, 0, ',', '.') : '',
                'Rp ' . number_format($saldo, 0, ',', '.'),
            ];
        });

        // Tambahkan baris total
        $rows->push([
            '', '', '', '', '', 'Total',
            'Rp ' . number_format($totalDebit, 0, ',', '.'),
            'Rp ' . number_format($totalKredit, 0, ',', '.'),
            'Rp ' . number_format($saldo, 0, ',', '.'),
        ]);

        return $result->merge($rows);
    }

    public function headings(): array
    {
        $headings = [
            ['LAPORAN KEUANGAN'],
            $subJudul = []
        ];

        // Menambahkan informasi filter jika ada
        if ($this->metodePembayaran) {
            $headings[] = ['Metode Pembayaran: ' . ucfirst($this->metodePembayaran)];
        }

        if ($this->kategoriPendanaan) {
            $headings[] = ['Kategori Pendanaan: ' . ucfirst($this->kategoriPendanaan)];
        }

        if ($this->timPeneliti) {
            $headings[] = ['Tim Peneliti: ' . ucfirst($this->timPeneliti)];
        }

        $headings[] = []; // Baris kosong sebelum header tabel
        $headings[] = ['No.', 'Tanggal', 'Tim Peneliti', 'Deskripsi Transaksi', 'Metode Pembayaran', 'Sumber Dana', 'Debit (Rp)', 'Kredit (Rp)', 'Saldo (Rp)'];

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Header Kolom (baris ke-4)
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '006400']], // hijau tua
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);

        // Baris total
        $jumlahBarisData = count($this->transaksis);
        $barisTotal = $jumlahBarisData + 5; // +4 header, +1 karena index dimulai dari 1

        $sheet->mergeCells("A{$barisTotal}:F{$barisTotal}");
        $sheet->setCellValue("A{$barisTotal}", 'Total');

        $sheet->getStyle("A{$barisTotal}:I{$barisTotal}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '006400']], // hijau tua
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);

        // Tambahkan border ke seluruh isi tabel dari baris 5 sampai baris total
        $sheet->getStyle("A5:I{$barisTotal}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 18,
            'C' => 25,
            'D' => 35,
            'E' => 20,
            'F' => 20,
            'G' => 15,
            'H' => 15,
            'I' => 20,
        ];
    }
}