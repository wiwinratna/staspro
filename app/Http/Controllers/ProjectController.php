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

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all();
        return view('project', ['projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sumber_internal  = Sumberdana::where('jenis_pendanaan', 'internal')->get();
        $sumber_eksternal = Sumberdana::where('jenis_pendanaan', 'eksternal')->get();
        return view('input_project', ['sumber_internal' => $sumber_internal, 'sumber_eksternal' => $sumber_eksternal]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'         => 'required',
            'nama_project'  => 'required',
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
                    $nominal = (float) $nominal;  // Mengubah nominal ke tipe data numerik
            
                    DetailSubkategori::create([
                        'nominal'                   => $nominal,  // Menggunakan nominal yang sudah diproses
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

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        // Pastikan project selalu fresh dari DB
        $project = Project::findOrFail($project->id);

        // Ambil anggota project
        $anggota = DB::table('detail_project as a')
            ->leftJoin('users as b', 'a.id_user', '=', 'b.id')
            ->where('a.id_project', $project->id)
            ->select('b.name')
            ->get();

        // Ambil user yang belum jadi anggota project
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

        // Ambil dana & realisasi anggaran
        $detail_dana = DB::table('detail_subkategori as a')
            ->leftJoin('subkategori_sumberdana as b', 'a.id_subkategori_sumberdana', '=', 'b.id')
            ->where('a.id_project', $project->id)
            ->select(
                'b.nama as nama_subkategori', // Nama subkategori
                'a.nominal',                  // Nominal dana yang diajukan
                'a.realisasi_anggaran'        // Realisasi anggaran
            )
            ->get();

        // Ambil detail request pembelian
        $detail_request = DB::table('request_pembelian_detail as a')
            ->leftJoin('request_pembelian_header as b', 'a.id_request_pembelian_header', '=', 'b.id')
            ->where('b.id_project', $project->id)
            ->select(
                'a.nama_barang',
                'a.kuantitas',
                'a.harga',
                DB::raw('a.kuantitas * a.harga as total')
            )
            ->get();

        // Return data ke view
        return view('detail_project', [
            'project' => $project,
            'anggota' => $anggota,
            'users' => $users,
            'detail_dana' => $detail_dana,
            'detail_request' => $detail_request
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
        
        $project->update([
                'nama_project' => $request->nama_project,
                'tahun' => $request->tahun,
                'durasi' => $request->durasi,
                'deskripsi' => $request->deskripsi,
                'id_sumber_dana' => $request->sumber_dana == 'internal'
                    ? $request->kategori_pendanaan_internal
                    : $request->kategori_pendanaan_eksternal,
            'bahan_habis_pakai_dan_peralatan' => $request->bahan_habis_pakai_dan_peralatan,
            'biaya_transportasi_dan_perjalanan' => $request->biaya_transportasi_dan_perjalanan,
            'biaya_lainnya' => $request->biaya_lainnya,
        ]);

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
}
