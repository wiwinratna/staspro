<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    public function index()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'peneliti', 403);

        $rows = Project::query()
            ->where('user_id_created', Auth::id())
            ->leftJoin('project_funding as pf', 'pf.project_id', '=', 'project.id')
            ->select([
                'project.id',
                'project.nama_project',
                'project.tahun',
                'project.workflow_status',
                'project.submitted_at',
                'project.approved_at',
                'project.funded_at',
                'project.finalized_at',
                'project.status',
                'project.user_id_created',
                DB::raw('COALESCE(SUM(pf.nominal),0) as funded_total'),
            ])
            ->groupBy([
                'project.id',
                'project.nama_project',
                'project.tahun',
                'project.workflow_status',
                'project.submitted_at',
                'project.approved_at',
                'project.funded_at',
                'project.finalized_at',
                'project.status',
                'project.user_id_created',
            ])
            ->orderByDesc('project.id')
            ->get();

        $countSubmitted = $rows->where('workflow_status', 'submitted')->count();
        $countApproved  = $rows->where('workflow_status', 'approved')->count();
        $countFunded    = $rows->where('workflow_status', 'funded')->count();
        $countFinalized = $rows->where('workflow_status', 'finalized')->count();

        return view('pengajuan.saya', compact(
            'rows','countSubmitted','countApproved','countFunded','countFinalized'
        ));
    }


    // ✅ ADMIN: daftar pengajuan masuk
    // ✅ ADMIN/BENDAHARA: daftar pengajuan masuk
    public function masuk()
    {
        abort_unless(Auth::check() && in_array(Auth::user()->role, ['admin','bendahara']), 403);

        $rows = Project::query()
            ->with('createdBy:id,name') // opsional
            ->orderByDesc('id')
            ->get([
                'id','nama_project','tahun',
                'workflow_status',
                'submitted_at','approved_at','funded_at','finalized_at',
                'user_id_created','status'
            ]);

        $countSubmitted = $rows->where('workflow_status', 'submitted')->count();
        $countApproved  = $rows->where('workflow_status', 'approved')->count();
        $countFunded    = $rows->where('workflow_status', 'funded')->count();
        $countFinalized = $rows->where('workflow_status', 'finalized')->count();

        return view('pengajuan.masuk', compact(
            'rows','countSubmitted','countApproved','countFunded','countFinalized'
        ));
    }


    // ✅ ADMIN: approve
    public function approve(Project $project)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        // hanya boleh approve kalau masih submitted
        if (($project->workflow_status ?? '') !== 'submitted') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $project->update([
            'workflow_status' => 'approved',
            'approved_at'     => now(),
        ]);

        return back()->with('success', 'Pengajuan berhasil di-approve.');
    }

    // ✅ ADMIN: reject (kita balikin jadi submitted/atau kasih status rejected)
    public function reject(Request $request, Project $project)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        if (($project->workflow_status ?? '') !== 'submitted') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        // opsi 1 (paling simple): balikin ke submitted tapi reset file? (ga perlu)
        // opsi 2 (lebih rapi): bikin workflow_status = 'rejected'
        // Aku pilih opsi 2 biar jelas.

        $project->update([
            'workflow_status' => 'rejected',
            'approved_at'     => null, // pastiin kosong
        ]);

        return back()->with('success', 'Pengajuan berhasil di-reject.');
    }

    public function fund(Project $project)
{
    // admin/bendahara
    abort_unless(in_array(auth()->user()->role, ['admin','bendahara']), 403);

    // biar rapi: cuma boleh fund kalau sudah approved
    abort_unless(($project->workflow_status ?? '') === 'approved', 403);

    $project->update([
        'workflow_status' => 'funded',
        'funded_at' => now(),
    ]);

    return back()->with('success','Status berhasil diubah menjadi FUNDED (Dana Cair).');
}

public function finalize(Project $project)
{
    abort_unless(in_array(auth()->user()->role, ['admin','bendahara']), 403);

    // cuma boleh finalize kalau sudah funded
    abort_unless(($project->workflow_status ?? '') === 'funded', 403);

    $project->update([
        'workflow_status' => 'finalized',
        'finalized_at' => now(),
    ]);

    return back()->with('success','Project berhasil di-FINALIZE.');
}

}
