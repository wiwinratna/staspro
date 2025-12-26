<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\RequestpembelianHeader;
use App\Models\PencatatanKeuangan;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

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
        // 2) PROJECT COUNTS
        // =========================
        $projectBase = Project::query();
        // kalau kamu mau peneliti cuma lihat project yang dia buat:
        if (!$isAdmin && \Schema::hasColumn('project', 'user_id_created')) {
            $projectBase->where('user_id_created', $user->id);
        }

        $totalProjects  = (clone $projectBase)->count();
        $activeProjects = (clone $projectBase)->where('status', 'aktif')->count();
        $closedProjects = (clone $projectBase)->where('status', 'ditutup')->count();

        // Project terbaru (top 3)
        $latestProjects = (clone $projectBase)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get(['id','nama_project','tahun','durasi','status','created_at']);

        // =========================
        // 3) REQUEST PEMBELIAN (PERIODE)
        // =========================
        $reqBase = RequestpembelianHeader::query();

        if (!$isAdmin && \Schema::hasColumn('request_pembelian_header', 'user_id_created')) {
            $reqBase->where('user_id_created', $user->id);
        }

        // total request periode pakai tgl_request (sesuai tabel kamu)
        $reqPeriod = (clone $reqBase)->whereBetween('tgl_request', [$start->toDateString(), $end->toDateString()]);

        $totalRequestsPeriod    = (clone $reqPeriod)->count();
        $submitRequestsPeriod   = (clone $reqPeriod)->where('status_request', 'submit_request')->count();
        $approvedRequestsPeriod = (clone $reqPeriod)->where('status_request', 'approve_request')->count();
        $rejectedRequestsPeriod = (clone $reqPeriod)->where('status_request', 'reject_request')->count();
        $doneRequestsPeriod     = (clone $reqPeriod)->where('status_request', 'done')->count();

        // "request baru" = submit_request pada periode (biar tidak ngaco)
        $newRequests = $submitRequestsPeriod;

        // Request terbaru (top 3) pada periode
        $latestRequests = (clone $reqPeriod)
            ->orderByDesc('tgl_request')
            ->orderByDesc('id')
            ->limit(3)
            ->get(['id','no_request','tgl_request','status_request']);

        // Untuk peneliti (kalau Blade butuh)
        $myRequestsPeriod = $totalRequestsPeriod;
        $mySubmitRequestsPeriod = $submitRequestsPeriod;
        $myApprovedRequestsPeriod = $approvedRequestsPeriod;
        $myRejectedRequestsPeriod = $rejectedRequestsPeriod;
        $myDoneRequestsPeriod = $doneRequestsPeriod;

        // =========================
        // 4) PENCATATAN KEUANGAN (PERIODE) - ADMIN SAJA
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

            // Grafik pengeluaran per proyek (pakai project_id di pencatatan_keuangan)
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
        // 5) KAS (PERIODE) - ADMIN SAJA (pakai DB::table kalau belum ada model)
        // =========================
        $kasMasukPeriod = 0;
        $kasKeluarPeriod = 0;
        $kasCountPeriod = 0;

        if ($isAdmin) {
            // kalau kamu punya model KasTransaction pakai model, kalau belum pakai query builder:
            $kasBase = \DB::table('kas_transactions')
                ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);

            $kasMasukPeriod = (clone $kasBase)->where('tipe', 'masuk')->sum('nominal');
            $kasKeluarPeriod = (clone $kasBase)->where('tipe', 'keluar')->sum('nominal');
            $kasCountPeriod = (clone $kasBase)->count();
        }

        // =========================
        // 6) TIM PENELITI (ADMIN SAJA)
        // =========================
        $totalTeams = $isAdmin ? User::where('role', 'peneliti')->count() : 0;

        return view('dashboard', compact(
            // range
            'startDate','endDate',

            // project
            'totalProjects','activeProjects','closedProjects','latestProjects',

            // request periode
            'totalRequestsPeriod','submitRequestsPeriod','approvedRequestsPeriod','rejectedRequestsPeriod','doneRequestsPeriod',
            'newRequests','latestRequests',

            // peneliti aliases (biar Blade aman)
            'myRequestsPeriod','mySubmitRequestsPeriod','myApprovedRequestsPeriod','myRejectedRequestsPeriod','myDoneRequestsPeriod',

            // pencatatan (admin)
            'pencatatanMasukPeriod','pencatatanKeluarPeriod','pencatatanCountPeriod',
            'namaProjects','pengeluaranPerProject',

            // kas (admin)
            'kasMasukPeriod','kasKeluarPeriod','kasCountPeriod',

            // teams
            'totalTeams'
        ));
    }
}
