<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sumberdana;
use App\Models\User;
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
            'jumlah_dana'        => 'required',
            'tahun'              => 'required',
            'nama_project'       => 'required',
            'kategori_pendanaan' => 'required',
            'durasi'             => 'required',
            'deskripsi'          => 'required',
            'file_proposal'      => 'required|mimes:pdf|max:5120',
            'file_rab'           => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $file_proposal = $request->file('file_proposal');
            $filename_proposal = time() . '.' . $file_proposal->getClientOriginalExtension();
            $file_proposal->move('file_proposal', $filename_proposal);

            $file_rab     = $request->file('file_rab');
            $filename_rab = time() . '.' . $file_rab->getClientOriginalExtension();
            $file_rab->move('file_rab', $filename_rab);

            Project::create([
                'jumlah_dana'     => $request->jumlah_dana,
                'tahun'           => $request->tahun,
                'nama_project'    => $request->nama_project,
                'id_sumber_dana'  => $request->kategori_pendanaan,
                'durasi'          => $request->durasi,
                'deskripsi'       => $request->deskripsi,
                'file_proposal'   => $filename_proposal,
                'file_rab'        => $filename_rab,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

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
        $project = Project::find($project->id);
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
            ->where('b.id', '!=', Auth::user()->id)
            ->where('b.role', '!=', 'admin')
            ->select('b.name', 'b.id')
            ->get();

        return view('detail_project', ['project' => $project, 'anggota' => $anggota, 'users' => $users]);
    }

    public function download_proposal($id)
    {
        $project = Project::find($id);

        return response()->download(public_path('file_proposal/' . $project->file_proposal));
    }

    public function download_rab($id)
    {
        $project = Project::find($id);

        return response()->download(public_path('file_rab/' . $project->file_rab));
    }
}
