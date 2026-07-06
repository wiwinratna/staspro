<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Jenis Project
        $jenisPenelitianId = DB::table('jenis_project')->insertGetId([
            'nama' => 'Penelitian',
            'kode' => 'PNL',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $jenisAbdimasId = DB::table('jenis_project')->insertGetId([
            'nama' => 'Abdimas',
            'kode' => 'ABD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Jenis Pendanaan
        $jenisInternalId = DB::table('jenis_pendanaan')->insertGetId([
            'nama' => 'Internal',
            'kode' => 'INT',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jenisEksternalId = DB::table('jenis_pendanaan')->insertGetId([
            'nama' => 'Eksternal',
            'kode' => 'EKS',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Provider Pendanaan
        $providerStasId = DB::table('provider_pendanaan')->insertGetId([
            'nama' => 'LPPM STAS',
            'singkatan' => 'STAS',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $providerDppmId = DB::table('provider_pendanaan')->insertGetId([
            'nama' => 'DPPM Kemdiktisaintek',
            'singkatan' => 'DPPM',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Komponen Biaya
        $komponen = [
            'Honorarium' => DB::table('komponen_biaya')->insertGetId(['nama' => 'Honorarium', 'created_at' => now(), 'updated_at' => now()]),
            'Belanja Bahan' => DB::table('komponen_biaya')->insertGetId(['nama' => 'Belanja Bahan', 'created_at' => now(), 'updated_at' => now()]),
            'Perjalanan' => DB::table('komponen_biaya')->insertGetId(['nama' => 'Perjalanan', 'created_at' => now(), 'updated_at' => now()]),
            'Publikasi' => DB::table('komponen_biaya')->insertGetId(['nama' => 'Publikasi', 'created_at' => now(), 'updated_at' => now()]),
            'Pelatihan' => DB::table('komponen_biaya')->insertGetId(['nama' => 'Pelatihan', 'created_at' => now(), 'updated_at' => now()]),
            'Luaran' => DB::table('komponen_biaya')->insertGetId(['nama' => 'Luaran', 'created_at' => now(), 'updated_at' => now()]),
            'Dokumentasi' => DB::table('komponen_biaya')->insertGetId(['nama' => 'Dokumentasi', 'created_at' => now(), 'updated_at' => now()]),
        ];

        // 5. Skema Pendanaan
        $skemaPklnId = DB::table('skema_pendanaan')->insertGetId([
            'kode' => 'PKLN',
            'nama' => 'Penelitian Kolaborasi Luar Negeri',
            'jenis_project_id' => $jenisPenelitianId,
            'jenis_pendanaan_id' => $jenisEksternalId,
            'provider_id' => $providerDppmId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $skemaPiId = DB::table('skema_pendanaan')->insertGetId([
            'kode' => 'PI',
            'nama' => 'Penelitian Internal',
            'jenis_project_id' => $jenisPenelitianId,
            'jenis_pendanaan_id' => $jenisInternalId,
            'provider_id' => $providerStasId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $skemaPkmId = DB::table('skema_pendanaan')->insertGetId([
            'kode' => 'PKM',
            'nama' => 'Pengabdian Kemitraan Masyarakat',
            'jenis_project_id' => $jenisAbdimasId,
            'jenis_pendanaan_id' => $jenisEksternalId,
            'provider_id' => $providerDppmId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Skema Komponen
        // PKLN
        DB::table('skema_komponen')->insert([
            ['skema_pendanaan_id' => $skemaPklnId, 'komponen_biaya_id' => $komponen['Honorarium'], 'urutan' => 1, 'is_wajib' => true, 'created_at' => now(), 'updated_at' => now()],
            ['skema_pendanaan_id' => $skemaPklnId, 'komponen_biaya_id' => $komponen['Belanja Bahan'], 'urutan' => 2, 'is_wajib' => true, 'created_at' => now(), 'updated_at' => now()],
            ['skema_pendanaan_id' => $skemaPklnId, 'komponen_biaya_id' => $komponen['Perjalanan'], 'urutan' => 3, 'is_wajib' => false, 'created_at' => now(), 'updated_at' => now()],
            ['skema_pendanaan_id' => $skemaPklnId, 'komponen_biaya_id' => $komponen['Publikasi'], 'urutan' => 4, 'is_wajib' => false, 'created_at' => now(), 'updated_at' => now()],
            ['skema_pendanaan_id' => $skemaPklnId, 'komponen_biaya_id' => $komponen['Luaran'], 'urutan' => 5, 'is_wajib' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // PI
        DB::table('skema_komponen')->insert([
            ['skema_pendanaan_id' => $skemaPiId, 'komponen_biaya_id' => $komponen['Honorarium'], 'urutan' => 1, 'is_wajib' => true, 'created_at' => now(), 'updated_at' => now()],
            ['skema_pendanaan_id' => $skemaPiId, 'komponen_biaya_id' => $komponen['Belanja Bahan'], 'urutan' => 2, 'is_wajib' => true, 'created_at' => now(), 'updated_at' => now()],
            ['skema_pendanaan_id' => $skemaPiId, 'komponen_biaya_id' => $komponen['Perjalanan'], 'urutan' => 3, 'is_wajib' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // PKM
        DB::table('skema_komponen')->insert([
            ['skema_pendanaan_id' => $skemaPkmId, 'komponen_biaya_id' => $komponen['Honorarium'], 'urutan' => 1, 'is_wajib' => true, 'created_at' => now(), 'updated_at' => now()],
            ['skema_pendanaan_id' => $skemaPkmId, 'komponen_biaya_id' => $komponen['Pelatihan'], 'urutan' => 2, 'is_wajib' => true, 'created_at' => now(), 'updated_at' => now()],
            ['skema_pendanaan_id' => $skemaPkmId, 'komponen_biaya_id' => $komponen['Dokumentasi'], 'urutan' => 3, 'is_wajib' => true, 'created_at' => now(), 'updated_at' => now()],
            ['skema_pendanaan_id' => $skemaPkmId, 'komponen_biaya_id' => $komponen['Perjalanan'], 'urutan' => 4, 'is_wajib' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
