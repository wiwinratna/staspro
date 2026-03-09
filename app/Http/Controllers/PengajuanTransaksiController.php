<?php

namespace App\Http\Controllers;

use App\Models\PengajuanTransaksiHeader;
use App\Models\Project;
use App\Models\SubkategoriSumberdana;
use App\Models\PencatatanKeuangan; // ✅ sesuaikan kalau nama model pencatatan kamu berbeda
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengajuanTransaksiController extends Controller
{
    /**
     * LIST
     * - Optional filter: ?status=submit/approve/bukti/done/reject
     * - View expects: $items
     */
    public function index(Request $request)
    {
        $q = PengajuanTransaksiHeader::with(['project', 'subKategoriSumberDana'])
            ->orderByDesc('id');

        if ($request->filled('status') && $request->status !== 'semua') {
            $q->where('status', $request->status);
        }

        $items = $q->get();

        return view('transaksi.pengajuan_transaksi.index', compact('items'));
    }

    /**
     * CREATE PENGAJUAN
     * View expects: $projects
     */
    public function createPengajuan()
    {
        $projects = Project::orderBy('nama_project')->get();
        return view('transaksi.pengajuan_transaksi.create_pengajuan', compact('projects'));
    }

    /**
     * STORE PENGAJUAN
     * status: submit
     * bukti: belum ada
     */
    public function storePengajuan(Request $request)
    {
        $request->validate([
            'id_project' => 'required|integer',
            'id_subkategori_sumberdana' => 'required|integer',
            'deskripsi' => 'required|string',
            'estimasi_nominal' => 'required|numeric|min:0',
            'tgl_request' => 'required|date',
            'nama_bank' => 'required|string|max:100',
            'no_rekening' => 'required|string|max:50',
            'metode_pembayaran' => 'nullable|string|max:30',
        ]);

        $trx = new PengajuanTransaksiHeader();
        $trx->no_request = $this->generateNoRequest();
        $trx->id_project = $request->id_project;
        $trx->id_subkategori_sumberdana = $request->id_subkategori_sumberdana;

        $trx->tipe = 'pengajuan';
        $trx->jenis_transaksi = 'pengeluaran';

        $trx->deskripsi = $request->deskripsi;
        $trx->estimasi_nominal = $request->estimasi_nominal;
        $trx->tgl_request = $request->tgl_request;

        $trx->nama_bank = $request->nama_bank;
        $trx->no_rekening = $request->no_rekening;

        // preferensi boleh diisi dari awal, tapi finalnya tetap saat approve
        $trx->metode_pembayaran = $request->metode_pembayaran;

        $trx->status = 'submit';
        $trx->user_id_created = Auth::id();

        $trx->save();

        return redirect()->route('pengajuan_transaksi.index')->with('success', 'Pengajuan dana berhasil dibuat.');
    }

    /**
     * CREATE REIMBURSEMENT
     * View expects: $projects
     */
    public function createReimbursement()
    {
        $projects = Project::orderBy('nama_project')->get();
        return view('transaksi.pengajuan_transaksi.create_reimbursement', compact('projects'));
    }

    /**
     * STORE REIMBURSEMENT
     * status: submit (tapi bukti wajib sudah ada dari awal)
     */
    public function storeReimbursement(Request $request)
    {
        $request->validate([
            'id_project' => 'required|integer',
            'id_subkategori_sumberdana' => 'required|integer',
            'deskripsi' => 'required|string',
            'estimasi_nominal' => 'required|numeric|min:0',
            'tgl_request' => 'required|date',

            'nama_bank' => 'required|string|max:100',
            'no_rekening' => 'required|string|max:50',

            'tgl_bukti' => 'required|date',
            'bukti_file' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
            'metode_pembayaran' => 'nullable|string|max:30',
        ]);

        // upload bukti
        $path = $request->file('bukti_file')->store('pengajuan_transaksi', 'public');

        $trx = new PengajuanTransaksiHeader();
        $trx->no_request = $this->generateNoRequest();
        $trx->id_project = $request->id_project;
        $trx->id_subkategori_sumberdana = $request->id_subkategori_sumberdana;

        $trx->tipe = 'reimbursement';
        $trx->jenis_transaksi = 'pengeluaran';

        $trx->deskripsi = $request->deskripsi;
        $trx->estimasi_nominal = $request->estimasi_nominal;
        $trx->tgl_request = $request->tgl_request;

        $trx->nama_bank = $request->nama_bank;
        $trx->no_rekening = $request->no_rekening;

        $trx->tgl_bukti = $request->tgl_bukti;
        // Reimbursement pakai satu input nominal; simpan juga ke nominal_realisasi biar kompatibel flow existing.
        $trx->nominal_realisasi = $request->estimasi_nominal;
        $trx->bukti_file = $path;

        $trx->metode_pembayaran = $request->metode_pembayaran;

        $trx->status = 'submit';
        $trx->user_id_created = Auth::id();

        $trx->save();

        return redirect()->route('pengajuan_transaksi.index')->with('success', 'Reimbursement berhasil diajukan.');
    }

    /**
     * SHOW DETAIL
     * - otomatis pilih view berdasarkan tipe
     * View expects: $trx
     */
    public function show($id)
    {
        $trx = PengajuanTransaksiHeader::with(['project', 'subKategoriSumberDana'])->findOrFail($id);

        if ($trx->tipe === 'reimbursement') {
            return view('transaksi.pengajuan_transaksi.detail_reimbursement', compact('trx'));
        }

        return view('transaksi.pengajuan_transaksi.detail_pengajuan', compact('trx'));
    }

    /**
     * APPROVE
     * - only from status submit
     * - set tgl_cair, nominal_disetujui, metode_pembayaran
     * - status -> approve
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'tgl_cair' => 'required|date',
            'nominal_disetujui' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:30',
        ]);

        $trx = PengajuanTransaksiHeader::findOrFail($id);

        if ($trx->status !== 'submit') {
            return back()->with('error', 'Status tidak valid untuk approve.');
        }

        $trx->tgl_cair = $request->tgl_cair;
        $trx->nominal_disetujui = $request->nominal_disetujui;
        $trx->metode_pembayaran = $request->metode_pembayaran;

        $trx->status = 'approve';
        $trx->user_id_updated = Auth::id();
        $trx->save();

        return back()->with('success', 'Berhasil approve.');
    }

    /**
     * REJECT
     * - only from status submit
     * - status -> reject
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'keterangan_reject' => 'required|string|max:1000',
        ]);

        $trx = PengajuanTransaksiHeader::findOrFail($id);

        if ($trx->status !== 'submit') {
            return back()->with('error', 'Status tidak valid untuk reject.');
        }

        $trx->status = 'reject';
        $trx->keterangan_reject = $request->keterangan_reject;

        $trx->user_id_updated = Auth::id();
        $trx->save();

        return back()->with('success', 'Berhasil reject.');
    }

    /**
     * UPLOAD BUKTI (khusus pengajuan dana)
     * Flow: approve -> bukti
     */
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'tgl_bukti' => 'required|date',
            'nominal_realisasi' => 'required|numeric|min:0',
            'bukti_file' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $trx = PengajuanTransaksiHeader::findOrFail($id);

        if ($trx->tipe !== 'pengajuan') {
            return back()->with('error', 'Upload bukti hanya untuk pengajuan dana.');
        }

        if ($trx->status !== 'approve') {
            return back()->with('error', 'Status tidak valid untuk upload bukti.');
        }

        // hapus bukti lama jika ada
        if (!empty($trx->bukti_file) && Storage::disk('public')->exists($trx->bukti_file)) {
            Storage::disk('public')->delete($trx->bukti_file);
        }

        $path = $request->file('bukti_file')->store('pengajuan_transaksi', 'public');

        $trx->tgl_bukti = $request->tgl_bukti;
        $trx->nominal_realisasi = $request->nominal_realisasi;
        $trx->bukti_file = $path;

        $trx->status = 'bukti';
        $trx->user_id_updated = Auth::id();
        $trx->save();

        return back()->with('success', 'Bukti berhasil diupload.');
    }

    /**
     * FINALIZE
     * - pengajuan: bukti -> done
     * - reimbursement: approve -> done
     * - on done: create pencatatan_keuangan & set pencatatan_keuangan_id
     */
