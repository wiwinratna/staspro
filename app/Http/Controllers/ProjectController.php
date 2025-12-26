<?php
namespace App\Http\Controllers;

use App\Models\DetailSubkategori;
use App\Models\Project;
use App\Models\SubkategoriSumberdana;
use App\Models\Sumberdana;
use App\Models\User;
use App\Models\RequestpembelianHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return view('project', ['projects' => $projects]);
    }

    public function create()
    {
        $sumber_internal  = Sumberdana::where('jenis_pendanaan', 'internal')->get();
        $sumber_eksternal = Sumberdana::where('jenis_pendanaan', 'eksternal')->get();
        return view('input_project', ['sumber_internal' => $sumber_internal, 'sumber_eksternal' => $sumber_eksternal]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'         => 'required',
            'nama_project'  => 'required|unique:project,nama_project',
            'durasi'        => 'required',
            'deskripsi'     => 'required',
            'file_proposal' => 'required|mimes:pdf|max:5120',
            'file_rab'      => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $file_proposal     = $request->file('file_proposal');
            $filename_proposal = time() . '.' . $file_proposal->getClientOriginalExtension();
            $file_proposal->move('file_proposal', $filename_proposal);

            $file_rab     = $request->file('file_rab');
            $filename_rab = time() . '.' . $file_rab->getClientOriginalExtension();
            $file_rab->move('file_rab', $filename_rab);

            $subkategori_sumberdana = SubkategoriSumberdana::where('id_sumberdana', $request->sumber_dana == 'internal' ? $request->kategori_pendanaan_internal : $request->kategori_pendanaan_eksternal)->get();

            $project = Project::create([
                'tahun'           => $request->tahun,
                'nama_project'    => $request->nama_project,
                'id_sumber_dana'  => $request->sumber_dana == 'internal' ? $request->kategori_pendanaan_internal : $request->kategori_pendanaan_eksternal,
                'durasi'          => $request->durasi,
                'deskripsi'       => $request->deskripsi,
                'file_proposal'   => $filename_proposal,
                'file_rab'        => $filename_rab,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            foreach ($subkategori_sumberdana as $subkategori) {
                $nama_form = $subkategori->nama_form;
                if ($request->has($nama_form)) {
                    $nominal = str_replace(['Rp.', '.', ','], ['', '', '.'], $request->$nama_form);
                    $nominal = (float) $nominal;
            
                    DetailSubkategori::create([
                        'nominal'                   => $nominal,
                        'id_subkategori_sumberdana' => $subkategori->id,
                        'id_project'                => $project->id,
                        'user_id_created'           => Auth::user()->id,
                        'user_id_updated'           => Auth::user()->id,
                    ]);
                }
            }            

            return redirect()->route('project.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('project.index')->with('error', $e->getMessage());
        }
    }

    public function show(Project $project) {
        $project = Project::with('sumberDana')->findOrFail($project->id);
        if (!$project->sumberDana) {
            $sumber_dana = DB::table('sumber_dana')
                ->where('id', $project->id_sumber_dana)
                ->first();
        }
        
        $anggota = DB::table('detail_project as a')
            ->leftJoin('users as b', 'a.id_user', '=', 'b.id')
            ->where('a.id_project', $project->id)
            ->select('b.name')
            ->get();
        
        $users = DB::table('users as b')
            ->leftJoin('detail_project as a', function ($join) use ($project) {
                $join->on('a.id_user', '=', 'b.id')
                    ->where('a.id_project', '=', $project->id);
            })
            ->whereNull('a.id_user')
            ->where('b.id', '!=', Auth::id())
            ->where('b.role', '!=', 'admin')
            ->select('b.name', 'b.id')
            ->get();
        
        $total_request_pembelian = DB::table('request_pembelian_detail as a')
            ->leftJoin('request_pembelian_header as b', 'a.id_request_pembelian_header', '=', 'b.id')
            ->where('b.id_project', $project->id)
            ->where('b.status_request', 'done')
            ->sum(DB::raw('a.kuantitas * a.harga'));
        
        $detail_request = DB::table('request_pembelian_detail as a')
            ->leftJoin('request_pembelian_header as b', 'a.id_request_pembelian_header', '=', 'b.id')
            ->where('b.id_project', $project->id)
            ->where('b.status_request', 'done')
            ->select(
                'a.nama_barang',
                'a.kuantitas',
                'a.harga',
                DB::raw('a.kuantitas * a.harga as total')
            )
            ->get();
        
        $detail_dana = DB::table('detail_subkategori as a')
        ->leftJoin('subkategori_sumberdana as b', 'a.id_subkategori_sumberdana', '=', 'b.id')
        ->leftJoin('sumber_dana as c', 'b.id_sumberdana', '=', 'c.id')
        ->where('a.id_project', $project->id)
        ->select(
            'b.nama as nama_subkategori',
            'a.nominal',
            'a.realisasi_anggaran',
            'a.id',
            'c.jenis_pendanaan',
            'c.nama_sumber_dana'
        )
        ->get();
        
        $jenis_pendanaan = $project->sumberDana ? $project->sumberDana->jenis_pendanaan : 
                        (isset($sumber_dana) ? $sumber_dana->jenis_pendanaan : 'internal');
        $nama_sumber_dana = $project->sumberDana ? $project->sumberDana->nama_sumber_dana : 
                        (isset($sumber_dana) ? $sumber_dana->nama_sumber_dana : null);
        
        $detail_dana = $detail_dana->map(function($item) use ($total_request_pembelian, $jenis_pendanaan, $nama_sumber_dana) {
            $realisasi = $item->realisasi_anggaran;

            if ($jenis_pendanaan == 'internal' && $item->nama_subkategori == 'Bahan Habis Pakai dan Peralatan') {
                $realisasi += $total_request_pembelian;
            }
            else if ($jenis_pendanaan == 'eksternal' && 
                    strtolower($nama_sumber_dana) == strtolower('Kedaireka') && 
                    strtolower($item->nama_subkategori) == strtolower('Peralatan Pendukung Terkait Langsung dengan Kegiatan')) {
                $realisasi += $total_request_pembelian;
            }
            else if ($jenis_pendanaan == 'eksternal' && 
                    strtolower($nama_sumber_dana) == strtolower('DRPTM') && 
                    strtolower($item->nama_subkategori) == strtolower('Bahan')) {
                $realisasi += $total_request_pembelian;
            }
            else if ($jenis_pendanaan == 'eksternal' && 
                    strtolower($nama_sumber_dana) == strtolower('LPDP') && 
                    strtolower($item->nama_subkategori) == strtolower('Biaya Langsung')) {
                $realisasi += $total_request_pembelian;
            }
            
            $item->realisasi_anggaran = $realisasi;
            return $item;
        });
        
        $total_nominal = $detail_dana->sum('nominal');
        $total_realisasi = $detail_dana->sum('realisasi_anggaran');
        
        return view('detail_project', [
            'project' => $project,
            'anggota' => $anggota,
            'users' => $users,
            'detail_dana' => $detail_dana,
            'detail_request' => $detail_request,
            'total_request_pembelian' => $total_request_pembelian,
            'total_nominal' => $total_nominal,
            'total_realisasi' => $total_realisasi,
            'sumber_dana' => $project->sumberDana ?? ($sumber_dana ?? null)
        ]);
    }

    public function download_proposal($id)
    {
        $project = Project::find($id);
        $filename = $project->nama_project . '_proposal.pdf'; // Menggunakan nama project untuk nama file

        return response()->download(public_path('file_proposal/' . $project->file_proposal), $filename);
    }

    public function download_rab($id)
    {
        $project = Project::find($id);
        $filename = $project->nama_project . '_rab.xlsx'; // Menggunakan nama project untuk nama file

        return response()->download(public_path('file_rab/' . $project->file_rab), $filename);
    }

    public function getSubkategori($id)
    {
        $subkategori = SubkategoriSumberdana::where('id_sumberdana', $id)->get();

        return response()->json($subkategori);
    }

    public function getProjectSubcategories($id)
    {
        $subcategories = DB::table('detail_subkategori as ds')
            ->join('subkategori_sumberdana as ss', 'ds.id_subkategori_sumberdana', '=', 'ss.id')
            ->where('ds.id_project', $id)
            ->select('ds.id', 'ds.nominal', 'ds.id_subkategori_sumberdana', 'ss.nama', 'ss.nama_form')
            ->get();
        
        return response()->json($subcategories);
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $sumber_internal = SumberDana::where('jenis_pendanaan', 'internal')->get();
        $sumber_eksternal = SumberDana::where('jenis_pendanaan', 'eksternal')->get();

        return view('input_project', compact('project', 'sumber_internal', 'sumber_eksternal'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'nama_project' => 'required|unique:project,nama_project,'.$id,
            'tahun' => 'required',
            'durasi' => 'required',
            'deskripsi' => 'required',
        ]);
        
        if ($request->hasFile('file_proposal')) {
            $file_proposal = $request->file('file_proposal');
            $filename_proposal = time() . '.' . $file_proposal->getClientOriginalExtension();
            $file_proposal->move('file_proposal', $filename_proposal);
            $project->file_proposal = $filename_proposal;
        }
        
        if ($request->hasFile('file_rab')) {
            $file_rab = $request->file('file_rab');
            $filename_rab = time() . '.' . $file_rab->getClientOriginalExtension();
            $file_rab->move('file_rab', $filename_rab);
            $project->file_rab = $filename_rab;
        }
        
        $new_sumber_dana_id = $request->sumber_dana == 'internal' 
            ? $request->kategori_pendanaan_internal 
            : $request->kategori_pendanaan_eksternal;
        
        $old_sumber_dana_id = $project->id_sumber_dana;

        $project->nama_project = $request->nama_project;
        $project->tahun = $request->tahun;
        $project->durasi = $request->durasi;
        $project->deskripsi = $request->deskripsi;
        $project->id_sumber_dana = $new_sumber_dana_id;
        $project->user_id_updated = Auth::user()->id;
        $project->save();
        
        $subkategori_sumberdana = SubkategoriSumberdana::where('id_sumberdana', $new_sumber_dana_id)->get();
        
        if ($old_sumber_dana_id != $new_sumber_dana_id) {
            DetailSubkategori::where('id_project', $project->id)->delete();
        }
        
        foreach ($subkategori_sumberdana as $subkategori) {
            $nama_form = $subkategori->nama_form;
            if ($request->has($nama_form) && !empty($request->$nama_form)) {
                $nominal_raw = $request->$nama_form;
                $nominal = str_replace(['Rp.', '.', ',', ' '], ['', '', '.', ''], $nominal_raw);
                $nominal = (float) $nominal;
                
                if ($nominal <= 0) {
                    continue;
                }
                
                $detail = DetailSubkategori::where('id_project', $project->id)
                    ->where('id_subkategori_sumberdana', $subkategori->id)
                    ->first();
                
                if ($detail) {
                    $detail->nominal = $nominal;
                    $detail->user_id_updated = Auth::user()->id;
                    $detail->save();
                } else {
                    DetailSubkategori::create([
                        'nominal' => $nominal,
                        'id_subkategori_sumberdana' => $subkategori->id,
                        'id_project' => $project->id,
                        'user_id_created' => Auth::user()->id,
                        'user_id_updated' => Auth::user()->id,
                        'realisasi_anggaran' => 0
                    ]);
                }
            }
        }
        
        $provided_subcategory_ids = [];
        foreach ($subkategori_sumberdana as $subkategori) {
            $nama_form = $subkategori->nama_form;
            if ($request->has($nama_form) && !empty($request->$nama_form)) {
                $nominal_raw = $request->$nama_form;
                $nominal = str_replace(['Rp.', '.', ',', ' '], ['', '', '.', ''], $nominal_raw);
                $nominal = (float) $nominal;
                if ($nominal > 0) {
                    $provided_subcategory_ids[] = $subkategori->id;
                }
            }
        }
        
        if (!empty($provided_subcategory_ids)) {
            DetailSubkategori::where('id_project', $project->id)
                ->whereNotIn('id_subkategori_sumberdana', $provided_subcategory_ids)
                ->delete();
        }
        
        return redirect()->route('project.index')->with('success', 'Project berhasil diupdate!');
    }

    public function destroy($id)
    {
        // Hapus data di tabel request_pembelian_detail yang terkait dengan request_pembelian_header
        $requestHeaders = RequestpembelianHeader::where('id_project', $id)->get();

        foreach ($requestHeaders as $header) {
            // Hapus data di tabel request_pembelian_detail yang terkait dengan request_pembelian_header
            DB::table('request_pembelian_detail')->where('id_request_pembelian_header', $header->id)->delete();
        }

        // Hapus data di tabel request_pembelian_header
        RequestpembelianHeader::where('id_project', $id)->delete();

        // Hapus detail_subkategori yang terkait dengan project
        DB::table('detail_subkategori')->where('id_project', $id)->delete();

        // Hapus project
        $project = Project::findOrFail($id);
        $project->delete();

        // Redirect ke halaman project tanpa pesan sukses
        return redirect()->route('project.index');
    }

public function close(Request $request, $id)
{
    $project = Project::findOrFail($id);

    // kalau sudah ditutup, jangan dobel insert
    if (strtolower($project->status ?? '') === 'ditutup') {
        return response()->json([
            'success' => false,
            'message' => 'Project sudah ditutup.'
        ], 400);
    }

    return DB::transaction(function () use ($project, $id) {

        // ===== HITUNG DARI DETAIL DANA (ANGGARAN vs REALISASI) =====
        // Sesuaikan nama tabel/kolom kalau beda
        $totalAnggaran = DB::table('detail_subkategori')   // <-- kalau tabelmu beda, ganti di sini
            ->where('id_project', $id)
            ->sum('nominal');

        $totalRealisasi = DB::table('detail_subkategori')  // <-- kalau tabelmu beda, ganti di sini
            ->where('id_project', $id)
            ->sum('realisasi_anggaran');

        $sisa = (int) $totalAnggaran - (int) $totalRealisasi;

        // ===== MASUKKAN KE KAS kalau sisa > 0 =====
        $kasMasuk = 0;

        if ($sisa > 0) {
            // cegah dobel insert untuk project yang sama
            $already = DB::table('kas_transactions')
                ->where('project_id', $id)
                ->where('tipe', 'masuk')
                ->where('kategori', 'Sisa Project')
                ->exists();

            if (!$already) {
                DB::table('kas_transactions')->insert([
                    'tanggal'           => Carbon::now()->toDateString(),
                    'tipe'              => 'masuk',
                    'kategori'          => 'Sisa Project',
                    'project_id'        => $id,
                    'nominal'           => $sisa,
                    'deskripsi'         => 'Penutupan project: ' . ($project->nama_project ?? ('#' . $id)),
                    'metode_pembayaran' => '-',
                    'bukti'             => null,
                    'created_by'        => auth()->id(),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }

            $kasMasuk = $sisa;
        }

        // ===== UPDATE STATUS PROJECT =====
        $project->status = 'ditutup';
        $project->save();

        // ===== RESPONSE buat popup (sesuai yang kamu mau) =====
        return response()->json([
            'success' => true,
            'data' => [
                'total_masuk' => (int) $totalAnggaran,     // tampil sebagai "Total Masuk"
                'total_keluar'=> (int) $totalRealisasi,    // tampil sebagai "Total Keluar"
                'sisa'        => (int) $sisa,              // ini akan jadi 6.400.000
                'kas_masuk'   => (int) $kasMasuk
            ]
        ]);
    });
}
}
