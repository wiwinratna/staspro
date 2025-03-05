<?php
namespace App\Http\Controllers;

use App\Models\Sumberdana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SumberdanaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sumberdana.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sumber_dana'  => 'required',
            'jenis_pendanaan'   => 'required',
            'keterangan'        => 'required',
            'anggaran_maksimal' => 'required',
            'tgl_berlaku'       => 'required',
        ]);

        try {
            Sumberdana::create([
                'nama_sumber_dana'  => $request->nama_sumber_dana,
                'jenis_pendanaan'   => $request->jenis_pendanaan,
                'keterangan'        => $request->keterangan,
                'anggaran_maksimal' => $request->anggaran_maksimal,
                'tgl_berlaku'       => $request->tgl_berlaku,
                'user_id_created'   => Auth::user()->id,
                'user_id_updated'   => Auth::user()->id,
            ]);

            return redirect()->route('project.create')->with('success', 'Sumber Dana berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->route('project.create')->with('error', 'Sumber Dana gagal dibuat');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sumberdana = Sumberdana::find($id);

        return response()->json($sumberdana);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