public function finalize($id)
{
    DB::beginTransaction();

    try {
        $trx = PengajuanTransaksiHeader::with(['project', 'subKategoriSumberDana'])
            ->lockForUpdate()
            ->findOrFail($id);

        // ✅ kalau sudah pernah finalize, stop (anti dobel)
        if (!empty($trx->pencatatan_keuangan_id) || $trx->status === 'done') {
            if ($trx->status !== 'done') {
                $trx->status = 'done';
                $trx->user_id_updated = Auth::id();
                $trx->save();
            }
            DB::commit();
            return back()->with('success', 'Transaksi sudah FINALIZED.');
        }

        // ✅ validasi flow
        if ($trx->tipe === 'pengajuan') {
            if ($trx->status !== 'bukti') {
                DB::rollBack();
                return back()->with('error', 'Pengajuan dana hanya bisa finalize dari status BUKTI.');
            }
        } else { // reimbursement
            if ($trx->status !== 'approve') {
                DB::rollBack();
                return back()->with('error', 'Reimbursement hanya bisa finalize dari status APPROVE.');
            }
            if (empty($trx->bukti_file)) {
                DB::rollBack();
                return back()->with('error', 'Bukti reimbursement tidak ditemukan.');
            }
        }

        // ✅ CEK: sudah ada ledger dengan marker ini? (anti dobel keras)
        $marker = "[REQTRX#{$trx->no_request}]";
        $existingLedger = PencatatanKeuangan::where('deskripsi_transaksi', 'like', "%{$marker}%")
            ->orderByDesc('id')
            ->first();

        if ($existingLedger) {
            $trx->pencatatan_keuangan_id = $existingLedger->id;
            $trx->status = 'done';
            $trx->user_id_updated = Auth::id();
            $trx->save();

            DB::commit();
            return back()->with('success', 'Ledger sudah ada. Status di-set DONE.');
        }

        // ✅ CREATE PENCATATAN KEUANGAN
        $tanggal = $trx->tgl_bukti ?? $trx->tgl_request;

        $jumlah  = $trx->nominal_realisasi
                ?? $trx->nominal_disetujui
                ?? $trx->estimasi_nominal
                ?? 0;

        $deskFinal = "{$marker} " . ($trx->deskripsi ?? '');

        $ledger = new PencatatanKeuangan();
        $ledger->tanggal = $tanggal;
        $ledger->project_id = $trx->id_project;
        $ledger->sub_kategori_pendanaan = $trx->id_subkategori_sumberdana;
        $ledger->jenis_transaksi = $trx->jenis_transaksi ?? 'pengeluaran';
        $ledger->jumlah_transaksi = $jumlah;
        $ledger->metode_pembayaran = $trx->metode_pembayaran;
        $ledger->bukti_transaksi = $trx->bukti_file;
        $ledger->deskripsi_transaksi = $deskFinal;
        $ledger->request_pembelian_id = null;
        $ledger->save();

        // ✅ INI YANG KAMU LUPA: UPDATE HEADER
        $trx->pencatatan_keuangan_id = $ledger->id;
        $trx->status = 'done';
        $trx->user_id_updated = Auth::id();
        $trx->save();

        DB::commit();
        return back()->with('success', 'Finalize berhasil. Transaksi masuk ke Pencatatan Keuangan (DONE).');

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal finalize: ' . $e->getMessage());
    }
}

    /**
     * JSON: subkategori by project
     * Dipakai di create_pengajuan & create_reimbursement
     *
     * IMPORTANT:
     * kamu sebelumnya punya query via detail_subkategori join subkategori_sumberdana
     * aku bikin versi aman: pakai DB query langsung biar sesuai struktur kamu.
     */
    public function subkategori($projectId)
    {
        $data = DB::table('detail_subkategori as a')
            ->join('subkategori_sumberdana as b', 'a.id_subkategori_sumberdana', '=', 'b.id')
            ->where('a.id_project', $projectId)
            ->select('b.id', 'b.nama')
            ->orderBy('b.nama')
            ->get();

        return response()->json($data);
    }

    /**
     * Generate no_request: REQTRX-YYYYMMDD-XXXX
     */
    private function generateNoRequest(): string
    {
        $prefix = 'REQTRX-' . now()->format('Ymd') . '-';

        $last = PengajuanTransaksiHeader::where('no_request', 'like', $prefix . '%')
            ->orderByDesc('no_request')
            ->value('no_request');

        $next = 1;
        if ($last) {
            $parts = explode('-', $last);
            $num = (int) end($parts);
            $next = $num + 1;
        }

        return $prefix . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }
}
