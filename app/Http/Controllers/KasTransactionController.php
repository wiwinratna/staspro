<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KasTransaction;

class KasTransactionController extends Controller
{
    public function index()
    {
        $rows = DB::table('kas_transactions')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $totalMasuk  = $rows->where('tipe', 'masuk')->sum('nominal');
        $totalKeluar = $rows->where('tipe', 'keluar')->sum('nominal');
        $saldo = $totalMasuk - $totalKeluar;

        return view('kas.index', compact('rows', 'totalMasuk', 'totalKeluar', 'saldo'));
    }

    public function create()
    {
        return view('kas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:masuk,keluar',
            'kategori' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $data['created_by'] = auth()->id();

        KasTransaction::create($data);

        return redirect()->route('kas.index')
            ->with('success', 'Transaksi kas berhasil ditambahkan');
    }
}
