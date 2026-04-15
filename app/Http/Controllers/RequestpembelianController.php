<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RequestpembelianDetail;
use App\Models\RequestpembelianHeader;
use App\Models\DetailSubkategori;
use App\Models\PencatatanKeuangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RequestpembelianController extends Controller
{
    public function index()
    {
        $hasTalanganHeaderCols = Schema::hasColumn('request_pembelian_header', 'is_talangan')
            && Schema::hasColumn('request_pembelian_header', 'status_alokasi');

        $selects = ['a.id', 'a.no_request', 'b.nama_project', 'c.nama_barang', 'c.total_harga', 'a.status_request'];
        if ($hasTalanganHeaderCols) {
            $selects[] = 'a.is_talangan';
            $selects[] = 'a.status_alokasi';
        }

        $q = DB::table('request_pembelian_header as a')
            ->leftJoin('project as b', 'a.id_project', '=', 'b.id')
            ->leftJoin(DB::raw("(SELECT id_request_pembelian_header, GROUP_CONCAT(CONCAT(kuantitas, ' x ', nama_barang)) as nama_barang, SUM(harga * kuantitas) as total_harga FROM request_pembelian_detail GROUP BY id_request_pembelian_header) as c"), 'a.id', '=', 'c.id_request_pembelian_header')
            ->select($selects);

        // ✅ kalau bukan admin, tampilkan hanya yang dibuat user tersebut
        // ✅ hanya PENELITI yang dibatasi data milik sendiri
        if (Auth::user()->role === 'peneliti') {
            $q->where('a.user_id_created', Auth::id());
        } else {
            // ✅ Admin/Bendahara JANGAN lihat yang masih draft
            $q->where('a.status_request', '!=', 'draft');
        }

        $request_pembelian = $q->get();

        return view('requestpembelian.index', ['request_pembelian' => $request_pembelian]);
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $project = \App\Models\Project::query()
                ->where('status', 'aktif')
                ->orderByDesc('tahun')
                ->orderBy('nama_project')
                ->get(['id', 'nama_project']);
        } else {
            // ✅ Peneliti: hanya project AKTIF yang dia tergabung (detail_project)
            $project = \App\Models\Project::query()
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

        return view('requestpembelian.create', compact('project'));
    }

    public function track()
    {
        if (Auth::user()->role === 'peneliti') {
            return redirect()->route('requestpembelian.index')
                ->with('info', 'Tracking paket komponen bisa dilihat di halaman detail pengajuan.');
        }

        $q = DB::table('request_pembelian_detail as d')
            ->join('request_pembelian_header as h', 'd.id_request_pembelian_header', '=', 'h.id')
            ->leftJoin('project as p', 'h.id_project', '=', 'p.id')
            ->select(
                'd.id as detail_id',
                'h.id',
                'h.no_request',
                'h.tgl_request',
                'h.status_request',
                'h.updated_at',
                'p.nama_project',
                'd.nama_barang',
                'd.kuantitas',
                'd.harga',
                DB::raw('(COALESCE(d.kuantitas,0) * COALESCE(d.harga,0)) as total_perkiraan'),
                DB::raw('COALESCE(d.total_invoice,0) as total_invoice'),
                DB::raw('COALESCE(d.is_sampai,0) as is_sampai'),
                DB::raw('COALESCE(d.is_pelaporan,0) as is_pelaporan')
            )
            ->whereIn('h.status_request', ['approve_request', 'submit_payment', 'approve_payment', 'done'])
            ->orderByDesc('h.id')
            ->orderBy('d.id');

        $tracks = $q->get();

        return view('requestpembelian.track', compact('tracks'));
    }

    public function markSampai(string $id)
    {
        if (!in_array(Auth::user()->role, ['admin', 'bendahara'], true)) {
            return back()->with('error', 'Hanya admin/bendahara yang bisa update status sampai.');
        }

        $detail = RequestpembelianDetail::findOrFail($id);
        $detail->is_sampai = 1;
        $detail->user_id_updated = Auth::id();
        $detail->save();

        return back()->with('success', 'Status item berhasil ditandai sudah sampai.');
    }

    public function markPelaporan(string $id)
    {
        if (!in_array(Auth::user()->role, ['admin', 'bendahara'], true)) {
            return back()->with('error', 'Hanya admin/bendahara yang bisa update status pelaporan.');
        }

        $detail = RequestpembelianDetail::findOrFail($id);
        if (!(bool) $detail->is_sampai) {
            return back()->with('error', 'Item harus ditandai sudah sampai dulu sebelum pelaporan.');
        }

        $detail->is_pelaporan = 1;
        $detail->user_id_updated = Auth::id();
        $detail->save();

        return back()->with('success', 'Status item berhasil ditandai sudah pelaporan.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_request' => 'required|date',
            'id_project'  => 'required|integer',
        ]);

        try {
            $user = Auth::user();

            // ✅ 1) Pastikan project ada & masih AKTIF
            $project = \App\Models\Project::where('id', $request->id_project)
                ->where('status', 'aktif')
                ->first();

            if (!$project) {
                return back()
                    ->with('error', 'Project sudah ditutup / tidak valid.')
                    ->withInput();
            }

            // ✅ 2) Kalau PENELITI: wajib tergabung di project (detail_project)
            if ($user->role !== 'admin') {
                $isMember = \DB::table('detail_project')
                    ->where('id_project', $request->id_project)
                    ->where('id_user', $user->id)
                    ->exists();

                if (!$isMember) {
                    return back()
                        ->with('error', 'Anda tidak tergabung pada project tersebut.')
                        ->withInput();
                }
            }

            // ✅ 3) Buat Request Pembelian Header
            $request_pembelian = RequestpembelianHeader::create([
                'no_request'      => 'REQ' . now()->format('YmdHis'),
                'tgl_request'     => $request->tgl_request,
                'id_project'      => $request->id_project,
                'status_request'  => 'draft',
                'user_id_created' => $user->id,
                'user_id_updated' => $user->id,
            ]);

            return redirect()
                ->route('requestpembelian.detail', $request_pembelian->id)
                ->with('success', 'Request Pembelian berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()
                ->route('requestpembelian.index')
                ->with('error', 'Request Pembelian gagal dibuat');
        }
    }

    public function edit(string $id)
    {
        $request_pembelian = RequestpembelianHeader::findOrFail($id);
        $detail            = RequestpembelianDetail::where('id_request_pembelian_header', $id)->get();
        $project           = Project::all();

        return view('requestpembelian.edit', ['request_pembelian' => $request_pembelian, 'detail' => $detail, 'project' => $project]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'tgl_request' => 'required',
            'id_project'  => 'required',
        ]);

        try {
            RequestpembelianHeader::where('id', $id)->update([
                'tgl_request'     => $request->tgl_request,
                'id_project'      => $request->id_project,
                'user_id_updated' => Auth::user()->id,
                'updated_at'      => now(),
            ]);

            return redirect()->route('requestpembelian.detail', $id)->with('success', 'Request Pembelian berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('requestpembelian.detail', $id)->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            // Hapus detail terkait sebelum menghapus header
            RequestpembelianDetail::where('id_request_pembelian_header', $id)->delete();

            // Hapus header request pembelian
            RequestpembelianHeader::destroy($id);

            return redirect()->route('requestpembelian.index')->with('success', 'Request Pembelian berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('requestpembelian.index')->with('error', 'Request Pembelian gagal dihapus: ' . $e->getMessage());
        }
    }

    public function detail(string $id)
    {
        $request_pembelian = RequestpembelianHeader::findOrFail($id);

        // ✅ kalau bukan admin, cuma boleh buka detail miliknya sendiri
        if (!in_array(Auth::user()->role, ['admin','bendahara'])
            && $request_pembelian->user_id_created != Auth::id()) {
            abort(403, 'Akses ditolak');
        }


        $detail  = RequestpembelianDetail::where('id_request_pembelian_header', $id)->get();
        $project = Project::all();

        $projectId = $request_pembelian->id_project;

        $subkategori = DB::table('detail_subkategori as a')
            ->join('subkategori_sumberdana as b', 'a.id_subkategori_sumberdana', '=', 'b.id')
            ->where('a.id_project', $projectId)
            ->select('b.id', 'b.nama', 'a.nominal', 'a.realisasi_anggaran')
            ->get();

        return view('requestpembelian.detail', [
            'request_pembelian' => $request_pembelian,
            'detail' => $detail,
            'project' => $project,
            'subkategori' => $subkategori
        ]);
    }

    public function storedetail(Request $request)
    {
        $validated = $request->validate([
            'nama_barang'    => 'required|string|max:255',
            'kuantitas'      => 'required|numeric|min:1',
            'harga'          => 'required|numeric|min:0',
            'link_pembelian' => 'required|url',
            'id_request_pembelian_header' => 'required|exists:request_pembelian_header,id',
            'id_subkategori_sumberdana' => 'nullable|exists:subkategori_sumberdana,id',
        ]);

        try {
            RequestpembelianDetail::create([
                'nama_barang'                 => $validated['nama_barang'],
                'kuantitas'                   => $validated['kuantitas'],
                'harga'                       => $validated['harga'],
                'link_pembelian'              => $validated['link_pembelian'],
                'id_request_pembelian_header' => $validated['id_request_pembelian_header'],
                'id_subkategori_sumberdana'   => $request->id_subkategori_sumberdana,
                'user_id_created'             => Auth::id(),
                'user_id_updated'             => Auth::id(),
            ]);

            return redirect()
                ->route('requestpembelian.detail', $validated['id_request_pembelian_header'])
                ->with('success', 'Detail Request Pembelian berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()
                ->route('requestpembelian.detail', $validated['id_request_pembelian_header'])
                ->with('error', 'Detail Request Pembelian gagal dibuat: ' . $e->getMessage());
        }
    }

    public function addbukti(string $id)
    {
        $detail = RequestpembelianDetail::find($id);
        return view('requestpembelian.addbukti', ['detail' => $detail]);
    }

    // Upload invoice per item (oleh admin/bendahara)
    public function storebukti(Request $request, string $id)
    {
        $request->validate([
            'id_request_pembelian_header' => 'required|exists:request_pembelian_header,id',
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $headerId = $request->id_request_pembelian_header;
        $header   = RequestpembelianHeader::findOrFail($headerId);

        if (!in_array(Auth::user()->role, ['admin', 'bendahara'], true)) {
            return redirect()
                ->route('requestpembelian.detail', $headerId)
                ->with('error', 'Hanya admin/bendahara yang bisa mengunggah invoice.');
        }

        if ($header->status_request === 'reject_request') {
            return redirect()
                ->route('requestpembelian.detail', $headerId)
                ->with('error', 'Upload invoice tidak tersedia saat status ditolak.');
        }

        try {
            $detail = RequestpembelianDetail::findOrFail($id);

            if (!empty($detail->invoice_pembelian) && Storage::disk('public')->exists($detail->invoice_pembelian)) {
                Storage::disk('public')->delete($detail->invoice_pembelian);
            }

            $path = $request->file('bukti_bayar')->store('request_pembelian/invoice_item', 'public');

            RequestpembelianDetail::where('id', $id)->update([
                'invoice_pembelian' => $path,
                'user_id_updated' => Auth::id(),
                'updated_at'      => now(),
            ]);

            return redirect()
                ->route('requestpembelian.detail', $headerId)
                ->with('success', 'Invoice item berhasil diunggah.');

        } catch (\Exception $e) {
            return redirect()
                ->route('requestpembelian.detail', $headerId)
                ->with('error', 'Invoice item gagal diunggah: ' . $e->getMessage());
        }
    }

    public function editdetail(string $id)
    {
        $detail = RequestpembelianDetail::find($id);

        $header = RequestpembelianHeader::find($detail->id_request_pembelian_header);

        $subkategori = DB::table('detail_subkategori as a')
            ->join('subkategori_sumberdana as b', 'a.id_subkategori_sumberdana', '=', 'b.id')
            ->where('a.id_project', $header->id_project)
            ->select('b.id', 'b.nama', 'a.nominal', 'a.realisasi_anggaran')
            ->get();

        return view('requestpembelian.editdetail', [
            'detail' => $detail,
            'subkategori' => $subkategori,
            'header' => $header
        ]);
    }

    public function updatedetail(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama_barang'    => 'required',
            'kuantitas'      => 'required',
            'harga'          => 'required',
            'link_pembelian' => 'required',
            'id_subkategori_sumberdana' => 'nullable|exists:subkategori_sumberdana,id',
        ]);

        try {
            $detail = RequestpembelianDetail::find($id);
            $filename_buktibayar = $detail->bukti_bayar;

            if ($request->hasFile('bukti_bayar')) {
                if ($detail->bukti_bayar && File::exists('bukti_bayar/' . $detail->bukti_bayar)) {
                    File::delete('bukti_bayar/' . $detail->bukti_bayar);
                }

                $bukti_bayar         = $request->file('bukti_bayar');
                $filename_buktibayar = time() . '_' . $id . '.' . $bukti_bayar->getClientOriginalExtension();
                $bukti_bayar->move('bukti_bayar', $filename_buktibayar);
            }

            RequestpembelianDetail::where('id', $id)->update([
                'nama_barang'     => $request->nama_barang,
                'kuantitas'       => $request->kuantitas,
                'harga'           => $request->harga,
                'link_pembelian'  => $request->link_pembelian,
                'bukti_bayar'     => $filename_buktibayar,
                'id_subkategori_sumberdana' => $request->id_subkategori_sumberdana,
                'user_id_updated' => Auth::user()->id,
                'updated_at'      => now(),
            ]);

            // ✅ kalau upload bukti lewat edit, cek semua item -> submit_payment
            if ($request->hasFile('bukti_bayar')) {
                $header = RequestpembelianHeader::find($detail->id_request_pembelian_header);

                if ($header && in_array($header->status_request, ['approve_request', 'reject_payment', 'submit_payment'])) {
                    $totalItem = RequestpembelianDetail::where('id_request_pembelian_header', $header->id)->count();
                    $uploaded  = RequestpembelianDetail::where('id_request_pembelian_header', $header->id)
                        ->whereNotNull('bukti_bayar')
                        ->where('bukti_bayar', '!=', '')
                        ->count();

                    if ($totalItem > 0 && $uploaded === $totalItem) {
                        $header->status_request    = 'submit_payment';
                        $header->keterangan_reject = null;
                        $header->user_id_updated   = Auth::id();
                        $header->updated_at        = now();
                        $header->save();
                    }
                }
            }

            return redirect()->route('requestpembelian.detail', $request->id_request_pembelian_header)->with('success', 'Detail Request Pembelian berhasil diubah');
        } catch (\Exception) {
            return redirect()->route('requestpembelian.detail', $request->id_request_pembelian_header)->with('error', 'Detail Request Pembelian gagal diubah');
        }
    }

    public function destroydetail(string $id)
    {
        $detail = RequestpembelianDetail::find($id);

        try {
            if ($detail->bukti_bayar && File::exists('bukti_bayar/' . $detail->bukti_bayar)) {
                File::delete('bukti_bayar/' . $detail->bukti_bayar);
            }
            $detail->delete();

            return redirect()->route('requestpembelian.detail', $detail->id_request_pembelian_header)->with('success', 'Detail Request Pembelian berhasil dihapus');
        } catch (\Exception) {
            return redirect()->route('requestpembelian.detail', $detail->id_request_pembelian_header)->with('error', 'Detail Request Pembelian gagal dihapus');
        }
    }

    public function changestatus(Request $request)
    {
        Log::info('Fungsi changestatus dipanggil');

        $validated = $request->validate([
            'status_request' => 'required',
            'id_request_pembelian_header' => 'required|exists:request_pembelian_header,id',
            'is_talangan' => 'nullable|boolean',
            'keterangan_reject' => 'nullable|string|max:500',
            'biaya_admin_transfer' => 'nullable|numeric|min:0',
            'nominal_final_total' => 'nullable|numeric|min:0',
            'nominal_penambahan' => 'nullable|numeric|min:0',
            'nominal_pengurangan' => 'nullable|numeric|min:0',
            'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        Log::info('Status request yang diterima: ' . $request->status_request);

        try {
            $header = RequestpembelianHeader::findOrFail($validated['id_request_pembelian_header']);

            if ($request->status_request == 'approve_payment') {
                Log::info('ID Request Pembelian: ' . $header->id);

                if ($request->hasFile('bukti_transfer')) {
                    if (!empty($header->bukti_transfer) && Storage::disk('public')->exists($header->bukti_transfer)) {
                        Storage::disk('public')->delete($header->bukti_transfer);
                    }
                    $header->bukti_transfer = $request->file('bukti_transfer')->store('request_pembelian/bukti_transfer', 'public');
                }

                $header->biaya_admin_transfer = (float)($request->biaya_admin_transfer ?? 0);
                $hasTalanganHeaderCols = Schema::hasColumn('request_pembelian_header', 'is_talangan');
                if ($hasTalanganHeaderCols && $request->has('is_talangan')) {
                    $header->is_talangan = (bool) $request->boolean('is_talangan');
                    if (Schema::hasColumn('request_pembelian_header', 'status_alokasi') && $header->is_talangan && empty($header->status_alokasi)) {
                        $header->status_alokasi = 'belum';
                    }
                }
                $header->nominal_final_total = $request->filled('nominal_final_total') ? (float)$request->nominal_final_total : null;
                $header->nominal_penambahan = (float)($request->nominal_penambahan ?? 0);
                $header->nominal_pengurangan = (float)($request->nominal_pengurangan ?? 0);
                $header->keterangan_penambahan = null;
                $header->keterangan_pengurangan = null;

                // Ubah status jadi done
                $header->status_request = 'done';
                $header->keterangan_reject = null;
                $header->user_id_updated = Auth::user()->id;
                $header->updated_at = now();
                $header->save();

                // Cegah duplikasi transaksi
                $existing = PencatatanKeuangan::where('request_pembelian_id', $header->id)->exists();
                if (!$existing) {
                    Log::info('Membuat pencatatan keuangan baru untuk request ID: ' . $header->id);
                    $details = RequestpembelianDetail::where('id_request_pembelian_header', $header->id)->get();
                    $hasTalanganColumns = Schema::hasColumn('pencatatan_keuangan', 'is_talangan');

                    foreach ($details as $detail) {
                        $totalNominal = (float) ($detail->total_invoice ?? ($detail->kuantitas * $detail->harga));

                        $ledgerData = [
                            'tanggal'                => $header->tgl_request,
                            'project_id'             => $header->id_project,
                            'sub_kategori_pendanaan' => $detail->id_subkategori_sumberdana ?? null,
                            'jenis_transaksi'        => 'pengeluaran',
                            'deskripsi_transaksi'    => "[REQBUY#{$header->id}] Pembelian: " . $detail->nama_barang,
                            'jumlah_transaksi'       => $totalNominal,
                            'metode_pembayaran'      => 'Transfer',
                            'bukti_transaksi'        => $header->bukti_transfer ?? $detail->invoice_pembelian ?? null,
                            'request_pembelian_id'   => $header->id,
                        ];
                        if ($hasTalanganColumns) {
                            $ledgerData['is_talangan'] = (bool) ($header->is_talangan ?? false);
                            $ledgerData['talangan_ref_type'] = (bool) ($header->is_talangan ?? false) ? 'request_pembelian' : null;
                            $ledgerData['talangan_ref_id'] = (bool) ($header->is_talangan ?? false) ? $header->id : null;
                            $ledgerData['is_reclass'] = false;
                            $ledgerData['reclass_group_id'] = null;
                        }
                        PencatatanKeuangan::create($ledgerData);

                        if ($detail->id_subkategori_sumberdana) {
                            $detailSubkategori = DetailSubkategori::where('id_subkategori_sumberdana', $detail->id_subkategori_sumberdana)
                                ->where('id_project', $header->id_project)
                                ->first();

                            if ($detailSubkategori) {
                                $detailSubkategori->realisasi_anggaran = ($detailSubkategori->realisasi_anggaran ?? 0) + $totalNominal;
                                $detailSubkategori->save();
                            }
                        }
                    }

                    $biayaAdmin = (float) ($header->biaya_admin_transfer ?? 0);
                    if ($biayaAdmin > 0) {
                        $adminLedgerData = [
                            'tanggal'                => $header->tgl_request,
                            'project_id'             => $header->id_project,
                            'sub_kategori_pendanaan' => null,
                            'jenis_transaksi'        => 'pengeluaran',
                            'deskripsi_transaksi'    => "[REQBUY#{$header->id}] Biaya Admin Transfer",
                            'jumlah_transaksi'       => $biayaAdmin,
                            'metode_pembayaran'      => 'Transfer',
                            'bukti_transaksi'        => $header->bukti_transfer ?? null,
                            'request_pembelian_id'   => $header->id,
                        ];
                        if ($hasTalanganColumns) {
                            $adminLedgerData['is_talangan'] = (bool) ($header->is_talangan ?? false);
                            $adminLedgerData['talangan_ref_type'] = (bool) ($header->is_talangan ?? false) ? 'request_pembelian' : null;
                            $adminLedgerData['talangan_ref_id'] = (bool) ($header->is_talangan ?? false) ? $header->id : null;
                            $adminLedgerData['is_reclass'] = false;
                            $adminLedgerData['reclass_group_id'] = null;
                        }
                        PencatatanKeuangan::create($adminLedgerData);
                    }
                }
            } else {
                // Untuk status lain
                $header->status_request = $request->status_request;
                $header->user_id_updated = Auth::user()->id;
                $header->updated_at = now();

                if ($request->status_request == 'reject_request' || $request->status_request == 'reject_payment') {
                    $header->keterangan_reject = $request->keterangan_reject;
                } else {
                    $header->keterangan_reject = null;
                }

                $header->save();
            }

            return redirect()->route('requestpembelian.index')->with('success', 'Status Request Pembelian berhasil diubah');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return redirect()->route('requestpembelian.index')->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function storeInvoiceItem(Request $request, string $id)
    {
        $request->validate([
            'invoice_pembelian' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120|required_without:total_invoice',
            'total_invoice' => 'nullable|numeric|min:0|required_without:invoice_pembelian',
        ]);

        $detail = RequestpembelianDetail::findOrFail($id);
        $header = RequestpembelianHeader::findOrFail($detail->id_request_pembelian_header);

        if (!in_array(Auth::user()->role, ['admin', 'bendahara'], true)) {
            return redirect()->route('requestpembelian.detail', $header->id)
                ->with('error', 'Hanya admin/bendahara yang bisa upload invoice item.');
        }

        if (!in_array($header->status_request, ['submit_request', 'approve_request', 'reject_payment', 'submit_payment', 'approve_payment', 'done'], true)) {
            return redirect()->route('requestpembelian.detail', $header->id)
                ->with('error', 'Invoice item hanya bisa diunggah saat proses pembelian.');
        }

        if ($request->hasFile('invoice_pembelian')) {
            if (!empty($detail->invoice_pembelian) && Storage::disk('public')->exists($detail->invoice_pembelian)) {
                Storage::disk('public')->delete($detail->invoice_pembelian);
            }
            $path = $request->file('invoice_pembelian')->store('request_pembelian/invoice_item', 'public');
            $detail->invoice_pembelian = $path;
        }

        if ($request->filled('total_invoice')) {
            if (!Schema::hasColumn('request_pembelian_detail', 'total_invoice')) {
                return redirect()->route('requestpembelian.detail', $header->id)
                    ->with('error', "Kolom total_invoice belum ada. Jalankan 'php artisan migrate' terlebih dahulu.");
            }
            $detail->total_invoice = (float) $request->total_invoice;
        }
        $detail->user_id_updated = Auth::id();
        $detail->save();

        return redirect()->route('requestpembelian.detail', $header->id)
            ->with('success', 'Invoice per item berhasil diunggah.');
    }

    public function storeInvoiceBulk(Request $request, string $id)
    {
        $request->validate([
            'total_invoice' => 'nullable|array',
            'total_invoice.*' => 'nullable|numeric|min:0',
            'is_talangan' => 'nullable|boolean',
            'biaya_admin_transfer' => 'nullable|numeric|min:0',
            'status_request' => 'nullable|string',
            'keterangan_reject' => 'nullable|string|max:500',
            'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $header = RequestpembelianHeader::findOrFail($id);

        if (!in_array(Auth::user()->role, ['admin', 'bendahara'], true)) {
            return redirect()->route('requestpembelian.detail', $header->id)
                ->with('error', 'Hanya admin/bendahara yang bisa upload invoice item.');
        }

        if (!in_array($header->status_request, ['submit_request', 'approve_request', 'reject_payment', 'submit_payment', 'approve_payment', 'done'], true)) {
            return redirect()->route('requestpembelian.detail', $header->id)
                ->with('error', 'Invoice item hanya bisa diunggah saat proses pembelian.');
        }

        $totals = $request->input('total_invoice', []);
        $hasAnyTotal = collect($totals)->contains(function ($v) {
            return $v !== null && $v !== '';
        });
        $hasStatus = $request->filled('status_request');
        $hasBuktiTransfer = $request->hasFile('bukti_transfer');
        $hasBiayaAdmin = $request->filled('biaya_admin_transfer');
        $hasTalanganHeaderCol = Schema::hasColumn('request_pembelian_header', 'is_talangan');
        $requestedTalangan = (bool) $request->boolean('is_talangan');
        $hasTalanganChange = $hasTalanganHeaderCol
            ? ($requestedTalangan !== (bool) ($header->is_talangan ?? false))
            : false;

        if (!$hasAnyTotal && !$hasStatus && !$hasBuktiTransfer && !$hasBiayaAdmin && !$hasTalanganChange) {
            return redirect()->route('requestpembelian.detail', $header->id)
                ->with('error', 'Belum ada perubahan yang disubmit.');
        }

        $details = RequestpembelianDetail::where('id_request_pembelian_header', $header->id)->get();
        $hasTotalInvoiceColumn = Schema::hasColumn('request_pembelian_detail', 'total_invoice');

        foreach ($details as $detail) {
            $isChanged = false;
            $detailId = (string) $detail->id;

            if (array_key_exists($detailId, $totals) && $totals[$detailId] !== '' && $totals[$detailId] !== null) {
                if (!$hasTotalInvoiceColumn) {
                    return redirect()->route('requestpembelian.detail', $header->id)
                        ->with('error', "Kolom total_invoice belum ada. Jalankan 'php artisan migrate' terlebih dahulu.");
                }
                $detail->total_invoice = (float) $totals[$detailId];
                $isChanged = true;
            }

            if ($isChanged) {
                $detail->user_id_updated = Auth::id();
                $detail->save();
            }
        }

        if ($request->filled('biaya_admin_transfer')) {
            $header->biaya_admin_transfer = (float) $request->biaya_admin_transfer;
        }

        if ($hasTalanganChange) {
            $header->is_talangan = $requestedTalangan;
            if (Schema::hasColumn('request_pembelian_header', 'status_alokasi') && $requestedTalangan && empty($header->status_alokasi)) {
                $header->status_alokasi = 'belum';
            }
            if (!$requestedTalangan) {
                if (Schema::hasColumn('request_pembelian_header', 'status_alokasi')) {
                    $header->status_alokasi = 'belum';
                }
                if (Schema::hasColumn('request_pembelian_header', 'project_id_alokasi_final')) {
                    $header->project_id_alokasi_final = null;
                }
                if (Schema::hasColumn('request_pembelian_header', 'tanggal_alokasi_final')) {
                    $header->tanggal_alokasi_final = null;
                }
                if (Schema::hasColumn('request_pembelian_header', 'catatan_alokasi')) {
                    $header->catatan_alokasi = null;
                }
            }
        }

        if ($request->hasFile('bukti_transfer')) {
            if (!empty($header->bukti_transfer) && Storage::disk('public')->exists($header->bukti_transfer)) {
                Storage::disk('public')->delete($header->bukti_transfer);
            }
            $header->bukti_transfer = $request->file('bukti_transfer')->store('request_pembelian/bukti_transfer', 'public');
        }

        if ($request->filled('status_request')) {
            $statusRequest = $request->status_request;

            if ($statusRequest === 'approve_payment') {
                $hasTalanganHeaderCols = Schema::hasColumn('request_pembelian_header', 'is_talangan');
                if ($hasTalanganHeaderCols && $request->has('is_talangan')) {
                    $header->is_talangan = (bool) $request->boolean('is_talangan');
                }
                if (Schema::hasColumn('request_pembelian_header', 'status_alokasi')
                    && (bool) ($header->is_talangan ?? false)
                    && empty($header->status_alokasi)) {
                    $header->status_alokasi = 'belum';
                }
                $header->nominal_final_total = null;
                $header->nominal_penambahan = 0;
                $header->nominal_pengurangan = 0;
                $header->keterangan_penambahan = null;
                $header->keterangan_pengurangan = null;
                $header->status_request = 'done';
                $header->keterangan_reject = null;

                $existing = PencatatanKeuangan::where('request_pembelian_id', $header->id)->exists();
                if (!$existing) {
                    $detailRows = RequestpembelianDetail::where('id_request_pembelian_header', $header->id)->get();
                    $hasTalanganColumns = Schema::hasColumn('pencatatan_keuangan', 'is_talangan');
                    foreach ($detailRows as $detail) {
                        $totalNominal = (float) ($detail->total_invoice ?? ($detail->kuantitas * $detail->harga));

                        $ledgerData = [
                            'tanggal'                => $header->tgl_request,
                            'project_id'             => $header->id_project,
                            'sub_kategori_pendanaan' => $detail->id_subkategori_sumberdana ?? null,
                            'jenis_transaksi'        => 'pengeluaran',
                            'deskripsi_transaksi'    => "[REQBUY#{$header->id}] Pembelian: " . $detail->nama_barang,
                            'jumlah_transaksi'       => $totalNominal,
                            'metode_pembayaran'      => 'Transfer',
                            'bukti_transaksi'        => $header->bukti_transfer ?? $detail->invoice_pembelian ?? null,
                            'request_pembelian_id'   => $header->id,
                        ];
                        if ($hasTalanganColumns) {
                            $ledgerData['is_talangan'] = (bool) ($header->is_talangan ?? false);
                            $ledgerData['talangan_ref_type'] = (bool) ($header->is_talangan ?? false) ? 'request_pembelian' : null;
                            $ledgerData['talangan_ref_id'] = (bool) ($header->is_talangan ?? false) ? $header->id : null;
                            $ledgerData['is_reclass'] = false;
                            $ledgerData['reclass_group_id'] = null;
                        }
                        PencatatanKeuangan::create($ledgerData);

                        if ($detail->id_subkategori_sumberdana) {
                            $detailSubkategori = DetailSubkategori::where('id_subkategori_sumberdana', $detail->id_subkategori_sumberdana)
                                ->where('id_project', $header->id_project)
                                ->first();

                            if ($detailSubkategori) {
                                $detailSubkategori->realisasi_anggaran = ($detailSubkategori->realisasi_anggaran ?? 0) + $totalNominal;
                                $detailSubkategori->save();
                            }
                        }
                    }

                    $biayaAdmin = (float) ($header->biaya_admin_transfer ?? 0);
                    if ($biayaAdmin > 0) {
                        $adminLedgerData = [
                            'tanggal'                => $header->tgl_request,
                            'project_id'             => $header->id_project,
                            'sub_kategori_pendanaan' => null,
                            'jenis_transaksi'        => 'pengeluaran',
                            'deskripsi_transaksi'    => "[REQBUY#{$header->id}] Biaya Admin Transfer",
                            'jumlah_transaksi'       => $biayaAdmin,
                            'metode_pembayaran'      => 'Transfer',
                            'bukti_transaksi'        => $header->bukti_transfer ?? null,
                            'request_pembelian_id'   => $header->id,
                        ];
                        if ($hasTalanganColumns) {
                            $adminLedgerData['is_talangan'] = (bool) ($header->is_talangan ?? false);
                            $adminLedgerData['talangan_ref_type'] = (bool) ($header->is_talangan ?? false) ? 'request_pembelian' : null;
                            $adminLedgerData['talangan_ref_id'] = (bool) ($header->is_talangan ?? false) ? $header->id : null;
                            $adminLedgerData['is_reclass'] = false;
                            $adminLedgerData['reclass_group_id'] = null;
                        }
                        PencatatanKeuangan::create($adminLedgerData);
                    }
                }
            } else {
                $header->status_request = $statusRequest;
                if ($statusRequest === 'reject_request' || $statusRequest === 'reject_payment') {
                    $header->keterangan_reject = $request->keterangan_reject;
                } else {
                    $header->keterangan_reject = null;
                }
            }
        }

        $header->user_id_updated = Auth::id();
        $header->updated_at = now();
        $header->save();

        return redirect()->route('requestpembelian.detail', $header->id)
            ->with('success', 'Data berhasil disimpan.');
    }

    public function talanganIndex()
    {
        if (!in_array(Auth::user()->role, ['admin', 'bendahara'], true)) {
            abort(403, 'Unauthorized');
        }

        if (!Schema::hasColumn('request_pembelian_header', 'is_talangan')) {
            return redirect()->route('requestpembelian.index')
                ->with('error', "Fitur talangan belum aktif. Jalankan 'php artisan migrate'.");
        }

        $rows = DB::table('request_pembelian_header as h')
            ->leftJoin('project as p', 'h.id_project', '=', 'p.id')
            ->leftJoin('project as pf', 'h.project_id_alokasi_final', '=', 'pf.id')
            ->where('h.status_request', 'done')
            ->where('h.is_talangan', 1)
            ->select(
                'h.id',
                'h.no_request',
                'h.tgl_request',
                'h.biaya_admin_transfer',
                'h.status_alokasi',
                'h.project_id_alokasi_final',
                'h.tanggal_alokasi_final',
                'h.catatan_alokasi',
                'p.nama_project as nama_project_awal',
                'pf.nama_project as nama_project_alokasi',
                DB::raw('(SELECT COALESCE(SUM(COALESCE(d.total_invoice, (d.kuantitas * d.harga))),0) FROM request_pembelian_detail d WHERE d.id_request_pembelian_header = h.id) as total_invoice')
            )
            ->orderByDesc('h.id')
            ->get();

        $projects = Project::query()
            ->where('status', 'aktif')
            ->orderBy('nama_project')
            ->get(['id', 'nama_project']);

        return view('requestpembelian.talangan', compact('rows', 'projects'));
    }

    public function talanganAllocate(Request $request, string $id)
    {
        if (!in_array(Auth::user()->role, ['admin', 'bendahara'], true)) {
            abort(403, 'Unauthorized');
        }

        if (!Schema::hasColumn('request_pembelian_header', 'is_talangan')) {
            return back()->with('error', "Fitur talangan belum aktif. Jalankan 'php artisan migrate'.");
        }

        $validated = $request->validate([
            'project_id_alokasi_final' => 'required|exists:project,id',
            'tanggal_alokasi_final' => 'required|date',
            'catatan_alokasi' => 'nullable|string|max:500',
        ]);

        $header = RequestpembelianHeader::findOrFail($id);
        if (!(bool) ($header->is_talangan ?? false)) {
            return back()->with('error', 'Request ini bukan transaksi talangan.');
        }
        if (($header->status_request ?? '') !== 'done') {
            return back()->with('error', 'Hanya request dengan status selesai yang bisa dialokasikan.');
        }

        DB::transaction(function () use ($header, $validated) {
            $targetProjectId = (int) $validated['project_id_alokasi_final'];
            $reclassGroupId = 'RECLASS-REQBUY-' . $header->id . '-' . Str::uuid()->toString();

            $ledgerUpdate = ['project_id' => $targetProjectId];
            if (Schema::hasColumn('pencatatan_keuangan', 'is_talangan')) {
                $ledgerUpdate['is_talangan'] = false;
                $ledgerUpdate['is_reclass'] = true;
                $ledgerUpdate['reclass_group_id'] = $reclassGroupId;
            }

            PencatatanKeuangan::where('request_pembelian_id', $header->id)->update($ledgerUpdate);

            $header->project_id_alokasi_final = $targetProjectId;
            $header->tanggal_alokasi_final = $validated['tanggal_alokasi_final'];
            $header->catatan_alokasi = $validated['catatan_alokasi'] ?? null;
            $header->status_alokasi = 'sudah';
            $header->user_id_updated = Auth::id();
            $header->updated_at = now();
            $header->save();
        });

        return back()->with('success', 'Talangan berhasil dialokasikan ke project final.');
    }

    public function pengajuanulang(string $id)
    {
        try {
            $request_pembelian = RequestpembelianHeader::find($id);

            if (!$request_pembelian) {
                return redirect()->route('requestpembelian.index')->with('error', 'Request tidak ditemukan');
            }

            $newStatus = 'submit_request';

            if ($request_pembelian->status_request == 'reject_request') {
                $newStatus = 'submit_request';
            } elseif ($request_pembelian->status_request == 'reject_payment') {
                // biarin sesuai alur kamu
                $newStatus = 'approve_request'; // biar user upload ulang satu-satu lagi
            }

            RequestpembelianHeader::where('id', $id)->update([
                'status_request'    => $newStatus,
                'keterangan_reject' => null,
                'user_id_updated'   => Auth::user()->id,
                'updated_at'        => now(),
            ]);

            return redirect()->route('requestpembelian.index')->with('success', 'Pengajuan ulang berhasil');
        } catch (\Exception $e) {
            Log::error('Error pengajuan ulang: ' . $e->getMessage());
            return redirect()->route('requestpembelian.index')->with('error', 'Pengajuan ulang gagal: ' . $e->getMessage());
        }
    }

    public function submitRequest(string $id)
    {
        try {
            $header = RequestpembelianHeader::findOrFail($id);

            // 1. Validasi role (hanya creator)
            if ($header->user_id_created != Auth::id()) {
                abort(403);
            }

            // 2. Validasi status (wajib draft)
            if ($header->status_request !== 'draft') {
                return back()->with('error', 'Hanya pengajuan berstatus Draft yang bisa dikirim.');
            }

            // 3. Validasi item & total
            $details = RequestpembelianDetail::where('id_request_pembelian_header', $id)->get();
            if ($details->count() === 0) {
                return back()->with('error', 'Gagal kirim: Minimal harus ada 1 item barang.');
            }

            $total = 0;
            foreach ($details as $d) {
                $total += ($d->kuantitas * $d->harga);
            }

            if ($total <= 0) {
                return back()->with('error', 'Gagal kirim: Total pengajuan harus lebih dari Rp 0.');
            }

            // 4. Update status
            $header->status_request = 'submit_request';
            $header->updated_at = now();
            $header->save();

            return redirect()
                ->route('requestpembelian.index')
                ->with('success', 'Pengajuan berhasil dikirim ke Admin/Bendahara.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
