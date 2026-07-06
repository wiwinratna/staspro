<?php

namespace App\Http\Controllers;

use App\Models\KomponenBiaya;
use Illuminate\Http\Request;

class KomponenBiayaController extends Controller
{
    public function index()
    {
        $data = KomponenBiaya::orderBy('nama')->get();
        return view('master_data.komponen_biaya.index', compact('data'));
    }

    public function create()
    {
        return view('master_data.komponen_biaya.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        // Handle optional JSON metadata
        if ($request->has('metadata_json') && !empty($request->metadata_json)) {
            $validated['metadata'] = json_decode($request->metadata_json, true);
        }

        KomponenBiaya::create($validated);
        return redirect()->route('komponen-biaya.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = KomponenBiaya::findOrFail($id);
        return view('master_data.komponen_biaya.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = KomponenBiaya::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        if ($request->has('metadata_json') && !empty($request->metadata_json)) {
            $validated['metadata'] = json_decode($request->metadata_json, true);
        }

        $data->update($validated);
        return redirect()->route('komponen-biaya.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data = KomponenBiaya::findOrFail($id);
        try {
            $data->delete();
            return redirect()->route('komponen-biaya.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('komponen-biaya.index')->with('error', 'Gagal dihapus, data mungkin sedang digunakan.');
        }
    }
}
