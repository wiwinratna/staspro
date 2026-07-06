<?php

namespace App\Http\Controllers;

use App\Models\SkemaPendanaan;
use App\Models\SkemaKomponen;
use App\Models\KomponenBiaya;
use App\Models\JenisProject;
use App\Models\JenisPendanaan;
use App\Models\ProviderPendanaan;
use Illuminate\Http\Request;

class SkemaPendanaanController extends Controller
{
    public function index()
    {
        $data = SkemaPendanaan::with(['jenisProject', 'jenisPendanaan', 'provider', 'komponen.komponenBiaya'])->get();
        return view('master_data.skema_pendanaan.index', compact('data'));
    }

    public function create()
    {
        $jenisProjects = JenisProject::where('is_active', true)->get();
        $jenisPendanaans = JenisPendanaan::where('is_active', true)->get();
        $providers = ProviderPendanaan::where('is_active', true)->get();

        return view('master_data.skema_pendanaan.create', compact('jenisProjects', 'jenisPendanaans', 'providers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:skema_pendanaan,kode',
            'nama' => 'required|string|max:255',
            'jenis_project_id' => 'required|exists:jenis_project,id',
            'jenis_pendanaan_id' => 'required|exists:jenis_pendanaan,id',
            'provider_id' => 'required|exists:provider_pendanaan,id',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $skema = SkemaPendanaan::create($validated);

        return redirect()->route('skema-pendanaan.edit', $skema->id)
            ->with('success', 'Skema Pendanaan berhasil dibuat. Silakan atur Komponen Biaya.');
    }

    public function edit($id)
    {
        $data = SkemaPendanaan::with(['komponen.komponenBiaya'])->findOrFail($id);
        $jenisProjects = JenisProject::where('is_active', true)->get();
        $jenisPendanaans = JenisPendanaan::where('is_active', true)->get();
        $providers = ProviderPendanaan::where('is_active', true)->get();

        // Ambil ID komponen yang sudah ada
        $existingKomponenIds = $data->komponen->pluck('komponen_biaya_id')->toArray();
        // Option komponen yang belum ditambahkan
        $availableKomponen = KomponenBiaya::where('is_active', true)
            ->whereNotIn('id', $existingKomponenIds)
            ->orderBy('nama')
            ->get();

        return view('master_data.skema_pendanaan.edit', compact('data', 'jenisProjects', 'jenisPendanaans', 'providers', 'availableKomponen'));
    }

    public function update(Request $request, $id)
    {
        $skema = SkemaPendanaan::findOrFail($id);
        
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:skema_pendanaan,kode,' . $id,
            'nama' => 'required|string|max:255',
            'jenis_project_id' => 'required|exists:jenis_project,id',
            'jenis_pendanaan_id' => 'required|exists:jenis_pendanaan,id',
            'provider_id' => 'required|exists:provider_pendanaan,id',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $skema->update($validated);

        return redirect()->route('skema-pendanaan.index')->with('success', 'Informasi Skema berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $skema = SkemaPendanaan::findOrFail($id);
        try {
            // Hapus relasi komponen secara manual jika tidak ada cascade onDelete di DB
            $skema->komponen()->delete();
            $skema->delete();
            return redirect()->route('skema-pendanaan.index')->with('success', 'Skema berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('skema-pendanaan.index')->with('error', 'Gagal dihapus, skema mungkin sedang digunakan oleh project.');
        }
    }

    // --- AJAX endpoints untuk Komponen Biaya ---

    public function storeKomponen(Request $request, $id)
    {
        $request->validate([
            'komponen_biaya_id' => 'required|exists:komponen_biaya,id',
            'is_wajib' => 'boolean'
        ]);

        $skema = SkemaPendanaan::findOrFail($id);

        // Validasi duplikat
        if ($skema->komponen()->where('komponen_biaya_id', $request->komponen_biaya_id)->exists()) {
            return response()->json(['error' => 'Komponen sudah ada di skema ini.'], 400);
        }

        // Tentukan urutan terakhir
        $lastUrutan = $skema->komponen()->max('urutan') ?? 0;

        $newKomponen = SkemaKomponen::create([
            'skema_pendanaan_id' => $skema->id,
            'komponen_biaya_id' => $request->komponen_biaya_id,
            'is_wajib' => $request->has('is_wajib') ? $request->is_wajib : true,
            'urutan' => $lastUrutan + 1
        ]);

        return response()->json(['success' => true, 'message' => 'Komponen berhasil ditambahkan']);
    }

    public function destroyKomponen($skema_id, $komponen_id)
    {
        $skemaKomponen = SkemaKomponen::where('skema_pendanaan_id', $skema_id)
            ->where('id', $komponen_id)
            ->firstOrFail();

        // Validasi is_wajib tidak bisa dihapus jika aturan bisnis melarang?
        // Tapi admin di sini mengkonfigurasi master. Jadi kita bolehkan.
        
        $skemaKomponen->delete();
        
        // Re-urutkan sisanya agar tidak ada gap
        $this->reorderSequentially($skema_id);

        return response()->json(['success' => true]);
    }

    public function reorderKomponen(Request $request, $skema_id)
    {
        $order = $request->input('order'); // array of skema_komponen id in new order
        if (is_array($order)) {
            foreach ($order as $index => $id) {
                SkemaKomponen::where('skema_pendanaan_id', $skema_id)
                    ->where('id', $id)
                    ->update(['urutan' => $index + 1]);
            }
        }
        return response()->json(['success' => true]);
    }

    public function toggleWajib(Request $request, $skema_id, $komponen_id)
    {
        $skemaKomponen = SkemaKomponen::where('skema_pendanaan_id', $skema_id)
            ->where('id', $komponen_id)
            ->firstOrFail();

        $skemaKomponen->update([
            'is_wajib' => filter_var($request->is_wajib, FILTER_VALIDATE_BOOLEAN)
        ]);

        return response()->json(['success' => true]);
    }

    private function reorderSequentially($skema_id)
    {
        $komponen = SkemaKomponen::where('skema_pendanaan_id', $skema_id)
            ->orderBy('urutan')
            ->get();
            
        $urutan = 1;
        foreach ($komponen as $k) {
            $k->update(['urutan' => $urutan++]);
        }
    }
}
