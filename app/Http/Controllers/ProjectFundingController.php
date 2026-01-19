<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFunding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectFundingController extends Controller
{
    // LIST + RINGKASAN
public function index(Request $request)
{
    abort_unless(in_array(Auth::user()->role, ['admin','bendahara']), 403);

    $projectId = $request->get('project_id');
    $metode    = $request->get('metode');
    $sumber    = $request->get('sumber');
    $start     = $request->get('start_date');
    $end       = $request->get('end_date');

    $q = DB::table('project_funding as f')
        ->join('project as p', 'p.id', '=', 'f.project_id')
        ->leftJoin('users as u', 'u.id', '=', 'f.created_by')
        ->select(
            'f.id','f.tanggal','f.nominal','f.metode_penerimaan','f.sumber_dana','f.keterangan','f.bukti',
            'p.nama_project','p.tahun',
            DB::raw('COALESCE(u.name,"-") as created_name')
        )
        ->orderByDesc('f.tanggal')
        ->orderByDesc('f.id');

    if ($projectId) $q->where('f.project_id', (int)$projectId);
    if ($metode)    $q->where('f.metode_penerimaan', $metode);
    if ($sumber)    $q->where('f.sumber_dana', $sumber);

    if ($start && $end) {
        $q->whereBetween('f.tanggal', [$start, $end]);
    } elseif ($start) {
        $q->where('f.tanggal', '>=', $start);
    } elseif ($end) {
        $q->where('f.tanggal', '<=', $end);
    }

    // paginate
    $rows = $q->paginate(15)->withQueryString();

    // ringkasan (pakai base query yg sama)
    $sumQ = DB::table('project_funding as f')
        ->join('project as p', 'p.id', '=', 'f.project_id');

    if ($projectId) $sumQ->where('f.project_id', (int)$projectId);
    if ($metode)    $sumQ->where('f.metode_penerimaan', $metode);
    if ($sumber)    $sumQ->where('f.sumber_dana', $sumber);

    if ($start && $end) {
        $sumQ->whereBetween('f.tanggal', [$start, $end]);
    } elseif ($start) {
        $sumQ->where('f.tanggal', '>=', $start);
    } elseif ($end) {
        $sumQ->where('f.tanggal', '<=', $end);
    }

    $summary = $sumQ->selectRaw('COALESCE(SUM(f.nominal),0) as total_dana')
        ->selectRaw('COUNT(*) as jumlah_transaksi')
        ->selectRaw('COUNT(DISTINCT f.project_id) as jumlah_project')
        ->first();

    $totalDana       = (int) ($summary->total_dana ?? 0);
    $jumlahTransaksi = (int) ($summary->jumlah_transaksi ?? 0);
    $jumlahProject   = (int) ($summary->jumlah_project ?? 0);

    // dropdown filter
    $projects = DB::table('project')
        ->where('status','aktif')
        ->orderBy('nama_project')
        ->get(['id','nama_project','tahun']);

    $metodeOptions = DB::table('project_funding')
        ->select('metode_penerimaan')
        ->whereNotNull('metode_penerimaan')
        ->distinct()
        ->orderBy('metode_penerimaan')
        ->pluck('metode_penerimaan');

    $sumberOptions = DB::table('project_funding')
        ->select('sumber_dana')
        ->whereNotNull('sumber_dana')
        ->distinct()
        ->orderBy('sumber_dana')
        ->pluck('sumber_dana');

    return view('funding.index', compact(
        'rows','projects','metodeOptions','sumberOptions',
        'totalDana','jumlahTransaksi','jumlahProject',
        'projectId','metode','sumber','start','end'
    ));
}

    // FORM
    // FORM
    public function create()
    {
        // admin & bendahara
        abort_unless(in_array(Auth::user()->role, ['admin','bendahara']), 403);

        // ✅ hanya project yang sudah APPROVED (biar dana cair ga salah sasaran)
        $projects = DB::table('project')
        ->where('status', 'aktif')
        ->where('workflow_status', 'approved')
        ->orderByDesc('id')
        ->get(['id','nama_project','tahun']);

        return view('funding.create', compact('projects'));
    }


    // SIMPAN
public function store(Request $request)
{
    abort_unless(in_array(Auth::user()->role, ['admin','bendahara']), 403);

    $request->validate([
        'project_id' => 'required|integer|exists:project,id',
        'tanggal'    => 'required|date',
        'nominal'    => 'required',
        'metode_penerimaan' => 'nullable|string|max:80',
        'sumber_dana' => 'nullable|string|max:150',
        'keterangan' => 'nullable|string',
        'bukti'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ]);

    // bersihin rupiah "Rp 1.000.000" -> 1000000
    $raw = (string) $request->nominal;
    $nominal = (int) preg_replace('/[^0-9]/', '', $raw);
    if ($nominal <= 0) {
        return back()->withErrors(['nominal' => 'Nominal harus lebih dari 0'])->withInput();
    }

    // ✅ upload bukti (kalau ada)
    $buktiName = null;
    if ($request->hasFile('bukti')) {
        $file = $request->file('bukti');
        $buktiName = time().'_funding_'.$file->getClientOriginalName();
        $file->storeAs('project_funding', $buktiName, 'public');
    }

    DB::transaction(function () use ($request, $nominal, $buktiName) {

        ProjectFunding::create([
            'project_id'        => (int) $request->project_id,
            'tanggal'           => $request->tanggal,
            'nominal'           => $nominal,
            'metode_penerimaan' => $request->metode_penerimaan,
            'sumber_dana'       => $request->sumber_dana,
            'keterangan'        => $request->keterangan,
            'bukti'             => $buktiName,
            'created_by'        => Auth::id(),
        ]);

        // ✅ AUTO FUNDED
        $p = Project::findOrFail((int) $request->project_id);

        if (($p->workflow_status ?? '') !== 'approved') {
            abort(403, 'Project belum approved, tidak bisa input dana cair.');
        }

        $p->update([
            'workflow_status' => 'funded',
            'funded_at'       => now(),
        ]);
    });

    return redirect()
        ->route('funding.index')
        ->with('success', 'Dana cair berhasil diinput & project otomatis menjadi FUNDED.');
}

    // (opsional) download bukti
    public function downloadBukti($id)
    {
        abort_unless(in_array(Auth::user()->role, ['admin','bendahara']), 403);

        $row = ProjectFunding::findOrFail($id);
        if (!$row->bukti) return back()->with('error','Bukti tidak tersedia.');

        $path = 'project_funding/'.$row->bukti;
        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error','File bukti tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($path, $row->bukti);
    }
}
