<?php

namespace App\Http\Controllers;

use App\Models\PencatatanKeuangan;
use App\Models\Project;
use App\Models\SubkategoriSumberdana;
use App\Models\DetailSubkategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PencatatanKeuanganController extends Controller
{
    /**
     * =========================================================
     * Helper: anggaran aktif & update anggaran revisi
     * =========================================================
     */
    private function anggaranAktif(DetailSubkategori $detail): float
    {
        return !is_null($detail->anggaran_revisi)
            ? (float) $detail->anggaran_revisi
            : (float) $detail->nominal;
    }

    private function tambahKeAnggaranRevisi(DetailSubkategori $detail, float $jumlah): void
    {
        // ✅ pemasukan selalu masuk ke revisi
        // kalau revisi masih null, jadikan revisi = nominal (awal) + pemasukan
        if (is_null($detail->anggaran_revisi)) {
            $detail->anggaran_revisi = (float) $detail->nominal + $jumlah;
        } else {
            $detail->anggaran_revisi = (float) $detail->anggaran_revisi + $jumlah;
        }
    }

    private function kurangKeAnggaranRevisi(DetailSubkategori $detail, float $jumlah): void
    {
        // rollback pemasukan: harus ngurangin revisi
        if (!is_null($detail->anggaran_revisi)) {
            $detail->anggaran_revisi = max(0, (float) $detail->anggaran_revisi - $jumlah);
        } else {
            // kalau revisi null (harusnya jarang), fallback kurangi nominal
            $detail->nominal = max(0, (float) $detail->nominal - $jumlah);
        }
    }

    /**
     * =========================================================
     * AUTO: Dana Cair (project_funding) -> pencatatan_keuangan
     * Tanpa sub_kategori_pendanaan & request_pembelian_id (NULL)
     * =========================================================
     */
    private function syncDanaCairFinalized(): void
    {
        $fundings = DB::table('project_funding as pf')
            ->join('project as p', 'p.id', '=', 'pf.project_id')
            ->where('p.workflow_status', 'finalized')
            ->select(
                'pf.id',
                'pf.project_id',
                'pf.tanggal',
                'pf.nominal',
                'pf.metode_penerimaan',
                'pf.keterangan',
                'pf.bukti'
            )
            ->get();

        foreach ($fundings as $f) {
            $marker = "[FUNDING#{$f->id}]";

            $exists = DB::table('pencatatan_keuangan')
                ->where('project_id', $f->project_id)
                ->where('jenis_transaksi', 'pemasukan')
                ->where('deskripsi_transaksi', 'like', "%{$marker}%")
                ->exists();

            if ($exists) continue;

            DB::table('pencatatan_keuangan')->insert([
                'tanggal' => $f->tanggal ?? now()->toDateString(),
                'project_id' => $f->project_id,
                'sub_kategori_pendanaan' => null,
                'jenis_transaksi' => 'pemasukan',
                'deskripsi_transaksi' => "{$marker} Dana Cair (Auto Finalized) - " . ($f->keterangan ?? '-'),
                'jumlah_transaksi' => (float) $f->nominal,
                'metode_pembayaran' => $f->metode_penerimaan ? strtolower($f->metode_penerimaan) : 'transfer bank',
                'bukti_transaksi' => $f->bukti ?? null,
                'request_pembelian_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

public function index()
{
    if (in_array(auth()->user()->role, ['admin','bendahara'])) {

        // ✅ sync dana cair tetap jalan (boleh untuk bendahara juga)
        $this->syncDanaCairFinalized();

        $pencatatanKeuangans = PencatatanKeuangan::with(['project', 'subKategoriPendanaan', 'requestPembelian'])->get();

        $totalNominalFiltered = $pencatatanKeuangans->sum('jumlah_transaksi');

        return view('transaksi.pencatatan_keuangan', compact('pencatatanKeuangans', 'totalNominalFiltered'));
    }

    abort(403, 'Unauthorized');
}

    public function filterTransaksi(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        $pencatatanKeuangans = PencatatanKeuangan::with(['project', 'subKategoriPendanaan', 'requestPembelian'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalNominalFiltered = $pencatatanKeuangans->sum('jumlah_transaksi');

        return view('transaksi.pencatatan_keuangan', compact('pencatatanKeuangans', 'totalNominalFiltered'));
    }

    public function create(Request $request)
    {
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
        ]);

        try {
            $tanggal = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
            $jumlah = (float) str_replace(['Rp.', ',', ' ', '.'], '', $request->jumlah_transaksi);

            $project = Project::find($request->project);
            if (!$project) {
                return response()->json(['success' => false, 'message' => 'Project tidak ditemukan.']);
            }

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

            $pencatatanKeuangan = PencatatanKeuangan::create($dataToSave);

            $detail = DetailSubkategori::where('id_subkategori_sumberdana', $request->subkategori_sumberdana)
                ->where('id_project', $project->id)
                ->first();

            if ($detail) {
                if (is_null($detail->realisasi_anggaran)) {
                    $detail->realisasi_anggaran = 0;
                }

                $anggaranAktif = $this->anggaranAktif($detail);

                if ($request->jenis_transaksi == 'pemasukan') {
                    // ✅ PEMASUKAN -> MASUK KE ANGGARAN REVISI
                    $this->tambahKeAnggaranRevisi($detail, $jumlah);

                } else if ($request->jenis_transaksi == 'pengeluaran') {
                    // ✅ PENGELUARAN -> BATASNYA PAKAI ANGGARAN AKTIF (revisi kalau ada)
                    if ($detail->realisasi_anggaran + $jumlah <= $anggaranAktif) {
                        $detail->realisasi_anggaran += $jumlah;
                    } else {
                        return response()->json(['success' => false, 'message' => 'Pengeluaran melebihi anggaran.']);
                    }
                }

                $anggaranAktifAfter = $this->anggaranAktif($detail);
                $detail->sisa_anggaran = $anggaranAktifAfter - (float) $detail->realisasi_anggaran;

                $detail->save();
            }

            return response()->json(['success' => true, 'message' => 'Pencatatan keuangan berhasil disimpan.']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pencatatan keuangan: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        $pencatatanKeuangan = PencatatanKeuangan::findOrFail($id);
        $project = Project::find($pencatatanKeuangan->project_id);

        if (!$project || (isset($project->status) && $project->status !== 'aktif')) {
            return redirect()->route('pencatatan_keuangan')
                ->with('error', 'Project sudah ditutup / tidak ditemukan, transaksi tidak bisa diedit.');
        }

        $projects = Project::where('status', 'aktif')->orderBy('nama_project')->get();
        $subKategoriSumberdana = SubkategoriSumberdana::all();
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

            $oldJumlah = (float) $pencatatanKeuangan->jumlah_transaksi;
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

            // === rollback detail lama
            $oldDetail = DetailSubkategori::where('id_subkategori_sumberdana', $oldSubkategori)
                ->where('id_project', $oldProjectId)
                ->first();

            if ($oldDetail) {
                if (is_null($oldDetail->realisasi_anggaran)) {
                    $oldDetail->realisasi_anggaran = 0;
                }

                if ($oldJenisTransaksi == 'pemasukan') {
                    // ✅ rollback pemasukan -> kurangi dari revisi (kalau ada)
                    $this->kurangKeAnggaranRevisi($oldDetail, $oldJumlah);
                } else if ($oldJenisTransaksi == 'pengeluaran') {
                    $oldDetail->realisasi_anggaran = max(0, (float) $oldDetail->realisasi_anggaran - $oldJumlah);
                }

                $oldAnggaranAktifAfter = $this->anggaranAktif($oldDetail);
                $oldDetail->sisa_anggaran = $oldAnggaranAktifAfter - (float) $oldDetail->realisasi_anggaran;

                $oldDetail->save();
            }

            // update transaksi
            $pencatatanKeuangan->update($updateData);

            // === apply detail baru
            $newDetail = DetailSubkategori::where('id_subkategori_sumberdana', $request->subkategori_sumberdana)
                ->where('id_project', $request->project)
                ->first();

            if ($newDetail) {
                if (is_null($newDetail->realisasi_anggaran)) {
                    $newDetail->realisasi_anggaran = 0;
                }

                $newAnggaranAktif = $this->anggaranAktif($newDetail);

                if ($request->jenis_transaksi == 'pemasukan') {
                    // ✅ pemasukan -> revisi
                    $this->tambahKeAnggaranRevisi($newDetail, $jumlah);
                } else if ($request->jenis_transaksi == 'pengeluaran') {
                    if ($newDetail->realisasi_anggaran + $jumlah <= $newAnggaranAktif) {
                        $newDetail->realisasi_anggaran += $jumlah;
                    } else {
                        return response()->json(['success' => false, 'message' => 'Pengeluaran melebihi anggaran yang tersedia.']);
                    }
                }

                $newAnggaranAktifAfter = $this->anggaranAktif($newDetail);
                $newDetail->sisa_anggaran = $newAnggaranAktifAfter - (float) $newDetail->realisasi_anggaran;

                $newDetail->save();
            }

            return response()->json(['success' => true, 'message' => 'Data pencatatan keuangan berhasil diperbarui!']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pencatatan keuangan: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $pencatatanKeuangan = PencatatanKeuangan::findOrFail($id);

            $detail = DetailSubkategori::where('id_subkategori_sumberdana', $pencatatanKeuangan->sub_kategori_pendanaan)
                ->where('id_project', $pencatatanKeuangan->project_id)
                ->first();

            if ($detail) {
                if (is_null($detail->realisasi_anggaran)) {
                    $detail->realisasi_anggaran = 0;
                }

                $jumlah = (float) $pencatatanKeuangan->jumlah_transaksi;

                if ($pencatatanKeuangan->jenis_transaksi == 'pemasukan') {
                    // ✅ hapus pemasukan -> rollback dari revisi
                    $this->kurangKeAnggaranRevisi($detail, $jumlah);
                } else if ($pencatatanKeuangan->jenis_transaksi == 'pengeluaran') {
                    $detail->realisasi_anggaran = max(0, (float) $detail->realisasi_anggaran - $jumlah);
                }

                $anggaranAktifAfter = $this->anggaranAktif($detail);
                $detail->sisa_anggaran = $anggaranAktifAfter - (float) $detail->realisasi_anggaran;

                $detail->save();
            }

            $pencatatanKeuangan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data pencatatan keuangan berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data pencatatan keuangan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function laporanKeuangan(Request $request)
    {
        $projects = Project::all();
        $query = PencatatanKeuangan::query();

        if ($request->filled('tim_peneliti')) {
            $query->where('project_id', $request->tim_peneliti);
        }

        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        if ($request->filled('sumber_dana')) {
            $query->whereHas('project.sumberDana', function ($q) use ($request) {
                $q->where('jenis_pendanaan', $request->sumber_dana);
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay()->toDateString();
            $end   = Carbon::parse($request->end_date)->endOfDay()->toDateString();
            $query->whereBetween('tanggal', [$start, $end]);
        }

        $pencatatanKeuangans = $query->get();

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
