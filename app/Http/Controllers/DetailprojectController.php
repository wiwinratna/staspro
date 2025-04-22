<?php

namespace App\Http\Controllers;

use App\Models\Detailproject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class DetailprojectController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input yang lebih ketat
        $validated = $request->validate([
            'id_project' => 'required|exists:project,id',
            'id_user'    => 'required|exists:users,id',
        ]);

        try {
            Detailproject::create([
                'id_project'      => $validated['id_project'],
                'id_user'         => $validated['id_user'],
                'user_id_created' => Auth::id(),
                'user_id_updated' => Auth::id(),
            ]);

            return redirect()
                ->route('project.show', $validated['id_project'])
                ->with('success', 'Data berhasil ditambahkan.');
        } catch (QueryException $e) {
            return redirect()
                ->route('project.show', $validated['id_project'])
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }
}
