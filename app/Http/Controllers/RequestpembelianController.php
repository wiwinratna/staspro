<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RequestpembelianDetail;
use App\Models\RequestpembelianHeader;
use App\Models\DetailSubkategori;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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
        } catch (\Exception) {
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
        $request_pembelian = RequestpembelianHeader::find($id);
        $detail            = RequestpembelianDetail::where('id_request_pembelian_header', $id)->get();
        $project           = Project::all();

        return view('requestpembelian.detail', ['request_pembelian' => $request_pembelian, 'detail' => $detail, 'project' => $project]);
    }

    public function storedetail(Request $request)
    {
        $validated = $request->validate([
            'nama_barang'    => 'required|string|max:255',
            'kuantitas'      => 'required|numeric|min:1',
            'harga'          => 'required|numeric|min:0',
            'link_pembelian' => 'required|url',
            'id_request_pembelian_header' => 'required|exists:request_pembelian_header,id',
        ]);

        try {
            RequestpembelianDetail::create([
                'nama_barang'                 => $validated['nama_barang'],
                'kuantitas'                   => $validated['kuantitas'],
                'harga'                       => $validated['harga'],
                'link_pembelian'              => $validated['link_pembelian'],
                'id_request_pembelian_header' => $validated['id_request_pembelian_header'],
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

    public function storebukti(Request $request, string $id)
    {
        $validated = $request->validate([
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

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
        } catch (\Exception) {
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

    public function setujuiBuktiPembayaran($id)
    {
        $requestHeader = RequestPembelianHeader::findOrFail($id);
        $requestHeader->status = 'done';
        $requestHeader->save();

        // Insert otomatis ke pencatatan transaksi
        $this->createTransaksiFromRequest($requestHeader);

        return redirect()->route('request_pembelian.index')->with('success', 'Bukti disetujui dan transaksi tercatat.');
    }

    private function createTransaksiFromRequest(RequestpembelianHeader $requestPembelian)
    {
        foreach ($requestPembelian->requestPembelianDetail as $detail) {
            $totalNominal = $detail->kuantitas * $detail->harga;

            // Tentukan subkategori berdasarkan id_project
            $project = $requestPembelian->project; // Ambil project terkait
            $subkategori = $this->tentukanSubkategoriPendanaan($requestPembelian);

            Transaksi::create([
                'tanggal'                => $requestPembelian->tgl_request,
                'project_id'             => $project->id,
                'sub_kategori_pendanaan' => $subkategori, // Gunakan subkategori yang ditentukan
                'jenis_transaksi'        => 'pengeluaran',
                'deskripsi_transaksi'    => 'Pembelian: ' . $detail->nama_barang,
                'jumlah_transaksi'       => $totalNominal,
                'metode_pembayaran'      => 'Transfer', // Default
                'bukti_transaksi'        => $detail->bukti_bayar ?? null,
                'request_pembelian_id'   => $requestPembelian->id,
            ]);
        }
    }

    private function tentukanSubkategoriPendanaan(RequestpembelianHeader $requestPembelian)
    {
        $project = $requestPembelian->project; // Ambil project terkait
        $sumberDana = $project->sumberDana; // Asumsi ada relasi ke sumber dana
        
        \Log::info('Sumber Dana: ' . $sumberDana);

        if ($sumberDana) {
            switch ($sumberDana->kategori) {
                case 'internal':
                    return 'Bahan Habis Pakai dan Peralatan'; // Subkategori untuk internal
                case 'DRTPM':
                    return 'Bahan'; // Subkategori untuk DRTPM
                case 'Kedaireka':
                    return 'Bahan Prototype/Produksi Skala Terbatas'; // Subkategori untuk Kedaireka
                case 'LPDP':
                    return 'Biaya Non Personil'; // Subkategori untuk LPDP
                default:
                    return null; // Jika tidak ada kategori yang cocok
            }
        }

        return null; // Jika tidak ada sumber dana
    }

    private function getSubkategori($sumberDana)
    {
        switch ($sumberDana) {
            case 'DRTPM': return 'Bahan';
            case 'Kedaireka': return 'Bahan Habis Penelitian';
            case 'LPDP': return 'Biaya Non-Personil';
            default: return 'Bahan Habis Pakai dan Peralatan'; // asumsikan internal
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
        ]);

        Log::info('Status request yang diterima: ' . $request->status_request);

        try {
            $header = RequestpembelianHeader::findOrFail($validated['id_request_pembelian_header']);

            if ($request->status_request == 'approve_payment') {
                Log::info('ID Request Pembelian: ' . $header->id);
                // Ubah status jadi done
                $header->status_request = 'done';
                $header->user_id_updated = Auth::user()->id;
                $header->updated_at = now();
                $header->save();

                // Cegah duplikasi transaksi
                $existing = Transaksi::where('request_pembelian_id', $header->id)->exists();
                if (!$existing) {
                    Log::info('Membuat transaksi baru untuk request ID: ' . $header->id);
                    $details = RequestpembelianDetail::where('id_request_pembelian_header', $header->id)->get();

                    foreach ($details as $detail) {
                        $totalNominal = $detail->kuantitas * $detail->harga;

                        Transaksi::create([
                            'tanggal'                => $header->tgl_request, // gunakan tanggal request
                            'project_id'             => $header->id_project,
                            'sub_kategori_pendanaan' => $detail->id_subkategori_sumberdana ?? null,
                            'jenis_transaksi'        => 'pengeluaran',
                            'deskripsi_transaksi'    => 'Pembelian: ' . $detail->nama_barang,
                            'jumlah_transaksi'       => $totalNominal,
                            'metode_pembayaran'      => 'Transfer', // default transfer
                            'bukti_transaksi'        => $detail->bukti_bayar ?? null,
                            'request_pembelian_id'   => $header->id, // Pastikan ini diisi
                        ]);
                    }
                }
            } else {
                // Untuk status lain
                $header->status_request = $request->status_request;
                $header->user_id_updated = Auth::user()->id;
                $header->updated_at = now();
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

    public function updateStatus(Request $request, $id)
    {
        $requestPembelian = RequestpembelianHeader::with('requestPembelianDetail')->find($id);

        if (!$requestPembelian) {
            return redirect()->back()->with('error', 'Data request pembelian tidak ditemukan.');
        }

        // Buat objek transaksi baru
        $transaksi = new Transaksi();
        $transaksi->tanggal = now(); // Bisa juga pakai $requestPembelian->tgl_request;
        $transaksi->project_id = $requestPembelian->project->id ?? null;
        $transaksi->sub_kategori_pendanaan = $requestPembelian->pendanaan->sub_kategori ?? '-';
        $transaksi->jenis_transaksi = 'Pengeluaran';
        $transaksi->deskripsi_transaksi = $requestPembelian->requestPembelianDetail->pluck('nama_barang')->implode(', ');
        $transaksi->jumlah_transaksi = $requestPembelian->requestPembelianDetail->sum(function ($item) {
            return $item->harga * $item->kuantitas;
        });
        $transaksi->metode_pembayaran = 'Transfer'; // Default
        $transaksi->bukti_transaksi = '-'; // Nanti bisa diisi manual
        $transaksi->save();

        return redirect()->route('pencatatan_transaksi')->with('success', 'Data transaksi berhasil dibuat dari request pembelian.');
    }
}
