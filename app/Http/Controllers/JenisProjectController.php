<?php

namespace App\Http\Controllers;

use App\Models\JenisProject;
use Illuminate\Http\Request;

class JenisProjectController extends Controller
{
    public function index()
    {
        $data = JenisProject::orderBy('nama')->get();
        return view('master_data.jenis_project.index', compact('data'));
    }

    public function create()
    {
        return view('master_data.jenis_project.create');
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
        JenisProject::create($validated);
        return redirect()->route('jenis-project.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = JenisProject::findOrFail($id);
        return view('master_data.jenis_project.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = JenisProject::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $data->update($validated);
        return redirect()->route('jenis-project.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data = JenisProject::findOrFail($id);
        try {
            $data->delete();
            return redirect()->route('jenis-project.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('jenis-project.index')->with('error', 'Gagal dihapus, data mungkin sedang digunakan.');
        }
    }
}
