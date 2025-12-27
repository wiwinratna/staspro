<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\RequestpembelianHeader;
use App\Models\PencatatanKeuangan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = ($user->role === 'admin');

        // =========================
        // 1) RANGE TANGGAL (default 30 hari terakhir)
        // =========================
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (!$startDate || !$endDate) {
            $start = now()->subDays(30)->startOfDay();
            $end   = now()->endOfDay();
            $startDate = $start->toDateString();
            $endDate   = $end->toDateString();
        } else {
            $start = Carbon::parse($startDate)->startOfDay();
            $end   = Carbon::parse($endDate)->endOfDay();
        }

        // =========================
        // 2) PROJECT IDs yang bisa diakses peneliti (BERDASARKAN detail_project)
        // =========================
        $joinedProjectIds = collect();

        if (!$isAdmin) {
            $joinedProjectIds = DB::table('detail_project')
                ->where('id_user', $user->id)
                ->pluck('id_project')
                ->map(fn($v) => (int)$v)
                ->unique()
                ->values();
        }

        // =========================
        // 3) PROJECT COUNTS + latest projects
        // =========================
        $projectBase = Project::query();

        if (!$isAdmin) {
            $projectBase->whereIn('id', $joinedProjectIds->isEmpty() ? [0] : $joinedProjectIds->toArray());
        }

        $totalProjects  = (clone $projectBase)->count();
        $activeProjects = (clone $projectBase)->where('status', 'aktif')->count();
        $closedProjects = (clone $projectBase)->where('status', 'ditutup')->count();

        $latestProjects = (clone $projectBase)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get(['id','nama_project','tahun','durasi','status','created_at']);

        // =========================
        // 4) REQUEST PEMBELIAN (PERIODE)
        // =========================
        $reqBase = RequestpembelianHeader::query();

        if (!$isAdmin) {
            // Peneliti: request yang project-nya dia join
            if (Schema::hasColumn('request_pembelian_header', 'id_project')) {
                $reqBase->whereIn('id_project', $joinedProjectIds->isEmpty() ? [0] : $joinedProjectIds->toArray());
            } elseif (Schema::hasColumn('request_pembelian_header', 'project_id')) {
                $reqBase->whereIn('project_id', $joinedProjectIds->isEmpty() ? [0] : $joinedProjectIds->toArray());
            }
        }

        $reqPeriod = (clone $reqBase)
            ->whereBetween('tgl_request', [$start->toDateString(), $end->toDateString()]);

        $totalRequestsPeriod    = (clone $reqPeriod)->count();
        $submitRequestsPeriod   = (clone $reqPeriod)->where('status_request', 'submit_request')->count();
        $approvedRequestsPeriod = (clone $reqPeriod)->where('status_request', 'approve_request')->count();
        $rejectedRequestsPeriod = (clone $reqPeriod)->where('status_request', 'reject_request')->count();
        $doneRequestsPeriod     = (clone $reqPeriod)->where('status_request', 'done')->count();

        $newRequests = $submitRequestsPeriod;

        $latestRequests = (clone $reqPeriod)
            ->orderByDesc('tgl_request')
            ->orderByDesc('id')
            ->limit(3)
            ->get(['id','no_request','tgl_request','status_request']);

        // Alias biar blade peneliti aman
        $myRequestsPeriod         = $totalRequestsPeriod;
        $mySubmitRequestsPeriod   = $submitRequestsPeriod;
        $myApprovedRequestsPeriod = $approvedRequestsPeriod;
        $myRejectedRequestsPeriod = $rejectedRequestsPeriod;
        $myDoneRequestsPeriod     = $doneRequestsPeriod;

        // =========================
        // 5) PENCATATAN KEUANGAN (PERIODE) - ADMIN SAJA
        // =========================
        $pencatatanMasukPeriod = 0;
        $pencatatanKeluarPeriod = 0;
        $pencatatanCountPeriod = 0;

        $namaProjects = [];
        $pengeluaranPerProject = [];

        if ($isAdmin) {
            $keuBase = PencatatanKeuangan::query()
                ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);

            $pencatatanMasukPeriod = (clone $keuBase)
                ->where('jenis_transaksi', 'pemasukan')
                ->sum('jumlah_transaksi');

            $pencatatanKeluarPeriod = (clone $keuBase)
                ->where('jenis_transaksi', 'pengeluaran')
                ->sum('jumlah_transaksi');

            $pencatatanCountPeriod = (clone $keuBase)->count();

            $projectsForChart = Project::orderBy('nama_project')->get(['id','nama_project']);
            foreach ($projectsForChart as $p) {
                $namaProjects[] = $p->nama_project;
                $pengeluaranPerProject[] = (clone $keuBase)
                    ->where('project_id', $p->id)
                    ->where('jenis_transaksi', 'pengeluaran')
                    ->sum('jumlah_transaksi');
            }
        }

        // =========================
        // 6) KAS (PERIODE) - ADMIN SAJA
        // =========================
        $kasMasukPeriod = 0;
        $kasKeluarPeriod = 0;
        $kasCountPeriod = 0;

        if ($isAdmin) {
            $kasBase = DB::table('kas_transactions')
                ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);

            $kasMasukPeriod  = (clone $kasBase)->where('tipe', 'masuk')->sum('nominal');
            $kasKeluarPeriod = (clone $kasBase)->where('tipe', 'keluar')->sum('nominal');
            $kasCountPeriod  = (clone $kasBase)->count();
        }

        // =========================
        // 7) TIM PENELITI (ADMIN SAJA)
        // =========================
        $totalTeams = $isAdmin ? User::where('role', 'peneliti')->count() : 0;

        return view('dashboard', compact(
            'startDate','endDate',
            'totalProjects','activeProjects','closedProjects','latestProjects',
            'totalRequestsPeriod','submitRequestsPeriod','approvedRequestsPeriod','rejectedRequestsPeriod','doneRequestsPeriod',
            'newRequests','latestRequests',
            'myRequestsPeriod','mySubmitRequestsPeriod','myApprovedRequestsPeriod','myRejectedRequestsPeriod','myDoneRequestsPeriod',
            'pencatatanMasukPeriod','pencatatanKeluarPeriod','pencatatanCountPeriod',
            'namaProjects','pengeluaranPerProject',
            'kasMasukPeriod','kasKeluarPeriod','kasCountPeriod',
            'totalTeams'
        ));
    }
}
