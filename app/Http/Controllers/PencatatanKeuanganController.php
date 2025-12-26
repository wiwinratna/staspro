<?php

namespace App\Http\Controllers;

use App\Models\PencatatanKeuangan;
use App\Models\Project;
use App\Models\SubkategoriSumberdana;
use App\Models\DetailSubkategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PencatatanKeuanganController extends Controller
{
    // Menampilkan semua pencatatan keuangan
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            $pencatatanKeuangans = PencatatanKeuangan::with(['project', 'subKategoriPendanaan', 'requestPembelian'])->get(); 

            $details = DetailSubkategori::with('subkategori')->get();
            
            $totalNominalFiltered = $pencatatanKeuangans->sum('jumlah_transaksi');

            return view('transaksi.pencatatan_keuangan', compact('pencatatanKeuangans', 'totalNominalFiltered'));
        }

        abort(403, 'Unauthorized');
    }

    // Filter pencatatan keuangan berdasarkan rentang tanggal
    public function filterTransaksi(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        $pencatatanKeuangans = PencatatanKeuangan::whereBetween('created_at', [$startDate, $endDate])->get();
        $totalNominalFiltered = $pencatatanKeuangans->sum('jumlah_transaksi');

        // Perbaiki path view
        return view('transaksi.pencatatan_keuangan', compact('pencatatanKeuangans', 'totalNominalFiltered'));
    }

    // Menampilkan form input pencatatan keuangan
    // Menampilkan form input pencatatan keuangan
    public function create(Request $request)
    {
        // ✅ hanya project aktif yang boleh dipilih
        $projects = Project::where('status', 'aktif')->get();

        $subKategoriSumberdana = SubkategoriSumberdana::all(); 
        $tanggalFormatted = now()->format('d-m-Y');

        return view('transaksi.form_input_pencatatan_keuangan', compact('projects', 'subKategoriSumberdana', 'tanggalFormatted'));
    }


    public function getSubkategori(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:project,id',
        ]);

        $project = Project::find($request->project_id);
        
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $subkategori = SubkategoriSumberdana::where('id_sumberdana', $project->id_sumber_dana)->get();
        
        return response()->json($subkategori);
    }

// Menyimpan pencatatan keuangan baru
public function store(Request $request)
{
    $request->validate([
        'tanggal' => 'required|date_format:d-m-Y',
        'project' => 'required|exists:project,id',
        'subkategori_sumberdana' => 'required|exists:subkategori_sumberdana,id',
        'jenis_transaksi' => 'required|in:pemasukan,pengeluaran',
        'deskripsi' => 'required|string|max:255',
        'jumlah_transaksi' => 'required|numeric|min:0',
        'metode_pembayaran' => 'required|string|max:100',
        'bukti_transaksi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ], [
        'tanggal.required' => 'Tanggal harus diisi',
        'project.required' => 'Project harus dipilih',
        'subkategori_sumberdana.required' => 'Subkategori sumber dana harus dipilih',
        'jenis_transaksi.required' => 'Jenis transaksi harus dipilih',
        'jenis_transaksi.in' => 'Jenis transaksi harus pemasukan atau pengeluaran',
        'deskripsi.required' => 'Deskripsi harus diisi',
        'jumlah_transaksi.required' => 'Jumlah transaksi harus diisi',
        'metode_pembayaran.required' => 'Metode pembayaran harus diisi',
    ]);

    try {
        \Log::info('Request data:', $request->all());

        $tanggal = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
        $jumlah = (float) str_replace(['Rp.', ',', ' ', '.'], '', $request->jumlah_transaksi);

        $project = Project::find($request->project);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project tidak ditemukan.']);
        }

        // ✅ CEGAH project yang sudah ditutup masih bisa dipakai transaksi
        // Ganti 'status' / 'aktif' sesuai kolom yang kamu punya (misal is_active, closed_at, dll)
        if (isset($project->status) && $project->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Project sudah ditutup dan tidak dapat digunakan untuk pencatatan.'
            ], 422);
        }

        $dataToSave = [
            'tanggal' => $tanggal,
            'project_id' => $request->project,
            'sub_kategori_pendanaan' => $request->subkategori_sumberdana,
            'jenis_transaksi' => $request->jenis_transaksi,
            'deskripsi_transaksi' => $request->deskripsi,
            'jumlah_transaksi' => $jumlah,
            'metode_pembayaran' => $request->metode_pembayaran,
        ];

        if ($request->hasFile('bukti_transaksi')) {
            $dataToSave['bukti_transaksi'] = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');
        }

        \Log::info('Data to save:', $dataToSave);

        $pencatatanKeuangan = PencatatanKeuangan::create($dataToSave);

        // Update rincian anggaran proyek
        $detail = DetailSubkategori::where('id_subkategori_sumberdana', $request->subkategori_sumberdana)
            ->where('id_project', $project->id)
            ->first();

        if ($detail) {
            if (is_null($detail->realisasi_anggaran)) {
                $detail->realisasi_anggaran = 0;
            }

            if ($request->jenis_transaksi == 'pemasukan') {
                $detail->nominal += $jumlah;
            } else if ($request->jenis_transaksi == 'pengeluaran') {
                if ($detail->realisasi_anggaran + $jumlah <= $detail->nominal) {
                    $detail->realisasi_anggaran += $jumlah;
                } else {
                    return response()->json(['success' => false, 'message' => 'Pengeluaran melebihi anggaran.']);
                }
            }

            $detail->sisa_anggaran = $detail->nominal - $detail->realisasi_anggaran;
            $detail->save();

            \Log::info('Detail setelah update:', [
                'nominal' => $detail->nominal,
                'realisasi_anggaran' => $detail->realisasi_anggaran,
                'sisa_anggaran' => $detail->sisa_anggaran,
            ]);
        } else {
            \Log::error('Detail subkategori tidak ditemukan untuk subkategori: ' . $request->subkategori_sumberdana);
        }

        return response()->json(['success' => true, 'message' => 'Pencatatan keuangan berhasil disimpan.']);

    } catch (\Exception $e) {
        \Log::error('Error saving pencatatan keuangan: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan pencatatan keuangan: ' . $e->getMessage()
        ]);
    }
}


    // Menampilkan form edit pencatatan keuangan
