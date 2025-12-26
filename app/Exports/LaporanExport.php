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
    protected $pencatatanKeuangans;
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $metodePembayaran;
    protected $kategoriPendanaan;
    protected $timPeneliti;

    public function __construct($pencatatanKeuangans, $tanggalAwal, $tanggalAkhir, $metodePembayaran = null, $kategoriPendanaan = null, $timPeneliti = null)
    {
        $this->pencatatanKeuangans = $pencatatanKeuangans;
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
        $data = $this->pencatatanKeuangans->sortBy('created_at')->values();
        $result = collect([]);

        $rows = $data->map(function ($pencatatanKeuangan, $index) use (&$saldo, &$totalDebit, &$totalKredit) {
            $debit = $pencatatanKeuangan->jenis_transaksi === 'pemasukan' ? $pencatatanKeuangan->jumlah_transaksi : 0;
            $kredit = $pencatatanKeuangan->jenis_transaksi === 'pengeluaran' ? $pencatatanKeuangan->jumlah_transaksi : 0;

            $totalDebit += $debit;
            $totalKredit += $kredit;
            $saldo += ($debit - $kredit);

            return [
                $index + 1,
                $pencatatanKeuangan->created_at->format('d-m-Y H:i'),
                $pencatatanKeuangan->project->nama_project ?? '-',
                ucfirst($pencatatanKeuangan->deskripsi_transaksi),
                ucfirst($pencatatanKeuangan->metode_pembayaran),
                ucwords($pencatatanKeuangan->project->sumberDana->jenis_pendanaan ?? '-'),
                $debit > 0 ? 'Rp ' . number_format($debit, 0, ',', '.') : '',
                $kredit > 0 ? 'Rp ' . number_format($kredit, 0, ',', '.') : '',
                'Rp ' . number_format($saldo, 0, ',', '.'),
            ];
        });

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
        ];

        // Menambahkan keterangan berdasarkan filter
        if ($this->tanggalAwal && $this->tanggalAkhir) {
            $headings[] = ['Periode: ' . $this->tanggalAwal . ' - ' . $this->tanggalAkhir];
        }

        // Baris ketiga kosong
        $headings[] = [];

        // Header kolom
        $headings[] = ['No.', 'Tanggal', 'Tim Peneliti', 'Deskripsi Transaksi', 'Metode Pembayaran', 'Sumber Dana', 'Debit (Rp )', 'Kredit (Rp)', 'Saldo (Rp)'];

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

        // Baris ketiga kosong, tidak perlu styling

        // Styling header tabel di baris 4
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '006400']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);

        $jumlahBarisData = count($this->pencatatanKeuangans);
        $barisTotal = 4 + $jumlahBarisData + 1; // Baris 4 = header, data mulai baris 5, + jumlah data, + 1 untuk total

        $sheet->mergeCells("A{$barisTotal}:F{$barisTotal}");
        $sheet->setCellValue("A{$barisTotal}", 'Total');

        $sheet->getStyle("A{$barisTotal}:I{$barisTotal}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '006400']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);

        // Borders dan alignment data (dari baris 5 sampai baris total)
        $sheet->getStyle("A5:I{$barisTotal}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);

        // Rata kanan untuk kolom nilai nominal (G,H,I)
        $sheet->getStyle("G5:I{$barisTotal}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
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