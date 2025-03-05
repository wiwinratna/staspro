<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Rab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RabController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rab = DB::table('rab as a')
            ->leftJoin('project as b', 'a.id_project', '=', 'b.id')
            ->select('a.id', 'a.judul_rab', 'a.tgl_pengajuan', 'b.nama_project', 'a.anggaran_diajukan', 'a.file_rab', 'a.status')
            ->get();

        return view('rab.index', ['rab' => $rab]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $project = Project::all();

        return view('rab.create', ['project' => $project]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_rab'         => 'required',
            'tgl_pengajuan'     => 'required',
            'id_project'        => 'required',
            'file_rab'          => 'required|mimes:xlsx,xls,csv|max:5120',
            'nama_pengaju'      => 'required',
            'anggaran_diajukan' => 'required',
        ]);

        try {
            $file     = $request->file('file_rab');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move('file_rab', $filename);

            Rab::create([
                'judul_rab'         => $request->judul_rab,
                'tgl_pengajuan'     => $request->tgl_pengajuan,
                'file_rab'          => $filename,
                'nama_pengaju'      => $request->nama_pengaju,
                'anggaran_diajukan' => $request->anggaran_diajukan,
                'status'            => 'dalam_tinjauan',
                'id_project'        => $request->id_project,
                'user_id_created'   => Auth::user()->id,
                'user_id_updated'   => Auth::user()->id,
            ]);

            return redirect()->route('rab.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('rab.index')->with('error', 'Data gagal disimpan');
        }
    }

    public function download($id)
    {
        $rab = Rab::find($id);

        return response()->download(public_path('file_rab/' . $rab->file_rab));
    }

    public function approve($id)
    {
        Rab::where('id', $id)->update(['status' => 'disetujui']);

        return redirect()->route('rab.index')->with('success', 'Data berhasil disetujui');
    }

    public function reject($id)
    {
        Rab::where('id', $id)->update(['status' => 'ditolak']);

        return redirect()->route('rab.index')->with('success', 'Data berhasil ditolak');
    }
}
