<?php
use App\Models\Sumberdana;
use App\Models\SubkategoriSumberdana;
use App\Models\JenisProject;
use App\Models\JenisPendanaan;
use App\Models\ProviderPendanaan;
use App\Models\KomponenBiaya;
use App\Models\SkemaPendanaan;
use App\Models\SkemaKomponen;
use Illuminate\Support\Str;

echo "Memulai migrasi data Sumber Dana ke Skema Pendanaan...\n";

// Get all legacy sumber_dana (excluding shadow ones if any exist)
$sumberDanas = Sumberdana::where('nama_sumber_dana', 'NOT LIKE', 'Alokasi:%')->get();

foreach ($sumberDanas as $sd) {
    echo "Memproses: " . $sd->nama_sumber_dana . "\n";
    
    // 1. Jenis Project
    $jpName = ucfirst(strtolower($sd->tipe_project ?? 'Penelitian'));
    $jp = JenisProject::firstOrCreate(['nama' => $jpName]);
    
    // 2. Jenis Pendanaan
    $jdName = ucfirst(strtolower($sd->jenis_pendanaan ?? 'Internal'));
    $jd = JenisPendanaan::firstOrCreate(['nama' => $jdName]);
    
    // 3. Provider (simplification)
    $provName = ($jdName == 'Internal') ? 'LPPM STAS' : $sd->nama_sumber_dana;
    $provider = ProviderPendanaan::firstOrCreate(
        ['nama' => $provName],
        ['singkatan' => Str::limit($provName, 10, '')]
    );
    
    // 4. Generate Kode Skema
    $kode = strtoupper(implode('', array_map(function($w) { return $w[0] ?? ''; }, explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', $sd->nama_sumber_dana)))));
    if (empty($kode)) $kode = 'SKM-' . $sd->id;
    
    // 5. Create Skema Pendanaan
    $skema = SkemaPendanaan::firstOrCreate(
        ['nama' => $sd->nama_sumber_dana],
        [
            'kode' => $kode,
            'jenis_project_id' => $jp->id,
            'jenis_pendanaan_id' => $jd->id,
            'provider_id' => $provider->id,
            'is_active' => true,
        ]
    );
    
    // 6. Migrate Components
    $subkategoris = SubkategoriSumberdana::where('id_sumberdana', $sd->id)->orderBy('id')->get();
    $urutan = 1;
    
    foreach ($subkategoris as $sub) {
        // Find or create Komponen Biaya
        $komponen = KomponenBiaya::firstOrCreate(
            ['nama' => trim($sub->nama)],
            ['is_active' => true]
        );
        
        // Link to Skema
        SkemaKomponen::firstOrCreate(
            [
                'skema_pendanaan_id' => $skema->id,
                'komponen_biaya_id' => $komponen->id,
            ],
            [
                'is_wajib' => true, // default required
                'urutan' => $urutan++
            ]
        );
    }
}

echo "Migrasi Selesai!\n";
