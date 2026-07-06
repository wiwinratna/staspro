<?php

namespace App\Http\Controllers;

use App\Models\ProviderPendanaan;
use Illuminate\Http\Request;

class ProviderPendanaanController extends Controller
{
    public function index()
    {
        $data = ProviderPendanaan::orderBy('nama')->get();
        return view('master_data.provider_pendanaan.index', compact('data'));
    }

    public function create()
    {
        return view('master_data.provider_pendanaan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'singkatan' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        ProviderPendanaan::create($validated);
        return redirect()->route('provider-pendanaan.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = ProviderPendanaan::findOrFail($id);
        return view('master_data.provider_pendanaan.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = ProviderPendanaan::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'singkatan' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $data->update($validated);
        return redirect()->route('provider-pendanaan.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data = ProviderPendanaan::findOrFail($id);
        try {
            $data->delete();
            return redirect()->route('provider-pendanaan.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('provider-pendanaan.index')->with('error', 'Gagal dihapus, data mungkin sedang digunakan.');
        }
    }
}
