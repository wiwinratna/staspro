<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Project;
use App\Models\SubkategoriSumberdana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // Menampilkan semua transaksi
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            $transaksis = Transaksi::with(['project', 'subKategoriPendanaan', 'requestPembelian'])->get(); 

            $totalNominalFiltered = $transaksis->sum('jumlah_transaksi');

            return view('transaksi.pencatatan_transaksi', compact('transaksis', 'totalNominalFiltered'));
        }

        // Kalau bukan admin, bisa diarahkan ke halaman lain atau kasih abort
        abort(403, 'Unauthorized');
    }

    // Filter transaksi berdasarkan rentang tanggal
    public function filterTransaksi(Request $request)
    {
        // Ambil input tanggal dan ubah jadi full datetime dengan Carbon
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        // Filter transaksi yang created_at-nya berada di antara rentang waktu tersebut
        $transaksis = Transaksi::whereBetween('created_at', [$startDate, $endDate])->get();

        // Hitung total jumlah transaksi dari hasil filter
        $totalNominalFiltered = $transaksis->sum('jumlah_transaksi');

        return view('transaksi.pencatatan_transaksi', compact('transaksis', 'totalNominalFiltered'));
    }

    // Menampilkan form input transaksi
    public function create(Request $request)
    {
        $projects = Project::all();
        $subKategoriSumberdana = SubKategoriSumberdana::all(); 
        $tanggalFormatted = now()->format('d-m-Y');

        return view('transaksi.form_input_transaksi', compact('projects', 'subKategoriSumberdana', 'tanggalFormatted'));
    }

    public function getSubkategori(Request $request)
    {
        // Validasi bahwa project_id ada dalam request
        $request->validate([
            'project_id' => 'required|exists:project,id',
        ]);

        // Ambil proyek berdasarkan ID
        $project = Project::find($request->project_id);
        
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        // Debugging: Cek id_sumber_dana dari proyek
        \Log::info('ID Sumber Dana dari Proyek: ' . $project->id_sumber_dana);

        // Ambil sub kategori berdasarkan ID sumber dana proyek
        $subkategori = SubkategoriSumberdana::where('id_sumberdana', $project->id_sumber_dana)->get();
        
        // Debugging: Cek data subkategori yang ditemukan
        \Log::info('Subkategori found: ', $subkategori->toArray());
        
        return response()->json($subkategori);
    }

    // Menyimpan transaksi baru
    public function store(Request $request)
    {
        \Log::info('Data yang diterima:', $request->all());

        $request->validate([
            'tanggal' => 'required',
            'project' => 'required',
            'subkategori_sumberdana' => 'required|exists:subkategori_sumberdana,id',
            'jenis_transaksi' => 'required',
            'deskripsi' => 'required|string',
            'jumlah_transaksi' => 'required|numeric',
            'metode_pembayaran' => 'required',
            'bukti_transaksi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Convert tanggal dari d-m-Y ke Y-m-d
        $tanggal = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');

        $jumlah = (float) str_replace(['Rp.', ',', ' '], '', $request->jumlah_transaksi);    

        $project = Project::find($request->project);
        if (!$project) {
            return redirect()->back()->with('error', 'Project tidak ditemukan.');
        }

        $path = null;
        if ($request->hasFile('bukti_transaksi')) {
            $file = $request->file('bukti_transaksi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_transaksi', $filename, 'public');
        }

        $transaksi = Transaksi::create([
            'tanggal' => $tanggal,
            'project_id' => $request->project,
            'tim_peneliti' => $project->tim_peneliti, 
            'sub_kategori_pendanaan' => $request->subkategori_sumberdana,
            'jenis_transaksi' => $request->jenis_transaksi,
            'deskripsi_transaksi' => $request->deskripsi,
            'jumlah_transaksi' => $jumlah,
            'metode_pembayaran' => $request->metode_pembayaran,
            'bukti_transaksi' => $path, 
        ]);

        return response()->json(['success' => true, 'message' => 'Transaksi berhasil disimpan.']);
    }

    // Menampilkan form edit transaksi
    public function edit($id) 
    {
        $transaksi = Transaksi::findOrFail($id);
        $projects = Project::all();
        $subKategoriSumberdana = SubKategoriSumberdana::all();

        // Ambil tanggal dari old() atau dari $transaksi
        $tanggal = old('tanggal', $transaksi->tanggal ?? now()->format('Y-m-d'));

        // Konversi ke format d-m-Y
        try {
            $tanggalFormatted = \Carbon\Carbon::parse($tanggal)->format('d-m-Y');
        } catch (\Exception $e) {
            $tanggalFormatted = now()->format('d-m-Y'); // fallback kalau error
        }

        return view('transaksi.form_input_transaksi', compact('transaksi', 'projects', 'subKategoriSumberdana', 'tanggalFormatted'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required',
            'project' => 'required',
            'subkategori_sumberdana' => 'required',
            'jenis_transaksi' => 'required',
            'deskripsi' => 'required|string',
            'jumlah_transaksi' => 'required|numeric',
            'metode_pembayaran' => 'required',
            'bukti_transaksi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $jumlah = (float) str_replace(['Rp.', ',', ' '], '', $request->jumlah_transaksi);

        $path = $transaksi->bukti_transaksi; // Default ke path lama jika tidak ada upload baru

        if ($request->hasFile('bukti_transaksi')) {
            // Hapus file lama jika ada
            if ($transaksi->bukti_transaksi) {
                Storage::disk('public')->delete($transaksi->bukti_transaksi);
            }

            // Simpan file baru
            $file = $request->file('bukti_transaksi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_transaksi', $filename, 'public');
        }

        $tanggal = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');

        // Update data transaksi
        $transaksi->update([
            'tanggal' => $tanggal,
            'project_id' => $request->project,
            'subkategori_sumberdana' => $request->subkategori_sumberdana,
            'jenis_transaksi' => $request->jenis_transaksi,
            'deskripsi_transaksi' => $request-> deskripsi,
            'jumlah_transaksi' => $jumlah,
            'metode_pembayaran' => $request->metode_pembayaran,
            'bukti_transaksi' => $path,
        ]);

        return response()->json(['success' => true, 'message' => 'Data transaksi berhasil diperbarui!']);
    }

    // Menghapus transaksi
    public function destroy($id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);
            $transaksi->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data.']);
        }
    }

    // Laporan Keuangan dengan filter berdasarkan Tim Penelitian dan Kategori Pendanaan
    public function laporanKeuangan(Request $request)
    {
        $projects = Project::all();

        // Mulai query untuk transaksi
        $query = Transaksi::query();

        // Filter berdasarkan tim peneliti
        if ($request->filled('tim_peneliti')) {
            $query->where('project_id', $request->tim_peneliti);
        }

        // Filter berdasarkan metode pembayaran
        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        // Filter berdasarkan sumber dana
        if ($request->filled('sumber_dana')) {
            $query->whereHas('project.sumberDana', function($q) use ($request) {
                $q->where('jenis_pendanaan', $request->sumber_dana);
            });
        }

        // Ambil data transaksi yang sudah difilter
        $transaksis = $query->get();

        // Log untuk memeriksa transaksi yang diambil
        \Log::info('Transaksi yang diambil untuk laporan: ', $transaksis->toArray());

        // Menghitung total pemasukan (debit)
        $totalDebit = $transaksis->where('jenis_transaksi', 'pemasukan')->sum('jumlah_transaksi');

        // Menghitung total pengeluaran (kredit)
        $totalKredit = $transaksis->where('jenis_transaksi', 'pengeluaran')->sum('jumlah_transaksi');

        // Menghitung total keseluruhan
        $totalNominal = $totalDebit - $totalKredit;

        return view('laporan_keuangan', compact('projects', 'transaksis', 'totalDebit', 'totalKredit', 'totalNominal'));
    }

    // Export laporan keuangan ke Excel
    public function export(Request $request, $format)
    {
        $query = Transaksi::with(['project', 'sumberDana']);
        $filterInfo = [];

        // Filter berdasarkan tim peneliti
        if ($request->filled('tim_peneliti')) {
            $query->where('project_id', $request->tim_peneliti);
            $project = Project::find($request->tim_peneliti);
            $filterInfo['Tim Peneliti'] = $project ? ucwords(strtolower($project->nama_project)) : 'Unknown';
        }

        // Filter berdasarkan metode pembayaran
        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
            $filterInfo['Metode Pembayaran'] = ucwords(strtolower($request->metode_pembayaran));
        }

        // Filter berdasarkan sumber dana
        if ($request->filled('sumber_dana')) {
            $query->whereHas('project.sumberDana', function ($q) use ($request) {
                $q->where('jenis_pendanaan', $request->sumber_dana);
            });
            $filterInfo['Sumber Dana'] = ucfirst(strtolower($request->sumber_dana));
        }

        $transaksis = $query->get();

        // Mendapatkan tanggal awal dan akhir
        $tanggal_awal = $transaksis->min('created_at')?->format('Y-m-d');
        $tanggal_akhir = $transaksis->max('created_at')?->format('Y-m-d');

        if ($format === 'excel') {
            return Excel::download(new LaporanExport($transaksis, $tanggal_awal, $tanggal_akhir), 'Laporan_Keuangan.xlsx');
        } elseif ($format === 'pdf') {
            // Tambahkan $tanggal_awal dan $tanggal_akhir ke dalam compact
            return PDF::loadView('laporan.pdf', compact('transaksis', 'filterInfo', 'tanggal_awal', 'tanggal_akhir'))
                ->download('Laporan_Keuangan.pdf');
        }

        return redirect()->back()->with('error', 'Format tidak valid');
    }
}
