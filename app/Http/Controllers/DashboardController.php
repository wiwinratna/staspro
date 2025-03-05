<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\RequestpembelianHeader;

class DashboardController extends Controller
{
    public function index()
    {
        $project = Project::count();
        $request_pembelian = RequestpembelianHeader::count();

        return view('dashboard', ['project' => $project, 'request_pembelian' => $request_pembelian]);
    }
}