public function edit($id)
{
    $pencatatanKeuangan = PencatatanKeuangan::findOrFail($id);

    // ambil project dari transaksi yang sedang diedit
    $project = Project::find($pencatatanKeuangan->project_id);

    // kalau project tidak ada / sudah ditutup
    if (!$project || (isset($project->status) && $project->status !== 'aktif')) {
        return redirect()->route('pencatatan_keuangan')
            ->with('error', 'Project sudah ditutup / tidak ditemukan, transaksi tidak bisa diedit.');
    }

    // list project aktif untuk dropdown
    $projects = Project::where('status', 'aktif')->orderBy('nama_project')->get();

    // subkategori boleh semua, tapi nanti dropdown kamu isi via fetch by project
    $subKategoriSumberdana = SubkategoriSumberdana::all();

    // format tanggal untuk flatpickr
    $tanggalFormatted = \Carbon\Carbon::parse($pencatatanKeuangan->tanggal)->format('d-m-Y');

    return view('transaksi.form_input_pencatatan_keuangan', compact(
        'pencatatanKeuangan',
        'projects',
        'subKategoriSumberdana',
        'tanggalFormatted'
    ));
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date_format:d-m-Y',
            'project' => 'required|exists:project,id',
            'subkategori_sumberdana' => 'required|exists:subkategori_sumberdana,id',
            'jenis_transaksi' => 'required|in:pemasukan,pengeluaran',
            'deskripsi' => 'required|string|max:255',
            'jumlah_transaksi' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:100',
            'bukti_transaksi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $pencatatanKeuangan = PencatatanKeuangan::findOrFail($id);
            
            $oldJumlah = $pencatatanKeuangan->jumlah_transaksi;
            $oldJenisTransaksi = $pencatatanKeuangan->jenis_transaksi;
            $oldSubkategori = $pencatatanKeuangan->sub_kategori_pendanaan;
            $oldProjectId = $pencatatanKeuangan->project_id;
            
            $jumlah = (float) str_replace(['Rp.', ',', ' ', '.'], '', $request->jumlah_transaksi);

            $updateData = [
                'tanggal' => Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d'),
                'project_id' => $request->project,
                'sub_kategori_pendanaan' => $request->subkategori_sumberdana,
                'jenis_transaksi' => $request->jenis_transaksi,
                'deskripsi_transaksi' => $request->deskripsi,
                'jumlah_transaksi' => $jumlah,
                'metode_pembayaran' => $request->metode_pembayaran,
            ];

            if ($request->hasFile('bukti_transaksi')) {
                $updateData['bukti_transaksi'] = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');
            }

            $oldDetail = DetailSubkategori::where('id_subkategori_sumberdana', $oldSubkategori)
                ->where('id_project', $oldProjectId)
                ->first();

            if ($oldDetail) {
                if ($oldJenisTransaksi == 'pemasukan') {
                    $oldDetail->nominal -= $oldJumlah;
                } else if ($oldJenisTransaksi == 'pengeluaran') {
                    $oldDetail->realisasi_anggaran -= $oldJumlah;
                }
                
                $oldDetail->realisasi_anggaran = max(0, $oldDetail->realisasi_anggaran);
                $oldDetail->sisa_anggaran = $oldDetail->nominal - $oldDetail->realisasi_anggaran;
                $oldDetail->save();
            }

            $pencatatanKeuangan->update($updateData);

            $newDetail = DetailSubkategori::where('id_subkategori_sumberdana', $request->subkategori_sumberdana)
                ->where('id_project', $request->project)
                ->first();

            if ($newDetail) {
                if (is_null($newDetail->realisasi_anggaran)) {
                    $newDetail->realisasi_anggaran = 0;
                }

                if ($request->jenis_transaksi == 'pemasukan') {
                    $newDetail->nominal += $jumlah;
                } else if ($request->jenis_transaksi == 'pengeluaran') {
                    if ($newDetail->realisasi_anggaran + $jumlah <= $newDetail->nominal) {
                        $newDetail->realisasi_anggaran += $jumlah;
                    } else {
                        return response()->json(['success' => false, 'message' => 'Pengeluaran melebihi anggaran yang tersedia.']);
                    }
                }

                $newDetail->sisa_anggaran = $newDetail->nominal - $newDetail->realisasi_anggaran;
                $newDetail->save();

                \Log::info('Detail setelah update:', [
                    'nominal' => $newDetail->nominal,
                    'realisasi_anggaran' => $newDetail->realisasi_anggaran,
                    'sisa_anggaran' => $newDetail->sisa_anggaran,
                ]);
            } else {
                \Log::error('Detail subkategori tidak ditemukan untuk subkategori: ' . $request->subkategori_sumberdana);
            }

            return response()->json(['success' => true, 'message' => 'Data pencatatan keuangan berhasil diperbarui!']);

        } catch (\Exception $e) {
            \Log::error('Error updating pencatatan keuangan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Gagal memperbarui pencatatan keuangan: ' . $e->getMessage()
            ]);
        }
    }

    // Menghapus pencatatan keuangan
    public function destroy($id)
    {
        try {
            \Log::info('Attempting to delete pencatatan keuangan with ID: ' . $id);

            $pencatatanKeuangan = PencatatanKeuangan::findOrFail($id);
            
            // Log data yang akan dihapus
            \Log::info('Found pencatatan keuangan:', $pencatatanKeuangan->toArray());
            
            // Update rincian anggaran sebelum menghapus
            $detail = DetailSubkategori::where('id_subkategori_sumberdana', $pencatatanKeuangan->sub_kategori_pendanaan)
                ->where('id_project', $pencatatanKeuangan->project_id)
                ->first();

            if ($detail) {
                // Kembalikan anggaran berdasarkan jenis transaksi
                if ($pencatatanKeuangan->jenis_transaksi == 'pemasukan') {
                    // Kurangi nominal karena pemasukan dihapus
                    $detail->nominal -= $pencatatanKeuangan->jumlah_transaksi;
                } else if ($pencatatanKeuangan->jenis_transaksi == 'pengeluaran') {
                    // Kurangi realisasi anggaran karena pengeluaran dihapus
                    $detail->realisasi_anggaran -= $pencatatanKeuangan->jumlah_transaksi;
                }

                // Hitung ulang sisa anggaran
                $detail->sisa_anggaran = $detail->nominal - $detail->realisasi_anggaran;
                $detail->save();

                \Log::info('Updated detail after delete:', [
                    'nominal' => $detail->nominal,
                    'realisasi_anggaran' => $detail->realisasi_anggaran,
                    'sisa_anggaran' => $detail->sisa_anggaran,
                ]);
            }

            // Hapus data dari database
            $pencatatanKeuangan->delete();
            
            \Log::info('Successfully deleted pencatatan keuangan with ID: ' . $id);

            return response()->json([
                'success' => true, 
                'message' => 'Data pencatatan keuangan berhasil dihapus.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error deleting pencatatan keuangan: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menghapus data pencatatan keuangan: ' . $e->getMessage()
            ], 500);
        }
    }

