<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\RequestpembelianHeader;
use App\Models\RequestpembelianDetail;
use App\Models\PencatatanKeuangan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil jumlah project
        $totalProjects = Project::count();


        // Mengambil jumlah request pembelian yang masih pending
        $pendingRequests = RequestpembelianHeader::where('status_request', 'pending')->count();

        // Mengambil total transaksi bulan ini
        $totalTransactions = PencatatanKeuangan::whereMonth('created_at', now()->month)->sum('jumlah_transaksi');

        // Menghitung jumlah tim (asumsi user dengan role 'peneliti' adalah tim project)
        $totalTeams = User::where('role', 'peneliti')->count();

        // Menghitung total request pembelian
        $totalRequests = RequestpembelianHeader::count();

        // Req Pembelian Baru
        $totalRequests = RequestpembelianHeader::count();
        $newRequests   = RequestpembelianHeader::where('status_request', 'submit_request')->count();

        // Ambil semua project
        $projects = Project::all();
        $namaProjects = [];
        $pengeluaranPerProject = [];

        foreach ($projects as $project) {
            $namaProjects[] = $project->nama_project;
            $totalPengeluaran = PencatatanKeuangan::where('project_id', $project->id)->sum('jumlah_transaksi');
            $pengeluaranPerProject[] = $totalPengeluaran;
        }

        return view('dashboard', compact(
            'totalRequests',
            'newRequests',
            'totalProjects',
            'pendingRequests',
            'totalTransactions',
            'totalTeams',
            'totalRequests',
            'namaProjects',
            'pengeluaranPerProject'
        ));        
    }
}
