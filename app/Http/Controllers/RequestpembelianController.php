<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RequestpembelianDetail;
use App\Models\RequestpembelianHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            RequestpembelianDetail::where('id', $id)->update([
                'nama_barang'     => $request->nama_barang,
                'kuantitas'       => $request->kuantitas,
                'harga'           => $request->harga,
                'link_pembelian'  => $request->link_pembelian,
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

        try {
            RequestpembelianHeader::where('id', $request->id_request_pembelian_header)->update([
                'status_request'  => $request->status_request,
                'user_id_updated' => Auth::user()->id,
                'updated_at'      => now(),
            ]);

            return redirect()->route('requestpembelian.index')->with('success', 'Status Request Pembelian berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('requestpembelian.index')->with('error', $e->getMessage());
        }
    }
}
