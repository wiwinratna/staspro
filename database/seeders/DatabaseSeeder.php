<?php
namespace Database\Seeders;

use App\Models\Sumberdana;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => Hash::make('123123123'),
            'role'     => 'admin',
        ]);

        Sumberdana::insert([
            [
                'id'                => 1,
                'nama_sumber_dana'  => 'Dana Penelitian',
                'jenis_pendanaan'   => 'internal',
                'keterangan'        => 'Pendanaan untuk Penelitian',
                'anggaran_maksimal' => 1000000,
                'tgl_berlaku'       => date_add(now(), date_interval_create_from_date_string('30 days')),
                'user_id_created'   => 1,
                'user_id_updated'   => 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ], [
                'id'                => 2,
                'nama_sumber_dana'  => 'Kedaireka',
                'jenis_pendanaan'   => 'eksternal',
                'keterangan'        => 'Pendanaan dari Kedaireka',
                'anggaran_maksimal' => 1000000,
                'tgl_berlaku'       => date_add(now(), date_interval_create_from_date_string('30 days')),
                'user_id_created'   => 1,
                'user_id_updated'   => 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}
