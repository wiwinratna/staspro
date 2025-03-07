<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $transaksis;

    public function __construct($transaksis)
    {
        $this->transaksis = $transaksis;
    }

    public function collection()
    {
        return $this->transaksis;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN KEUANGAN'], // Judul Besar di Atas
            [
                'No.',
                'Tanggal',
                'Tim Peneliti',
                'Jenis Transaksi',
                'Deskripsi Transaksi',
                'Jumlah Transaksi',
                'Metode Pembayaran',
                'Kategori Transaksi',
            ]
        ];
    }

    public function map($transaksi): array
    {
        static $nomor = 0;
        $nomor++;

        return [
            $nomor,
            $transaksi->tanggal,
            $transaksi->tim_peneliti,
            $transaksi->jenis_transaksi,
            $transaksi->deskripsi_transaksi,
            number_format($transaksi->jumlah_transaksi, 0, ',', '.'), // Format ribuan
            strtoupper($transaksi->metode_pembayaran),
            $transaksi->kategori_transaksi,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Membuat Judul Besar di Tengah & Berwarna
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['size' => 14, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
        ]);

        // Membuat Header Lebih Menonjol
        $sheet->getStyle('A2:H2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F497D']],
        ]);

        // Menambahkan Border ke Seluruh Tabel
        $sheet->getStyle("A2:H$highestRow")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => '000000']],
            ],
        ]);

        // **Tambah Baris Total Transaksi**
        $lastRow = $highestRow + 1;
        $sheet->setCellValue("E$lastRow", "TOTAL TRANSAKSI");
        $sheet->setCellValue("F$lastRow", "=SUM(F3:F$highestRow)");

        // Styling untuk total transaksi
        $sheet->getStyle("E$lastRow:F$lastRow")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
        ]);

        // **Menghapus Conditional Formatting agar isiannya tetap putih**
        foreach (range(3, $highestRow) as $row) {
            $sheet->getStyle("A$row:H$row")->applyFromArray([
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE],
            ]);
        }

        // **Freeze Header agar tetap terlihat saat scroll**
        $sheet->freezePane('A3');

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // Tanggal
            'C' => 25,  // Tim Peneliti
            'D' => 20,  // Jenis Transaksi
            'E' => 40,  // Deskripsi Transaksi
            'F' => 15,  // Jumlah Transaksi
            'G' => 20,  // Metode Pembayaran
            'H' => 20,  // Kategori Transaksi
        ];
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }
}
