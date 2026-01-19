<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BendaharaDashboardController extends Controller
{
    public function index(Request $request)
    {
        // ✅ guard role
        abort_unless(Auth::check() && Auth::user()->role === 'bendahara', 403);

        // ✅ default periode: bulan ini (bisa kamu ganti nanti)
        $start = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : now()->startOfMonth();

        $end = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : now()->endOfMonth();

        // =========================================================
        // A) Pending verifikasi (submit_payment) + total nominal pending
        // =========================================================
        $pending = DB::table('request_pembelian_header as h')
            ->leftJoin('request_pembelian_detail as d', 'd.id_request_pembelian_header', '=', 'h.id')
            ->leftJoin('project as p', 'p.id', '=', 'h.id_project')
            ->where('h.status_request', 'submit_payment')
            ->selectRaw('COUNT(DISTINCT h.id) as pending_count')
            ->selectRaw('COALESCE(SUM(d.kuantitas * d.harga),0) as pending_nominal')
            ->first();

        $pendingCount   = (int) ($pending->pending_count ?? 0);
        $pendingNominal = (float) ($pending->pending_nominal ?? 0);

        // =========================================================
        // B) Done pengeluaran pada periode (dari pencatatan_keuangan)
        // =========================================================
        $donePengeluaranPeriod = DB::table('pencatatan_keuangan')
            ->where('jenis_transaksi', 'pengeluaran')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('jumlah_transaksi');

        // (opsional) pemasukan periode (kalau kamu mau tampilkan juga)
        $pemasukanPeriod = DB::table('pencatatan_keuangan')
            ->where('jenis_transaksi', 'pemasukan')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('jumlah_transaksi');

        // =========================================================
        // C) Reject payment count
        // =========================================================
        $rejectPaymentCount = DB::table('request_pembelian_header')
            ->where('status_request', 'reject_payment')
            ->count();

        // =========================================================
        // D) Kas (read-only) periode (dari kas_transactions)
        // =========================================================
        $kasMasuk = DB::table('kas_transactions')
            ->where('tipe', 'masuk')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('nominal');

        $kasKeluar = DB::table('kas_transactions')
            ->where('tipe', 'keluar')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('nominal');

        $kasSaldo = $kasMasuk - $kasKeluar;

        // =========================================================
        // E) List kerja bendahara: Top 10 submit_payment
        // =========================================================
        $pendingList = DB::table('request_pembelian_header as h')
            ->leftJoin('project as p', 'p.id', '=', 'h.id_project')
            ->leftJoin(DB::raw("
                (SELECT id_request_pembelian_header,
                        SUM(harga * kuantitas) as total_harga
                 FROM request_pembelian_detail
                 GROUP BY id_request_pembelian_header
                ) as x
            "), 'x.id_request_pembelian_header', '=', 'h.id')
            ->select(
                'h.id',
                'h.no_request',
                'h.tgl_request',
                'h.status_request',
                'p.nama_project',
                DB::raw('COALESCE(x.total_harga,0) as total_harga')
            )
            ->where('h.status_request', 'submit_payment')
            ->orderByDesc('h.id')
            ->limit(10)
            ->get();

        // =========================================================
        // (Opsional) F) ringkasan project aktif vs ditutup
        // =========================================================
        $projectAktif = DB::table('project')->where('status', 'aktif')->count();
        $projectDitutup = DB::table('project')->where('status', 'ditutup')->count();

        return view('bendahara.dashboard', compact(
            'start','end',
            'pendingCount','pendingNominal',
            'donePengeluaranPeriod','pemasukanPeriod',
            'rejectPaymentCount',
            'kasMasuk','kasKeluar','kasSaldo',
            'pendingList',
            'projectAktif','projectDitutup'
        ));
    }
}
