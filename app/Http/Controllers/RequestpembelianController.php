<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RequestpembelianDetail;
use App\Models\RequestpembelianHeader;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RequestpembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $request_pembelian = DB::table('request_pembelian_header as a')
            ->leftJoin('project as b', 'a.id_project', '=', 'b.id')
            ->leftJoin(DB::raw("(SELECT id_request_pembelian_header, GROUP_CONCAT(CONCAT(kuantitas, ' x ', nama_barang)) as nama_barang, SUM(harga * kuantitas) as total_harga FROM request_pembelian_detail GROUP BY id_request_pembelian_header) as c"), 'a.id', '=', 'c.id_request_pembelian_header')
            ->select('a.id', 'a.no_request', 'b.nama_project', 'c.nama_barang', 'c.total_harga', 'a.status_request')
            ->get();

        return view('requestpembelian.index', ['request_pembelian' => $request_pembelian]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $project = Project::all();

        return view('requestpembelian.create', ['project' => $project]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_request' => 'required',
            'id_project'  => 'required',
        ]);

        try {
            $request_pembelian = RequestpembelianHeader::create([
                'no_request'      => 'REQ' . date('YmdHis'),
                'tgl_request'     => $request->tgl_request,
                'id_project'      => $request->id_project,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('requestpembelian.detail', $request_pembelian->id)->with('success', 'Request Pembelian berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->route('requestpembelian.index')->with('error', 'Request Pembelian gagal dibuat');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $request_pembelian = RequestpembelianHeader::find($id);
        $detail            = RequestpembelianDetail::where('id_request_pembelian_header', $id)->get();
        $project           = Project::all();

        return view('requestpembelian.edit', ['request_pembelian' => $request_pembelian, 'detail' => $detail, 'project' => $project]);
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            RequestpembelianHeader::destroy($id);

            return redirect()->route('requestpembelian.index')->with('success', 'Request Pembelian berhasil dihapus');
        } catch (\Exception) {
            return redirect()->route('requestpembelian.index')->with('error', 'Request Pembelian gagal dihapus');
        }
    }

    public function detail(string $id)
    {
        $request_pembelian = RequestpembelianHeader::find($id);
        $detail            = RequestpembelianDetail::where('id_request_pembelian_header', $id)->get();
        $project           = Project::all();

        return view('requestpembelian.detail', ['request_pembelian' => $request_pembelian, 'detail' => $detail, 'project' => $project]);
    }

    public function storedetail(Request $request)
    {
        $validated = $request->validate([
            'nama_barang'    => 'required',
            'kuantitas'      => 'required',
            'harga'          => 'required',
            'link_pembelian' => 'required',
        ]);

        try {
            RequestpembelianDetail::create([
                'nama_barang'                 => $request->nama_barang,
                'kuantitas'                   => $request->kuantitas,
                'harga'                       => $request->harga,
                'link_pembelian'              => $request->link_pembelian,
                'id_request_pembelian_header' => $request->id_request_pembelian_header,
                'user_id_created'             => Auth::user()->id,
                'user_id_updated'             => Auth::user()->id,
            ]);

            return redirect()->route('requestpembelian.detail', $request->id_request_pembelian_header)->with('success', 'Detail Request Pembelian berhasil dibuat');
        } catch (\Exception) {
            return redirect()->route('requestpembelian.detail', $request->id_request_pembelian_header)->with('error', 'Detail Request Pembelian gagal dibuat');
        }
    }

    public function addbukti(string $id)
    {
        $detail = RequestpembelianDetail::find($id);
        $request_pembelian = RequestpembelianHeader::find($detail->id_request_pembelian_header);

        // Cek status request
        if ($request_pembelian->status_request !== 'approved') {
            return redirect()->route('requestpembelian.detail', $request_pembelian->id)
                ->with('error', 'Anda tidak dapat mengupload bukti pembayaran karena request belum disetujui.');
        }

        return view('requestpembelian.addbukti', ['detail' => $detail]);
    }

    public function storebukti(Request $request, string $id)
    {
        $validated = $request->validate([
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $detail = RequestpembelianDetail::find($id);
        $request_pembelian = RequestpembelianHeader::find($detail->id_request_pembelian_header);

        // Cek status request
        if ($request_pembelian->status_request !== 'approved') {
            return redirect()->route('requestpembelian.detail', $request_pembelian->id)
                ->with('error', 'Anda tidak dapat mengupload bukti pembayaran karena request belum disetujui.');
        }

        try {
            $bukti_bayar         = $request->file('bukti_bayar');
            $filename_buktibayar = time() . '.' . $bukti_bayar->getClientOriginalExtension();
            $bukti_bayar->move('bukti_bayar', $filename_buktibayar);

            RequestpembelianDetail::where('id', $id)->update([
                'bukti_bayar'     => $filename_buktibayar,
                'user_id_updated' => Auth::user()->id,
                'updated_at'      => now(),
            ]);

            return redirect()->route('requestpembelian.detail', $request->id_request_pembelian_header)->with('success', 'Bukti Pembayaran berhasil diunggah');
        } catch (\Exception $e) {
            return redirect()->route('requestpembelian.detail', $request->id_request_pembelian_header)->with('error', 'Bukti Pembayaran gagal diunggah');
        }
    }

    public function editdetail(string $id)
    {
        $detail = RequestpembelianDetail::find($id);

        return view('requestpembelian.editdetail', ['detail' => $detail]);
    }

    public function updatedetail(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama_barang'    => 'required',
            'kuantitas'      => 'required',
            'harga'          => 'required',
            'link_pembelian' => 'required',
        ]);

        try {
            $detail = RequestpembelianDetail::find($id);

            if ($request->hasFile('bukti_bayar')) {
                if ($detail->bukti_bayar && File::exists('bukti_bayar/' . $detail->bukti_bayar)) {
                    File::delete('bukti_bayar/' . $detail->bukti_bayar);
                }

                $bukti_bayar         = $request->file('bukti_bayar');
                $filename_buktibayar = time() . '.' . $bukti_bayar->getClientOriginalExtension();
                $bukti_bayar->move('bukti_bayar', $filename_buktibayar);
            }

            RequestpembelianDetail::where('id', $id)->update([
                'nama_barang'     => $request->nama_barang,
                'kuantitas'       => $request->kuantitas,
                'harga'           => $request->harga,
                'link_pembelian'  => $request->link_pembelian,
                'bukti_bayar'     => $request->hasFile('bukti_bayar') ? $filename_buktibayar : $detail->bukti_bayar,
                'user_id_updated' => Auth::user()->id,
                'updated_at'      => now(),
            ]);

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
        $validated = $request->validate([
            'status_request' => 'required',
        ]);

        if ($request->status_request == 'reject_request' || $request->status_request == 'reject_payment') {
            $request->validate([
                'keterangan_reject' => 'required',
            ]);
        }

        try {
            RequestpembelianHeader::where('id', $request->id_request_pembelian_header)->update([
                'status_request'    => $request->status_request,
                'keterangan_reject' => $request->keterangan_reject,
                'user_id_updated'   => Auth::user()->id,
                'updated_at'        => now(),
            ]);

            return redirect()->route('requestpembelian.index')->with('success', 'Status Request Pembelian berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('requestpembelian.index')->with('error', $e->getMessage());
        }
    }

    public function pengajuanulang(string $id)
    {
        $request_pembelian = RequestpembelianHeader::find($id);
        try {
            RequestpembelianHeader::where('id', $id)->update([
                'status_request'    => $request_pembelian->status_request == 'reject_request' ? 'submit_request' : 'submit_payment',
                'keterangan_reject' => null,
                'user_id_updated'   => Auth::user()->id,
                'updated_at'        => now(),
            ]);

            return redirect()->route('requestpembelian.index')->with('success', 'Pengajuan ulang berhasil');
        } catch (\Exception) {
            return redirect()->route('requestpembelian.index')->with('error', 'Pengajuan ulang gagal');
        }
    }

    public function approve($id)
    {
        DB::beginTransaction();

        try {
            // Ambil request dan pastikan ada
            $request = RequestpembelianHeader::with('details')->findOrFail($id);

            // Cek apakah sudah approved sebelumnya
            if ($request->status_request === 'approved') {
                return redirect()->back()->with('info', 'Request sudah disetujui sebelumnya.');
            }

            // Update status request
            $request->status_request = 'approved';
            $request->user_id_updated = Auth::id();
            $request->updated_at = now();
            $request->save();

            // Hitung total pengeluaran
            $jumlah_pengeluaran = $request->details->sum(function($detail) {
                return $detail->kuantitas * $detail->harga;
            });

            // Ambil project terkait
            $project = Project::findOrFail($request->id_project);

            // Update realisasi anggaran di detail subkategori
            foreach ($request->details as $detail) {
                DetailSubkategori::where('id_project', $project->id)
                    ->where('id_subkategori_sumberdana', $detail->id_subkategori_sumberdana)
                    ->increment('realisasi_anggaran', $detail->kuantitas * $detail->harga);
    }

            // Buat transaksi otomatis
            Transaksi::create([
                'tanggal' => now(),
                'project_id' => $project->id,
                'jenis_transaksi' => 'pengeluaran',
                'jumlah_transaksi' => $jumlah_pengeluaran,
                'deskripsi_transaksi' => 'Otomatis dari request pembelian no. ' . $request->no_request,
                'sub_kategori_pendanaan' => $project->sub_kategori_pendanaan ?? '-',
                'metode_pembayaran' => 'default',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Request berhasil disetujui dan dana project diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyetujui request: ' . $e->getMessage());
        }
    }
}