public function laporanKeuangan(Request $request)
{
    $projects = Project::all();

    // Mulai query untuk transaksi
    $query = PencatatanKeuangan::query();

    // Filter tim peneliti
    if ($request->filled('tim_peneliti')) {
        $query->where('project_id', $request->tim_peneliti);
    }

    // Filter metode pembayaran
    if ($request->filled('metode_pembayaran')) {
        $query->where('metode_pembayaran', $request->metode_pembayaran);
    }

    // Filter sumber dana
    if ($request->filled('sumber_dana')) {
        $query->whereHas('project.sumberDana', function ($q) use ($request) {
            $q->where('jenis_pendanaan', $request->sumber_dana);
        });
    }

    // ✅ Filter rentang tanggal (pakai kolom `tanggal` transaksi)
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $start = Carbon::parse($request->start_date)->startOfDay()->toDateString();
        $end   = Carbon::parse($request->end_date)->endOfDay()->toDateString();
        $query->whereBetween('tanggal', [$start, $end]);
    }

    // ✅ Ambil data SETELAH semua filter
    $pencatatanKeuangans = $query->get();

    // ✅ Baru hitung tanggal awal/akhir (buat dipakai di export / info)
    $tanggal_awal  = $request->filled('start_date')
        ? $request->start_date
        : optional($pencatatanKeuangans->min('tanggal'))->format('Y-m-d');

    $tanggal_akhir = $request->filled('end_date')
        ? $request->end_date
        : optional($pencatatanKeuangans->max('tanggal'))->format('Y-m-d');

    $totalDebit  = $pencatatanKeuangans->where('jenis_transaksi', 'pemasukan')->sum('jumlah_transaksi');
    $totalKredit = $pencatatanKeuangans->where('jenis_transaksi', 'pengeluaran')->sum('jumlah_transaksi');

    return view('laporan_keuangan', compact(
        'projects',
        'pencatatanKeuangans',
        'totalDebit',
        'totalKredit',
        'tanggal_awal',
        'tanggal_akhir'
    ));
}


    // Export laporan keuangan ke Excel
    public function export(Request $request, $format)
    {
        $query = PencatatanKeuangan::with(['project', 'sumberDana']);
        $filterInfo = [];

        if ($request->filled('tim_peneliti')) {
            $query->where('project_id', $request->tim_peneliti);
            $project = Project::find($request->tim_peneliti);
            $filterInfo['Tim Peneliti'] = $project ? ucwords(strtolower($project->nama_project)) : 'Unknown';
        }

        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
            $filterInfo['Metode Pembayaran'] = ucwords(strtolower($request->metode_pembayaran));
        }

        if ($request->filled('sumber_dana')) {
            $query->whereHas('project.sumberDana', function ($q) use ($request) {
                $q->where('jenis_pendanaan', $request->sumber_dana);
            });
            $filterInfo['Sumber Dana'] = ucfirst(strtolower($request->sumber_dana));
        }

        $pencatatanKeuangans = $query->get();

        $tanggal_awal = $pencatatanKeuangans->min('created_at')?->format('Y-m-d');
        $tanggal_akhir = $pencatatanKeuangans->max('created_at')?->format('Y-m-d');

        if ($format === 'excel') {
            return Excel::download(new LaporanExport($pencatatanKeuangans, $tanggal_awal, $tanggal_akhir), 'Laporan_Keuangan.xlsx');
        } elseif ($format === 'pdf') {
            return PDF::loadView('laporan.pdf', compact('pencatatanKeuangans', 'filterInfo', 'tanggal_awal', 'tanggal_akhir'))
                ->download('Laporan_Keuangan.pdf');
        }

        return redirect()->back()->with('error', 'Format tidak valid');
    }
}
?>