<?php
namespace App\Http\Controllers;

use App\Models\Detailproject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetailprojectController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required',
        ]);

        try {
            Detailproject::create([
                'id_project'      => $request->id_project,
                'id_user'         => $request->id_user,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('project.show', $request->id_project)->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('project.show', $request->id_project)->with('error', 'Data gagal ditambahkan');
        }
    }
}
