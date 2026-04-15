<?php

namespace App\Http\Controllers;

use App\Models\PengajuanTransaksiHeader;
use App\Models\Project;
use App\Models\SubkategoriSumberdana;
use App\Models\DetailSubkategori;
use App\Models\PencatatanKeuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengajuanTransaksiController extends Controller
{
    /* ─── LIST ────────────────────────────────────────────── */
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

    /* ─── CREATE PENGAJUAN ───────────────────────────────── */
    public function createPengajuan()
    {
        $projects = $this->getProjectsForUser();
        return view('transaksi.pengajuan_transaksi.create_pengajuan', compact('projects'));
    }

    /* ─── STORE PENGAJUAN ────────────────────────────────── */
    public function storePengajuan(Request $request)
    {
        $request->validate([
            'id_project'                => 'required|integer',
            'id_subkategori_sumberdana' => 'required|integer',
            'deskripsi'                 => 'required|string',
            'kuantitas'                 => 'required|integer|min:1',
            'harga_satuan'              => 'required|numeric|min:0',
            'tgl_request'               => 'required|date',
            'nama_bank'                 => 'required|string|max:100',
            'no_rekening'               => 'required|string|max:50',
            'metode_pembayaran'         => 'nullable|string|max:30',
        ]);

        $kuantitas   = (int) $request->kuantitas;
        $hargaSatuan = (float) $request->harga_satuan;

        $trx = new PengajuanTransaksiHeader();
        $trx->no_request                = $this->generateNoRequest();
        $trx->id_project                = $request->id_project;
        $trx->id_subkategori_sumberdana = $request->id_subkategori_sumberdana;
        $trx->tipe                      = 'pengajuan';
        $trx->jenis_transaksi           = 'pengeluaran';
        $trx->deskripsi                 = $request->deskripsi;
        $trx->kuantitas                 = $kuantitas;
        $trx->harga_satuan              = $hargaSatuan;
        $trx->estimasi_nominal          = $kuantitas * $hargaSatuan;
        $trx->tgl_request               = $request->tgl_request;
        $trx->nama_bank                 = $request->nama_bank;
        $trx->no_rekening               = $request->no_rekening;
        $trx->metode_pembayaran         = $request->metode_pembayaran;
        $trx->status                    = 'submit';
        $trx->user_id_created           = Auth::id();
        $trx->save();

        return redirect()->route('pengajuan_transaksi.index')->with('success', 'Pengajuan dana berhasil dibuat.');
    }

    /* ─── CREATE REIMBURSEMENT ───────────────────────────── */
    public function createReimbursement()
    {
        $projects = $this->getProjectsForUser();
        return view('transaksi.pengajuan_transaksi.create_reimbursement', compact('projects'));
    }

    /* ─── STORE REIMBURSEMENT ────────────────────────────── */
    public function storeReimbursement(Request $request)
    {
        $request->validate([
            'id_project'                => 'required|integer',
            'id_subkategori_sumberdana' => 'required|integer',
            'deskripsi'                 => 'required|string',
            'kuantitas'                 => 'required|integer|min:1',
            'harga_satuan'              => 'required|numeric|min:0',
            'tgl_request'               => 'required|date',
            'nama_bank'                 => 'required|string|max:100',
            'no_rekening'               => 'required|string|max:50',
            'tgl_bukti'                 => 'required|date',
            'bukti_file'                => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
            'metode_pembayaran'         => 'nullable|string|max:30',
        ]);

        $kuantitas   = (int) $request->kuantitas;
        $hargaSatuan = (float) $request->harga_satuan;
        $path = $request->file('bukti_file')->store('pengajuan_transaksi', 'public');

        $trx = new PengajuanTransaksiHeader();
        $trx->no_request                = $this->generateNoRequest();
        $trx->id_project                = $request->id_project;
        $trx->id_subkategori_sumberdana = $request->id_subkategori_sumberdana;
        $trx->tipe                      = 'reimbursement';
        $trx->jenis_transaksi           = 'pengeluaran';
        $trx->deskripsi                 = $request->deskripsi;
        $trx->kuantitas                 = $kuantitas;
        $trx->harga_satuan              = $hargaSatuan;
        $trx->estimasi_nominal          = $kuantitas * $hargaSatuan;
        $trx->tgl_request               = $request->tgl_request;
        $trx->nama_bank                 = $request->nama_bank;
        $trx->no_rekening               = $request->no_rekening;
        $trx->tgl_bukti                 = $request->tgl_bukti;
        $trx->nominal_realisasi         = $kuantitas * $hargaSatuan;
        $trx->bukti_file                = $path;
        $trx->metode_pembayaran         = $request->metode_pembayaran;
        $trx->status                    = 'submit';
        $trx->user_id_created           = Auth::id();
        $trx->save();

        return redirect()->route('pengajuan_transaksi.index')->with('success', 'Reimbursement berhasil diajukan.');
    }

    /* ─── SHOW DETAIL ────────────────────────────────────── */
    public function show($id)
    {
        $trx = PengajuanTransaksiHeader::with(['project', 'subKategoriSumberDana'])->findOrFail($id);

        if ($trx->tipe === 'reimbursement') {
            return view('transaksi.pengajuan_transaksi.detail_reimbursement', compact('trx'));
        }

        return view('transaksi.pengajuan_transaksi.detail_pengajuan', compact('trx'));
    }

    /* ─── APPROVE ────────────────────────────────────────── */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'tgl_cair'           => 'required|date',
            'nominal_final'      => 'required|numeric|min:0',
            'biaya_admin'        => 'nullable|numeric|min:0',
            'metode_pembayaran'  => 'required|string|max:30',
            'bukti_transfer'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'is_talangan'        => 'nullable',
        ]);

        $trx = PengajuanTransaksiHeader::findOrFail($id);

        if ($trx->status !== 'submit') {
            return back()->with('error', 'Status tidak valid untuk approve.');
        }

        // Upload bukti transfer jika ada
        if ($request->hasFile('bukti_transfer')) {
            $path = $request->file('bukti_transfer')->store('pengajuan_transaksi/bukti_transfer', 'public');
            $trx->bukti_transfer = $path;
        }

        $trx->tgl_cair          = $request->tgl_cair;
        $trx->nominal_disetujui = $request->nominal_final; // backward compat
        $trx->nominal_final     = $request->nominal_final;
        $trx->biaya_admin       = $request->biaya_admin ?? 0;
        $trx->metode_pembayaran = $request->metode_pembayaran;
        $trx->is_talangan       = $request->boolean('is_talangan');
        $trx->user_id_updated   = Auth::id();

        // ✅ Reimbursement: auto-finalize karena bukti sudah ada dari awal
        if ($trx->tipe === 'reimbursement') {
            $trx->status = 'done';

            if ($trx->is_talangan && empty($trx->status_alokasi)) {
                $trx->status_alokasi = 'belum';
            }

            $trx->save();

            // Create pencatatan keuangan
            $this->createLedgerEntries($trx);

            return back()->with('success', 'Reimbursement disetujui & diselesaikan. Masuk ke Pencatatan Keuangan.');
        }

        // Pengajuan Dana: approve saja, tunggu upload nota dari pengaju
        $trx->status = 'approve';

        if ($trx->is_talangan && empty($trx->status_alokasi)) {
            $trx->status_alokasi = 'belum';
        }

        $trx->save();

        return back()->with('success', 'Pengajuan disetujui. Menunggu upload bukti/nota dari pengaju.');
    }

    /* ─── REJECT ─────────────────────────────────────────── */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'keterangan_reject' => 'required|string|max:1000',
        ]);

        $trx = PengajuanTransaksiHeader::findOrFail($id);

        if ($trx->status !== 'submit') {
            return back()->with('error', 'Status tidak valid untuk reject.');
        }

        $trx->status            = 'reject';
        $trx->keterangan_reject = $request->keterangan_reject;
        $trx->user_id_updated   = Auth::id();
        $trx->save();

        return back()->with('success', 'Pengajuan ditolak.');
    }

    /* ─── UPLOAD BUKTI (khusus pengajuan dana) ───────────── */
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'tgl_bukti'         => 'required|date',
            'nominal_realisasi' => 'required|numeric|min:0',
            'bukti_file'        => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $trx = PengajuanTransaksiHeader::findOrFail($id);

        if ($trx->tipe !== 'pengajuan') {
            return back()->with('error', 'Upload bukti hanya untuk pengajuan dana.');
        }

        if ($trx->status !== 'approve') {
            return back()->with('error', 'Status tidak valid untuk upload bukti.');
        }

        // Hapus bukti lama jika ada
        if (!empty($trx->bukti_file) && Storage::disk('public')->exists($trx->bukti_file)) {
            Storage::disk('public')->delete($trx->bukti_file);
        }

        $path = $request->file('bukti_file')->store('pengajuan_transaksi', 'public');

        $trx->tgl_bukti         = $request->tgl_bukti;
        $trx->nominal_realisasi = $request->nominal_realisasi;
        $trx->bukti_file        = $path;
        $trx->status            = 'bukti';
        $trx->user_id_updated   = Auth::id();
        $trx->save();

        return back()->with('success', 'Bukti berhasil diupload.');
    }

    /* ─── FINALIZE ───────────────────────────────────────── */
    public function finalize($id)
    {
        DB::beginTransaction();

        try {
            $trx = PengajuanTransaksiHeader::with(['project', 'subKategoriSumberDana'])
                ->lockForUpdate()
                ->findOrFail($id);

            // Anti dobel
            if (!empty($trx->pencatatan_keuangan_id) || $trx->status === 'done') {
                if ($trx->status !== 'done') {
                    $trx->status = 'done';
                    $trx->user_id_updated = Auth::id();
                    $trx->save();
                }
                DB::commit();
                return back()->with('success', 'Transaksi sudah FINALIZED.');
            }

            // Validasi flow
            if ($trx->tipe === 'pengajuan') {
                if ($trx->status !== 'bukti') {
                    DB::rollBack();
                    return back()->with('error', 'Pengajuan dana hanya bisa finalize dari status BUKTI.');
                }
            } else {
                // Reimbursement sudah auto-finalize di approve, tapi jaga-jaga
                if (!in_array($trx->status, ['approve', 'done'])) {
                    DB::rollBack();
                    return back()->with('error', 'Reimbursement hanya bisa finalize dari status APPROVE.');
                }
            }

            $trx->status = 'done';
            $trx->user_id_updated = Auth::id();
            $trx->save();

            $this->createLedgerEntries($trx);

            DB::commit();
            return back()->with('success', 'Finalize berhasil. Transaksi masuk ke Pencatatan Keuangan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal finalize: ' . $e->getMessage());
        }
    }

    /* ─── CREATE LEDGER ENTRIES ───────────────────────────── */
    private function createLedgerEntries(PengajuanTransaksiHeader $trx): void
    {
        // Cek sudah ada entry?
        $existing = PencatatanKeuangan::where('deskripsi_transaksi', 'like', "%[REQTRX#{$trx->no_request}]%")
            ->exists();

        if ($existing) {
            // Link saja jika belum
            if (empty($trx->pencatatan_keuangan_id)) {
                $ledgerId = PencatatanKeuangan::where('deskripsi_transaksi', 'like', "%[REQTRX#{$trx->no_request}]%")
                    ->value('id');
                $trx->pencatatan_keuangan_id = $ledgerId;
                $trx->save();
            }
            return;
        }

        $tanggal = $trx->tgl_bukti ?? $trx->tgl_cair ?? $trx->tgl_request;

        // Nominal: pakai nominal_final dari admin, fallback ke estimasi
        $jumlah = $trx->nominal_final
            ?? $trx->nominal_realisasi
            ?? $trx->nominal_disetujui
            ?? $trx->estimasi_nominal
            ?? 0;

        $deskripsi = $trx->deskripsi ?? '';

        $hasTalanganColumns = Schema::hasColumn('pencatatan_keuangan', 'is_talangan');

        // Entry utama
        $ledgerData = [
            'tanggal'                => $tanggal,
            'project_id'             => $trx->id_project,
            'sub_kategori_pendanaan' => $trx->id_subkategori_sumberdana,
            'jenis_transaksi'        => $trx->jenis_transaksi ?? 'pengeluaran',
            'deskripsi_transaksi'    => "[REQTRX#{$trx->no_request}] " . $deskripsi,
            'jumlah_transaksi'       => $jumlah,
            'metode_pembayaran'      => $trx->metode_pembayaran,
            'bukti_transaksi'        => $trx->bukti_file ?? $trx->bukti_transfer ?? null,
            'request_pembelian_id'   => null,
        ];

        if ($hasTalanganColumns) {
            $ledgerData['is_talangan']       = (bool) ($trx->is_talangan ?? false);
            $ledgerData['talangan_ref_type']  = (bool) ($trx->is_talangan ?? false) ? 'pengajuan_transaksi' : null;
            $ledgerData['talangan_ref_id']    = (bool) ($trx->is_talangan ?? false) ? $trx->id : null;
            $ledgerData['is_reclass']         = false;
            $ledgerData['reclass_group_id']   = null;
        }

        $ledger = PencatatanKeuangan::create($ledgerData);

        // Update realisasi anggaran
        if ($trx->id_subkategori_sumberdana) {
            $detailSubkategori = DetailSubkategori::where('id_subkategori_sumberdana', $trx->id_subkategori_sumberdana)
                ->where('id_project', $trx->id_project)
                ->first();

            if ($detailSubkategori) {
                $detailSubkategori->realisasi_anggaran = ($detailSubkategori->realisasi_anggaran ?? 0) + $jumlah;
                $detailSubkategori->save();
            }
        }

        // Entry biaya admin (jika > 0)
        $biayaAdmin = (float) ($trx->biaya_admin ?? 0);
        if ($biayaAdmin > 0) {
            $adminData = [
                'tanggal'                => $tanggal,
                'project_id'             => $trx->id_project,
                'sub_kategori_pendanaan' => null,
                'jenis_transaksi'        => 'pengeluaran',
                'deskripsi_transaksi'    => "[REQTRX#{$trx->no_request}] Biaya Admin Transfer",
                'jumlah_transaksi'       => $biayaAdmin,
                'metode_pembayaran'      => $trx->metode_pembayaran,
                'bukti_transaksi'        => $trx->bukti_transfer ?? null,
                'request_pembelian_id'   => null,
            ];

            if ($hasTalanganColumns) {
                $adminData['is_talangan']       = (bool) ($trx->is_talangan ?? false);
                $adminData['talangan_ref_type']  = (bool) ($trx->is_talangan ?? false) ? 'pengajuan_transaksi' : null;
                $adminData['talangan_ref_id']    = (bool) ($trx->is_talangan ?? false) ? $trx->id : null;
                $adminData['is_reclass']         = false;
                $adminData['reclass_group_id']   = null;
            }

            PencatatanKeuangan::create($adminData);
        }

        // Link ledger ID
        $trx->pencatatan_keuangan_id = $ledger->id;
        $trx->save();
    }

    /* ─── SUBKATEGORI JSON ───────────────────────────────── */
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

    /* ─── HELPER: Get projects for current user ──────────── */
    private function getProjectsForUser()
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'bendahara'])) {
            return Project::query()
                ->where('status', 'aktif')
                ->orderByDesc('tahun')
                ->orderBy('nama_project')
                ->get(['id', 'nama_project']);
        }

        // Peneliti: hanya project yang dia tergabung
        return Project::query()
            ->where('status', 'aktif')
            ->whereIn('id', function ($sub) use ($user) {
                $sub->select('id_project')
                    ->from('detail_project')
                    ->where('id_user', $user->id);
            })
            ->orderByDesc('tahun')
            ->orderBy('nama_project')
            ->get(['id', 'nama_project']);
    }

    /* ─── HELPER: Generate no_request ────────────────────── */
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

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
