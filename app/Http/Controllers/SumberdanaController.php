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
        $listSubkategori = SubkategoriSumberdana::select('nama')->orderBy('nama')->get();
        return view('sumberdana.create', compact('listSubkategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sumber_dana' => 'required|string|max:255|unique:sumber_dana,nama_sumber_dana',
            'jenis_pendanaan'  => 'required|string|max:255',
            'subkategori'      => 'nullable|array',
            'subkategori.*'    => 'nullable|string|max:255',
        ]);

        try {
            // Buat sumber dana baru
            $sumberdana = Sumberdana::create([
                'nama_sumber_dana' => $request->nama_sumber_dana,
                'jenis_pendanaan'  => $request->jenis_pendanaan,
                'user_id_created'  => Auth::user()->id,
                'user_id_updated'  => Auth::user()->id,
            ]);

            // Jika ada input subkategori, maka buat subkategori
            if ($request->has('subkategori') && is_array($request->subkategori)) {
                foreach ($request->subkategori as $subkategori) {
                    $subkategori = trim($subkategori);
                    if (!empty($subkategori)) {
                        $format_nama = Str::title($subkategori);
                        
                        // Periksa apakah subkategori dengan nama yang sama sudah ada
                        $exists = SubkategoriSumberdana::where('id_sumberdana', $sumberdana->id)
                            ->where('nama', 'LIKE', $format_nama)
                            ->exists();
                            
                        if (!$exists) {
                            SubkategoriSumberdana::create([
                                'id_sumberdana'   => $sumberdana->id,
                                'nama'            => $format_nama,
                                'nama_form'       => Str::lower(str_replace(' ', '_', $format_nama)),
                                'user_id_created' => Auth::user()->id,
                                'user_id_updated' => Auth::user()->id,
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('sumberdana.index')->with('success', 'Sumber Dana berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('sumberdana.index')->with('error', 'Gagal menambahkan Sumber Dana: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $sumberdana   = Sumberdana::findOrFail($id);
        $subkategori  = SubkategoriSumberdana::where('id_sumberdana', $id)->get();

        // ambil semua nama subkategori distinct buat opsi search
        $listSubkategori = SubkategoriSumberdana::select('nama')
            ->distinct()
            ->orderBy('nama')
            ->pluck('nama')
            ->toArray();


        return view('sumberdana.edit', compact('sumberdana', 'subkategori', 'listSubkategori'));
    }


public function update(Request $request, string $id)
{
    $request->validate([
        'nama_sumber_dana' => 'required|string|max:255',
        'jenis_pendanaan'  => 'required|string|in:internal,eksternal',
        'subkategori'      => 'nullable|array',
        'subkategori.*'    => 'nullable|string|max:255',
    ]);

    try {
        $sumberdana = Sumberdana::findOrFail($id);

        // 1) Update header
        $sumberdana->update([
            'nama_sumber_dana' => $request->nama_sumber_dana,
            'jenis_pendanaan'  => $request->jenis_pendanaan,
            'user_id_updated'  => Auth::id(),
        ]);

        // 2) Ambil subkategori dari form (yang terbaru)
        $inputSubs = collect($request->input('subkategori', []))
            ->map(fn($x) => Str::title(trim((string)$x)))
            ->filter(fn($x) => $x !== '')
            ->unique()
            ->values();

        // 3) Ambil subkategori yang sekarang di DB
        $dbSubs = SubkategoriSumberdana::where('id_sumberdana', $sumberdana->id)
            ->pluck('nama')
            ->map(fn($x) => Str::title(trim((string)$x)))
            ->unique()
            ->values();

        // 4) Yang harus DIHAPUS = ada di DB tapi tidak ada di input
        $toDelete = $dbSubs->diff($inputSubs);

        if ($toDelete->isNotEmpty()) {
            SubkategoriSumberdana::where('id_sumberdana', $sumberdana->id)
                ->whereIn('nama', $toDelete->all())
                ->delete();
        }

        // 5) Yang harus DITAMBAH = ada di input tapi belum ada di DB
        $toInsert = $inputSubs->diff($dbSubs);

        foreach ($toInsert as $name) {
            SubkategoriSumberdana::create([
                'id_sumberdana'   => $sumberdana->id,
                'nama'            => $name,
                'nama_form'       => Str::lower(str_replace(' ', '_', $name)),
                'user_id_created' => Auth::id(),
                'user_id_updated' => Auth::id(),
            ]);
        }

        // 6) Redirect biar kelihatan berubah (dan gak “kayak ga ngaruh”)
        return redirect()->route('sumberdana.index')->with('success', 'Perubahan disimpan.');
    } catch (\Exception $e) {
        return redirect()->route('sumberdana.edit', $id)->with('error', 'Gagal menyimpan: '.$e->getMessage());
    }
}


    public function destroy(string $id)
    {
        try {
            // Mulai transaksi database untuk memastikan semua proses delete berhasil atau tidak sama sekali
            \DB::beginTransaction();
            
            // Cari semua subkategori yang terkait dengan sumber dana ini
            $subkategoriIds = SubkategoriSumberdana::where('id_sumberdana', $id)->pluck('id')->toArray();
            
            if (!empty($subkategoriIds)) {
                // Hapus semua transaksi yang terkait dengan subkategori ini
                \DB::table('pencatatan_keuangan')->whereIn('sub_kategori_pendanaan', $subkategoriIds)->delete();
                
                // Hapus semua detail subkategori yang terkait dengan subkategori ini
                \DB::table('detail_subkategori')->whereIn('id_subkategori_sumberdana', $subkategoriIds)->delete();
            }
            
            // Hapus semua subkategori terkait
            SubkategoriSumberdana::where('id_sumberdana', $id)->delete();
            
            // Cek apakah ada project yang menggunakan sumber dana ini
            $projectExists = \DB::table('project')->where('id_sumber_dana', $id)->exists();
            
            if ($projectExists) {
                \DB::rollBack();
                return redirect()->route('sumberdana.index')->with('error', 'Tidak dapat menghapus Sumber Dana karena masih digunakan oleh Project. Hapus Project terkait terlebih dahulu.');
            }
            
            // Setelah itu baru hapus sumber dana
            Sumberdana::destroy($id);
            
            // Commit transaksi jika semua berhasil
            \DB::commit();
            
            return redirect()->route('sumberdana.index')->with('success', 'Sumber Dana berhasil dihapus.');
        } catch (\Exception $e) {
            // Rollback semua perubahan jika terjadi error
            \DB::rollBack();
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
            'nama' => [
                'required',
                'string',
                'max:255',
                // Memastikan nama subkategori unik dalam satu sumber dana yang sama
                function ($attribute, $value, $fail) use ($request) {
                    $exists = SubkategoriSumberdana::where('id_sumberdana', $request->id_sumberdana)
                        ->where('nama', 'LIKE', Str::title($value))
                        ->exists();
                    
                    if ($exists) {
                        $fail('Subkategori dengan nama ini sudah ada untuk sumber dana ini.');
                    }
                }
            ],
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

    public function updatesubkategori(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $id) {
                    $exists = SubkategoriSumberdana::where('id_sumberdana', $request->id_sumberdana)
                        ->where('nama', 'LIKE', Str::title($value))
                        ->where('id', '!=', $id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Subkategori dengan nama ini sudah ada untuk sumber dana ini.');
                    }
                }
            ],
        ]);

        try {
            $format_nama = Str::title($request->nama);
            
            $subkategori = SubkategoriSumberdana::findOrFail($id);
            $id_sumberdana = $subkategori->id_sumberdana;

            $subkategori->update([
                'nama'            => $format_nama,
                'nama_form'       => Str::lower(str_replace(' ', '_', $format_nama)),
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('sumberdana.edit', $id_sumberdana)
                ->with('success', 'Subkategori Sumber Dana berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui Subkategori Sumber Dana: ' . $e->getMessage());
        }
    }
}
