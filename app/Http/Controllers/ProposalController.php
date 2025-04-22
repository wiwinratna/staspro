<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Proposal;
use App\Models\Sumberdana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProposalController extends Controller
{
    public function index()
    {
        $proposal = DB::table('proposal as a')
            ->leftJoin('project as b', 'a.id_project', '=', 'b.id')
            ->leftJoin('sumber_dana as c', 'a.id_sumber_dana', '=', 'c.id')
            ->select('a.id', 'a.judul_proposal', 'a.tgl_pengajuan', 'b.nama_project', 'a.anggaran_diajukan', 'c.nama_sumber_dana', 'a.file_proposal', 'a.status')
            ->get();

        return view('proposal.index', ['proposal' => $proposal]);
    }

    public function create()
    {
        $project     = Project::all();
        $sumber_dana = Sumberdana::all();

        return view('proposal.create', ['project' => $project, 'sumber_dana' => $sumber_dana]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_proposal'    => 'required',
            'tgl_pengajuan'     => 'required',
            'id_project'        => 'required',
            'file_proposal'     => 'required|mimes:pdf|max:5120',
            'nama_pengaju'      => 'required',
            'anggaran_diajukan' => 'required',
            'id_sumber_dana'    => 'required',
        ]);

        try {
            $file     = $request->file('file_proposal');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move('file_proposal', $filename);

            Proposal::create([
                'judul_proposal'    => $request->judul_proposal,
                'tgl_pengajuan'     => $request->tgl_pengajuan,
                'file_proposal'     => $filename,
                'id_project'        => $request->id_project,
                'nama_pengaju'      => $request->nama_pengaju,
                'anggaran_diajukan' => $request->anggaran_diajukan,
                'status'            => 'dalam_tinjauan',
                'id_project'        => $request->id_project,
                'id_sumber_dana'    => $request->id_sumber_dana,
                'user_id_created'   => Auth::user()->id,
                'user_id_updated'   => Auth::user()->id,
            ]);

            return redirect()->route('proposal.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('proposal.index')->with('error', $e->getMessage());
        }
    }

    public function download($id)
    {
        $proposal = Proposal::find($id);

        return response()->download(public_path('file_proposal/' . $proposal->file_proposal));
    }

    public function approve($id)
    {
        Proposal::where('id', $id)->update(['status' => 'disetujui']);

        return redirect()->route('proposal.index')->with('success', 'Proposal berhasil disetujui');
    }

    public function reject($id)
    {
        Proposal::where('id', $id)->update(['status' => 'ditolak']);

        return redirect()->route('proposal.index')->with('success', 'Proposal berhasil ditolak');
    }
}
