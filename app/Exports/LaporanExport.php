<?php

namespace App\Exports;

// Import interface dari Maatwebsite Excel untuk ekspor data
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

// Import style dari PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Kelas untuk ekspor laporan keuangan ke Excel
class LaporanExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    // Properti untuk menampung data dan filter
    protected $transaksis;
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $metodePembayaran;
    protected $kategoriPendanaan;
    protected $timPeneliti;

    // Konstruktor untuk inisialisasi properti
    public function __construct($transaksis, $tanggalAwal, $tanggalAkhir, $metodePembayaran = null, $kategoriPendanaan = null, $timPeneliti = null)
    {
        $this->transaksis = $transaksis;
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->metodePembayaran = $metodePembayaran;
        $this->kategoriPendanaan = $kategoriPendanaan;
        $this->timPeneliti = $timPeneliti;
    }

    // Method utama untuk mengatur data yang akan diekspor
    public function collection()
    {
        $saldo = 0;
        $totalDebit = 0;
        $totalKredit = 0;
        $data = $this->transaksis->sortBy('created_at')->values(); // Urutkan berdasarkan waktu
        $result = collect([]);

        // Proses data transaksi baris per baris
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

        // Tambahkan baris total ke akhir tabel
        $rows->push([
            '', '', '', '', '', 'Total',
            'Rp ' . number_format($totalDebit, 0, ',', '.'),
            'Rp ' . number_format($totalKredit, 0, ',', '.'),
            'Rp ' . number_format($saldo, 0, ',', '.'),
        ]);

        return $result->merge($rows);
    }

    // Menentukan heading untuk sheet
    public function headings(): array
    {
        $headings = [
            ['LAPORAN KEUANGAN'], // Judul utama
            $subJudul = [] // Kosong dulu, bisa diisi dinamis
        ];

        // Tambahkan filter jika tersedia
        if ($this->metodePembayaran) {
            $headings[] = ['Metode Pembayaran: ' . ucfirst($this->metodePembayaran)];
        }

        if ($this->kategoriPendanaan) {
            $headings[] = ['Kategori Pendanaan: ' . ucfirst($this->kategoriPendanaan)];
        }

        if ($this->timPeneliti) {
            $headings[] = ['Tim Peneliti: ' . ucfirst($this->timPeneliti)];
        }

        $headings[] = []; // Baris kosong sebelum tabel utama

        // Header tabel utama
        $headings[] = ['No.', 'Tanggal', 'Tim Peneliti', 'Deskripsi Transaksi', 'Metode Pembayaran', 'Sumber Dana', 'Debit (Rp)', 'Kredit (Rp)', 'Saldo (Rp)'];

        return $headings;
    }

    // Style untuk lembar Excel
    public function styles(Worksheet $sheet)
    {
        // Merge dan style judul
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

        // Style header kolom (baris ke-4)
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '006400']], // hijau tua
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);

        // Hitung baris terakhir untuk baris total
        $jumlahBarisData = count($this->transaksis);
        $barisTotal = $jumlahBarisData + 5; // baris data + header

        // Merge cell dan isi total
        $sheet->mergeCells("A{$barisTotal}:F{$barisTotal}");
        $sheet->setCellValue("A{$barisTotal}", 'Total');

        // Style baris total
        $sheet->getStyle("A{$barisTotal}:I{$barisTotal}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '006400']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);

        // Border untuk seluruh tabel
        $sheet->getStyle("A5:I{$barisTotal}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ]);
    }

    // Atur lebar kolom
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No.
            'B' => 18,  // Tanggal
            'C' => 25,  // Tim Peneliti
            'D' => 35,  // Deskripsi Transaksi
            'E' => 20,  // Metode Pembayaran
            'F' => 20,  // Sumber Dana
            'G' => 15,  // Debit
            'H' => 15,  // Kredit
            'I' => 20,  // Saldo
        ];
    }
}
