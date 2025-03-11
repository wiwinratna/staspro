<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sumberdana;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Tampilkan daftar proyek.
     */
    public function index()
    {
        $projects = Project::all();
        return view('project', compact('projects'));
    }

    /**
     * Form untuk menambah proyek baru.
     */
    public function create()
    {
        $sumber_internal = Sumberdana::where('jenis_pendanaan', 'internal')->get();
        $sumber_eksternal = Sumberdana::where('jenis_pendanaan', 'eksternal')->get();
        return view('input_project', compact('sumber_internal', 'sumber_eksternal'));
    }

    /**
     * Simpan proyek ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama_project'      => 'required|string|max:255',
            'tahun'             => 'required|numeric',
            'durasi'            => 'required|string',
            'deskripsi'         => 'required|string',
            'file_proposal'     => 'required|mimes:pdf|max:2048',
            'file_rab'          => 'required|mimes:xlsx|max:2048',
            'kategori_pendanaan'=> 'required|string',
            'jumlah_dana'       => 'required|numeric',
        ]);

        // Simpan file ke storage/public/proposals dan storage/public/rab_files
        $proposalPath = $request->file('file_proposal')->store('public/proposals');
        $rabPath = $request->file('file_rab')->store('public/rab_files');

        // Simpan data proyek ke database
        Project::create([
            'nama_project'      => $validatedData['nama_project'],
            'tahun'             => $validatedData['tahun'],
            'durasi'            => $validatedData['durasi'],
            'deskripsi'         => $validatedData['deskripsi'],
            'file_proposal'     => str_replace('public/', '', $proposalPath),
            'file_rab'          => str_replace('public/', '', $rabPath),
            'kategori_pendanaan'=> $validatedData['kategori_pendanaan'],
            'jumlah_dana'       => $validatedData['jumlah_dana'],
            'user_id'           => Auth::id(), // Menyimpan ID user yang membuat proyek
        ]);

        return redirect()->route('project.index')->with('success', 'Project berhasil disimpan.');
    }

    /**
     * Tampilkan detail proyek.
     */
    public function show(Project $project)
    {
        $anggota = $project->users()->select('id', 'name')->get();
        $users = User::whereNotIn('id', $anggota->pluck('id'))
                     ->where('id', '!=', Auth::id())
                     ->where('role', '!=', 'admin')
                     ->select('id', 'name')
                     ->get();

        return view('detail_project', compact('project', 'anggota', 'users'));
    }

    /**
     * Download file proposal.
     */
    public function download_proposal(Project $project)
    {
        $filePath = "public/{$project->file_proposal}";

        if (!Storage::exists($filePath)) {
            return back()->with('error', 'File proposal tidak ditemukan.');
        }
        return Storage::download($filePath);
    }

    /**
     * Download file RAB.
     */
    public function download_rab(Project $project)
    {
        $filePath = "public/{$project->file_rab}";

        if (!Storage::exists($filePath)) {
            return back()->with('error', 'File RAB tidak ditemukan.');
        }
        return Storage::download($filePath);
    }
}
