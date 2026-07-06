<?php

namespace App\Http\Controllers;

use App\Models\JenisPendanaan;
use Illuminate\Http\Request;

class JenisPendanaanController extends Controller
{
    public function index()
    {
        $data = JenisPendanaan::orderBy('nama')->get();
        return view('master_data.jenis_pendanaan.index', compact('data'));
    }

    public function create()
    {
        return view('master_data.jenis_pendanaan.create');
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
        JenisPendanaan::create($validated);
        return redirect()->route('jenis-pendanaan.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = JenisPendanaan::findOrFail($id);
        return view('master_data.jenis_pendanaan.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = JenisPendanaan::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $data->update($validated);
        return redirect()->route('jenis-pendanaan.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data = JenisPendanaan::findOrFail($id);
        try {
            $data->delete();
            return redirect()->route('jenis-pendanaan.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('jenis-pendanaan.index')->with('error', 'Gagal dihapus, data mungkin sedang digunakan.');
        }
    }
}
