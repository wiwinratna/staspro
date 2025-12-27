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

class RequestpembelianController extends Controller
{
    public function index()
    {
        $q = DB::table('request_pembelian_header as a')
            ->leftJoin('project as b', 'a.id_project', '=', 'b.id')
            ->leftJoin(DB::raw("(SELECT id_request_pembelian_header, GROUP_CONCAT(CONCAT(kuantitas, ' x ', nama_barang)) as nama_barang, SUM(harga * kuantitas) as total_harga FROM request_pembelian_detail GROUP BY id_request_pembelian_header) as c"), 'a.id', '=', 'c.id_request_pembelian_header')
            ->select('a.id', 'a.no_request', 'b.nama_project', 'c.nama_barang', 'c.total_harga', 'a.status_request');

        // ✅ kalau bukan admin, tampilkan hanya yang dibuat user tersebut
        if (Auth::user()->role != 'admin') {
            $q->where('a.user_id_created', Auth::id());
        }

        $request_pembelian = $q->get();

        return view('requestpembelian.index', ['request_pembelian' => $request_pembelian]);
    }

    public function create()
    {
        $user = auth()->user();

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

        return view('requestpembelian.create', compact('project'));
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
        $request_pembelian = RequestpembelianHeader::find($id);
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
        if (Auth::user()->role != 'admin' && $request_pembelian->user_id_created != Auth::id()) {
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

    // ✅ FIX UTAMA: upload bukti boleh satu-satu, status berubah hanya jika SEMUA sudah upload
    public function storebukti(Request $request, string $id)
    {
        // validasi request + file
        $request->validate([
            'id_request_pembelian_header' => 'required|exists:request_pembelian_header,id',
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $headerId = $request->id_request_pembelian_header;
        $header   = RequestpembelianHeader::findOrFail($headerId);

        // admin tidak boleh upload
        if (Auth::user()->role === 'admin') {
            return redirect()
                ->route('requestpembelian.detail', $headerId)
                ->with('error', 'Admin tidak diperbolehkan mengunggah bukti pembayaran.');
        }

        // status yang mengizinkan upload (approve_request / reject_payment / submit_payment)
        if (!in_array($header->status_request, ['approve_request', 'reject_payment', 'submit_payment'])) {
            return redirect()
                ->route('requestpembelian.detail', $headerId)
                ->with('error', 'Bukti bayar hanya bisa diupload setelah Approve Request atau saat Reject Payment.');
        }

        try {
            // hapus file lama (kalau upload ulang per item)
            $detail = RequestpembelianDetail::findOrFail($id);
            if ($detail->bukti_bayar && File::exists('bukti_bayar/' . $detail->bukti_bayar)) {
                File::delete('bukti_bayar/' . $detail->bukti_bayar);
            }

            // upload file
            $bukti_bayar = $request->file('bukti_bayar');
            $filename_buktibayar = time() . '_' . $id . '.' . $bukti_bayar->getClientOriginalExtension();
            $bukti_bayar->move('bukti_bayar', $filename_buktibayar);

            // update detail bukti
            RequestpembelianDetail::where('id', $id)->update([
                'bukti_bayar'     => $filename_buktibayar,
                'user_id_updated' => Auth::id(),
                'updated_at'      => now(),
            ]);

            // 🔥 cek apakah semua detail sudah ada bukti
            $totalItem = RequestpembelianDetail::where('id_request_pembelian_header', $headerId)->count();
            $uploaded  = RequestpembelianDetail::where('id_request_pembelian_header', $headerId)
                ->whereNotNull('bukti_bayar')
                ->where('bukti_bayar', '!=', '')
                ->count();

            // kalau semua sudah upload → baru jadi submit_payment
            if ($totalItem > 0 && $uploaded === $totalItem) {
                $header->status_request    = 'submit_payment';
                $header->keterangan_reject = null;
                $header->user_id_updated   = Auth::id();
                $header->updated_at        = now();
                $header->save();
            }

            return redirect()
                ->route('requestpembelian.detail', $headerId)
                ->with('success', "Bukti berhasil diunggah ($uploaded / $totalItem)");

        } catch (\Exception $e) {
            return redirect()
                ->route('requestpembelian.detail', $headerId)
                ->with('error', 'Bukti Pembayaran gagal diunggah: ' . $e->getMessage());
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
            'keterangan_reject' => 'nullable|string|max:500',
        ]);

        Log::info('Status request yang diterima: ' . $request->status_request);

        try {
            $header = RequestpembelianHeader::findOrFail($validated['id_request_pembelian_header']);

            if ($request->status_request == 'approve_payment') {
                Log::info('ID Request Pembelian: ' . $header->id);

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

                    foreach ($details as $detail) {
                        $totalNominal = $detail->kuantitas * $detail->harga;

                        PencatatanKeuangan::create([
                            'tanggal'                => $header->tgl_request,
                            'project_id'             => $header->id_project,
                            'sub_kategori_pendanaan' => $detail->id_subkategori_sumberdana ?? null,
                            'jenis_transaksi'        => 'pengeluaran',
                            'deskripsi_transaksi'    => 'Pembelian: ' . $detail->nama_barang,
                            'jumlah_transaksi'       => $totalNominal,
                            'metode_pembayaran'      => 'Transfer',
                            'bukti_transaksi'        => $detail->bukti_bayar ?? null,
                            'request_pembelian_id'   => $header->id,
                        ]);

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
}
