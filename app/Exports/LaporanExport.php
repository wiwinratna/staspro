<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanExport implements FromCollection, WithHeadings
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
            'No.',
            'Tanggal',
            'Tim Peneliti',
            'Jenis Transaksi',
            'Deskripsi Transaksi',
            'Jumlah Transaksi',
            'Metode Pembayaran',
            'Kategori Transaksi',
        ];
    }
}
