<?php
namespace App\Http\Controllers;

use App\Models\SubkategoriSumberdana;
use App\Models\Sumberdana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SumberdanaController extends Controller
{
    public function index()
    {
        $sumberdana = Sumberdana::all();
        return view('sumberdana.index', ['sumberdana' => $sumberdana]);
    }

    public function create()
    {
        return view('sumberdana.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sumber_dana' => 'required|string|max:255',
            'jenis_pendanaan'  => 'required|string|max:255',
        ]);

        try {
            Sumberdana::create([
                'nama_sumber_dana' => $request->nama_sumber_dana,
                'jenis_pendanaan'  => $request->jenis_pendanaan,
                'user_id_created'  => Auth::user()->id,
                'user_id_updated'  => Auth::user()->id,
            ]);

            return redirect()->route('sumberdana.index')->with('success', 'Sumber Dana berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('sumberdana.index')->with('error', 'Gagal menambahkan Sumber Dana: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $sumberdana = Sumberdana::findOrFail($id);
        return view('sumberdana.edit', ['sumberdana' => $sumberdana]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama_sumber_dana' => 'required|string|max:255',
            'jenis_pendanaan'  => 'required|string|max:255',
        ]);

        try {
            Sumberdana::where('id', $id)->update([
                'nama_sumber_dana' => $request->nama_sumber_dana,
                'jenis_pendanaan'  => $request->jenis_pendanaan,
                'user_id_updated'  => Auth::user()->id,
            ]);

            return redirect()->route('sumberdana.index')->with('success', 'Sumber Dana berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('sumberdana.index')->with('error', 'Gagal memperbarui Sumber Dana: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            Sumberdana::destroy($id);
            return redirect()->route('sumberdana.index')->with('success', 'Sumber Dana berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('sumberdana.index')->with('error', 'Gagal menghapus Sumber Dana: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $sumberdana_header = Sumberdana::findOrFail($id);
        $detail            = SubkategoriSumberdana::where('id_sumberdana', $id)->get();

        return view('sumberdana.detail', [
            'sumberdana_header' => $sumberdana_header,
            'detail'            => $detail,
        ]);
    }

    public function storedetail(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        try {
            $format_nama = Str::title($request->nama);

            SubkategoriSumberdana::create([
                'id_sumberdana'   => $request->id_sumberdana,
                'nama'            => $format_nama,
                'nama_form'       => Str::lower(str_replace(' ', '_', $format_nama)),
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('sumberdana.detail', $request->id_sumberdana)->with('success', 'Subkategori Sumber Dana berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('sumberdana.detail', $request->id_sumberdana)->with('error', 'Gagal menambahkan Subkategori Sumber Dana: ' . $e->getMessage());
        }
    }

    public function destroydetail($id)
    {
        try {
            SubkategoriSumberdana::destroy($id);
            return redirect()->back()->with('success', 'Subkategori Sumber Dana berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Subkategori Sumber Dana: ' . $e->getMessage());
        }
    }
}
