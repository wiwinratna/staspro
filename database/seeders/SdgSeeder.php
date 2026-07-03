<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SdgSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sdgs = [
            ['nomor' => 1, 'nama' => 'Tanpa Kemiskinan', 'warna' => '#e5243b'],
            ['nomor' => 2, 'nama' => 'Tanpa Kelaparan', 'warna' => '#dda63a'],
            ['nomor' => 3, 'nama' => 'Kehidupan Sehat dan Sejahtera', 'warna' => '#4c9f38'],
            ['nomor' => 4, 'nama' => 'Pendidikan Berkualitas', 'warna' => '#c5192d'],
            ['nomor' => 5, 'nama' => 'Kesetaraan Gender', 'warna' => '#ff3a21'],
            ['nomor' => 6, 'nama' => 'Air Bersih dan Sanitasi Layak', 'warna' => '#26bde2'],
            ['nomor' => 7, 'nama' => 'Energi Bersih dan Terjangkau', 'warna' => '#fcc30b'],
            ['nomor' => 8, 'nama' => 'Pekerjaan Layak dan Pertumbuhan Ekonomi', 'warna' => '#a21942'],
            ['nomor' => 9, 'nama' => 'Industri, Inovasi dan Infrastruktur', 'warna' => '#fd6925'],
            ['nomor' => 10, 'nama' => 'Berkurangnya Kesenjangan', 'warna' => '#dd1367'],
            ['nomor' => 11, 'nama' => 'Kota dan Pemukiman yang Berkelanjutan', 'warna' => '#fd9d24'],
            ['nomor' => 12, 'nama' => 'Konsumsi dan Produksi yang Bertanggung Jawab', 'warna' => '#bf8b2e'],
            ['nomor' => 13, 'nama' => 'Penanganan Perubahan Iklim', 'warna' => '#3f7e44'],
            ['nomor' => 14, 'nama' => 'Ekosistem Lautan', 'warna' => '#0a97d9'],
            ['nomor' => 15, 'nama' => 'Ekosistem Daratan', 'warna' => '#56c02b'],
            ['nomor' => 16, 'nama' => 'Perdamaian, Keadilan dan Kelembagaan yang Tangguh', 'warna' => '#00689d'],
            ['nomor' => 17, 'nama' => 'Kemitraan untuk Mencapai Tujuan', 'warna' => '#19486a'],
        ];

        foreach ($sdgs as $sdg) {
            \App\Models\Sdg::create($sdg);
        }
    }
}
