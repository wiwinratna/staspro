<?php

namespace App\Http\Controllers;

use App\Models\DetailSubkategori;
use App\Models\Project;
use App\Models\SubkategoriSumberdana;
use App\Models\Sumberdana;
use App\Models\User;
use App\Models\RequestpembelianHeader;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class ProjectController extends Controller
{
    public function index()
    {
        // biar sumberDana kebaca di card
        $projects = Project::with('sumberDana')->get();

        $joinedProjectIds = [];

        // khusus peneliti: ambil project yang dia join dari detail_project
        if (strtolower(Auth::user()->role ?? '') !== 'admin') {
            $joinedProjectIds = DB::table('detail_project')
                ->where('id_user', Auth::id())
                ->pluck('id_project')
                ->map(fn($v) => (int) $v)
                ->unique()
                ->toArray();
        }

        return view('project', compact('projects', 'joinedProjectIds'));
    }

    public function create()
    {
        $sumber_internal  = Sumberdana::where('jenis_pendanaan', 'internal')->get();
        $sumber_eksternal = Sumberdana::where('jenis_pendanaan', 'eksternal')->get();

        return view('input_project', [
            'sumber_internal'  => $sumber_internal,
            'sumber_eksternal' => $sumber_eksternal
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'         => 'required',
            'nama_project'  => 'required|unique:project,nama_project',
            'durasi'        => 'required',
            'deskripsi'     => 'required',
            'file_proposal' => 'required|mimes:pdf|max:5120',
            'file_rab'      => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            // ========= UPLOAD FILE (AMAN DI CPANEL) =========
            $file_proposal = $request->file('file_proposal');
            $filename_proposal = time() . '_proposal.' . $file_proposal->getClientOriginalExtension();
            $file_proposal->storeAs('file_proposal', $filename_proposal, 'public');

            $file_rab = $request->file('file_rab');
            $filename_rab = time() . '_rab.' . $file_rab->getClientOriginalExtension();
            $file_rab->storeAs('file_rab', $filename_rab, 'public');

            // sumber dana id
            $sumberDanaId = $request->sumber_dana == 'internal'
                ? $request->kategori_pendanaan_internal
                : $request->kategori_pendanaan_eksternal;

            $subkategori_sumberdana = SubkategoriSumberdana::where('id_sumberdana', $sumberDanaId)->get();

            $project = Project::create([
                'tahun'           => $request->tahun,
                'nama_project'    => $request->nama_project,
                'id_sumber_dana'  => $sumberDanaId,
                'durasi'          => $request->durasi,
                'deskripsi'       => $request->deskripsi,
                'file_proposal'   => $filename_proposal,
                'file_rab'        => $filename_rab,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            foreach ($subkategori_sumberdana as $subkategori) {
                $nama_form = $subkategori->nama_form;

                if ($request->has($nama_form)) {
                    $nominal = str_replace(['Rp.', '.', ','], ['', '', '.'], $request->$nama_form);
                    $nominal = (float) $nominal;

                    DetailSubkategori::create([
                        'nominal'                   => $nominal,
                        'id_subkategori_sumberdana' => $subkategori->id,
                        'id_project'                => $project->id,
                        'user_id_created'           => Auth::user()->id,
                        'user_id_updated'           => Auth::user()->id,
                    ]);
                }
            }

            return redirect()->route('project.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('project.index')->with('error', $e->getMessage());
        }
    }

    public function show(Project $project)
    {
        $project = Project::with('sumberDana')->findOrFail($project->id);

        if (!$project->sumberDana) {
            $sumber_dana = DB::table('sumber_dana')
                ->where('id', $project->id_sumber_dana)
                ->first();
        }

        $anggota = DB::table('detail_project as a')
            ->leftJoin('users as b', 'a.id_user', '=', 'b.id')
            ->where('a.id_project', $project->id)
            ->select('b.name')
            ->get();

        $users = DB::table('users as b')
            ->leftJoin('detail_project as a', function ($join) use ($project) {
                $join->on('a.id_user', '=', 'b.id')
                    ->where('a.id_project', '=', $project->id);
            })
            ->whereNull('a.id_user')
            ->where('b.id', '!=', Auth::id())
            ->where('b.role', '!=', 'admin')
            ->select('b.name', 'b.id')
            ->get();

        $total_request_pembelian = DB::table('request_pembelian_detail as a')
            ->leftJoin('request_pembelian_header as b', 'a.id_request_pembelian_header', '=', 'b.id')
            ->where('b.id_project', $project->id)
            ->where('b.status_request', 'done')
            ->sum(DB::raw('a.kuantitas * a.harga'));

        $detail_request = DB::table('request_pembelian_detail as a')
            ->leftJoin('request_pembelian_header as b', 'a.id_request_pembelian_header', '=', 'b.id')
            ->where('b.id_project', $project->id)
            ->where('b.status_request', 'done')
            ->select(
                'a.nama_barang',
                'a.kuantitas',
                'a.harga',
                DB::raw('a.kuantitas * a.harga as total')
            )
            ->get();

        $detail_dana = DB::table('detail_subkategori as a')
            ->leftJoin('subkategori_sumberdana as b', 'a.id_subkategori_sumberdana', '=', 'b.id')
            ->leftJoin('sumber_dana as c', 'b.id_sumberdana', '=', 'c.id')
            ->where('a.id_project', $project->id)
            ->select(
                'b.nama as nama_subkategori',
                'a.nominal',
                'a.realisasi_anggaran',
                'a.id',
                'c.jenis_pendanaan',
                'c.nama_sumber_dana'
            )
            ->get();

        $jenis_pendanaan = $project->sumberDana
            ? $project->sumberDana->jenis_pendanaan
            : (isset($sumber_dana) ? $sumber_dana->jenis_pendanaan : 'internal');

        $nama_sumber_dana = $project->sumberDana
            ? $project->sumberDana->nama_sumber_dana
            : (isset($sumber_dana) ? $sumber_dana->nama_sumber_dana : null);

        $detail_dana = $detail_dana->map(function ($item) use ($total_request_pembelian, $jenis_pendanaan, $nama_sumber_dana) {
            $realisasi = $item->realisasi_anggaran;

            if ($jenis_pendanaan == 'internal' && $item->nama_subkategori == 'Bahan Habis Pakai dan Peralatan') {
                $realisasi += $total_request_pembelian;
            } else if (
                $jenis_pendanaan == 'eksternal' &&
                strtolower($nama_sumber_dana) == strtolower('Kedaireka') &&
                strtolower($item->nama_subkategori) == strtolower('Peralatan Pendukung Terkait Langsung dengan Kegiatan')
            ) {
                $realisasi += $total_request_pembelian;
            } else if (
                $jenis_pendanaan == 'eksternal' &&
                strtolower($nama_sumber_dana) == strtolower('DRPTM') &&
                strtolower($item->nama_subkategori) == strtolower('Bahan')
            ) {
                $realisasi += $total_request_pembelian;
            } else if (
                $jenis_pendanaan == 'eksternal' &&
                strtolower($nama_sumber_dana) == strtolower('LPDP') &&
                strtolower($item->nama_subkategori) == strtolower('Biaya Langsung')
            ) {
                $realisasi += $total_request_pembelian;
            }

            $item->realisasi_anggaran = $realisasi;
            return $item;
        });

        $total_nominal   = $detail_dana->sum('nominal');
        $total_realisasi = $detail_dana->sum('realisasi_anggaran');

        return view('detail_project', [
            'project' => $project,
            'anggota' => $anggota,
            'users' => $users,
            'detail_dana' => $detail_dana,
            'detail_request' => $detail_request,
            'total_request_pembelian' => $total_request_pembelian,
            'total_nominal' => $total_nominal,
            'total_realisasi' => $total_realisasi,
            'sumber_dana' => $project->sumberDana ?? ($sumber_dana ?? null)
        ]);
    }

    // ===================== DOWNLOAD (AMAN) =====================
    public function download_proposal($id)
    {
        $project = Project::findOrFail($id);

        if (!$project->file_proposal) {
            return back()->with('error', 'File proposal belum tersedia.');
        }

        $path = 'file_proposal/' . $project->file_proposal;

        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File proposal tidak ditemukan di server.');
        }

        $downloadName = ($project->nama_project ?? 'proposal') . '_proposal.pdf';
        return Storage::disk('public')->download($path, $downloadName);
    }

    public function download_rab($id)
    {
        $project = Project::findOrFail($id);

        if (!$project->file_rab) {
            return back()->with('error', 'File RAB belum tersedia.');
        }

        $path = 'file_rab/' . $project->file_rab;

        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File RAB tidak ditemukan di server.');
        }

        $downloadName = ($project->nama_project ?? 'rab') . '_rab.xlsx';
        return Storage::disk('public')->download($path, $downloadName);
    }

    public function getSubkategori($id)
    {
        $subkategori = SubkategoriSumberdana::where('id_sumberdana', $id)->get();
        return response()->json($subkategori);
    }

    public function getProjectSubcategories($id)
    {
        $subcategories = DB::table('detail_subkategori as ds')
            ->join('subkategori_sumberdana as ss', 'ds.id_subkategori_sumberdana', '=', 'ss.id')
            ->where('ds.id_project', $id)
            ->select('ds.id', 'ds.nominal', 'ds.id_subkategori_sumberdana', 'ss.nama', 'ss.nama_form')
            ->get();

        return response()->json($subcategories);
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $sumber_internal = SumberDana::where('jenis_pendanaan', 'internal')->get();
        $sumber_eksternal = SumberDana::where('jenis_pendanaan', 'eksternal')->get();

        return view('input_project', compact('project', 'sumber_internal', 'sumber_eksternal'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'nama_project' => 'required|unique:project,nama_project,' . $id,
            'tahun'        => 'required',
            'durasi'       => 'required',
            'deskripsi'    => 'required',
        ]);

        // ========= UPDATE FILE (HAPUS LAMA, SIMPAN BARU) =========
        if ($request->hasFile('file_proposal')) {
            if ($project->file_proposal) {
                Storage::disk('public')->delete('file_proposal/' . $project->file_proposal);
            }

            $file = $request->file('file_proposal');
            $name = time() . '_proposal.' . $file->getClientOriginalExtension();
            $file->storeAs('file_proposal', $name, 'public');
            $project->file_proposal = $name;
        }

        if ($request->hasFile('file_rab')) {
            if ($project->file_rab) {
                Storage::disk('public')->delete('file_rab/' . $project->file_rab);
            }

            $file = $request->file('file_rab');
            $name = time() . '_rab.' . $file->getClientOriginalExtension();
            $file->storeAs('file_rab', $name, 'public');
            $project->file_rab = $name;
        }

        $new_sumber_dana_id = $request->sumber_dana == 'internal'
            ? $request->kategori_pendanaan_internal
            : $request->kategori_pendanaan_eksternal;

        $old_sumber_dana_id = $project->id_sumber_dana;

        $project->nama_project    = $request->nama_project;
        $project->tahun           = $request->tahun;
        $project->durasi          = $request->durasi;
        $project->deskripsi       = $request->deskripsi;
        $project->id_sumber_dana  = $new_sumber_dana_id;
        $project->user_id_updated = Auth::user()->id;
        $project->save();

        $subkategori_sumberdana = SubkategoriSumberdana::where('id_sumberdana', $new_sumber_dana_id)->get();

        if ($old_sumber_dana_id != $new_sumber_dana_id) {
            DetailSubkategori::where('id_project', $project->id)->delete();
        }

        foreach ($subkategori_sumberdana as $subkategori) {
            $nama_form = $subkategori->nama_form;

            if ($request->has($nama_form) && !empty($request->$nama_form)) {
                $nominal_raw = $request->$nama_form;
                $nominal = str_replace(['Rp.', '.', ',', ' '], ['', '', '.', ''], $nominal_raw);
                $nominal = (float) $nominal;

                if ($nominal <= 0) {
                    continue;
                }

                $detail = DetailSubkategori::where('id_project', $project->id)
                    ->where('id_subkategori_sumberdana', $subkategori->id)
                    ->first();

                if ($detail) {
                    $detail->nominal = $nominal;
                    $detail->user_id_updated = Auth::user()->id;
                    $detail->save();
                } else {
                    DetailSubkategori::create([
                        'nominal' => $nominal,
                        'id_subkategori_sumberdana' => $subkategori->id,
                        'id_project' => $project->id,
                        'user_id_created' => Auth::user()->id,
                        'user_id_updated' => Auth::user()->id,
                        'realisasi_anggaran' => 0
                    ]);
                }
            }
        }

        $provided_subcategory_ids = [];
        foreach ($subkategori_sumberdana as $subkategori) {
            $nama_form = $subkategori->nama_form;

            if ($request->has($nama_form) && !empty($request->$nama_form)) {
                $nominal_raw = $request->$nama_form;
                $nominal = str_replace(['Rp.', '.', ',', ' '], ['', '', '.', ''], $nominal_raw);
                $nominal = (float) $nominal;

                if ($nominal > 0) {
                    $provided_subcategory_ids[] = $subkategori->id;
                }
            }
        }

        if (!empty($provided_subcategory_ids)) {
            DetailSubkategori::where('id_project', $project->id)
                ->whereNotIn('id_subkategori_sumberdana', $provided_subcategory_ids)
                ->delete();
        }

        return redirect()->route('project.index')->with('success', 'Project berhasil diupdate!');
    }

    public function destroy($id)
    {
        $requestHeaders = RequestpembelianHeader::where('id_project', $id)->get();

        foreach ($requestHeaders as $header) {
            DB::table('request_pembelian_detail')
                ->where('id_request_pembelian_header', $header->id)
                ->delete();
        }

        RequestpembelianHeader::where('id_project', $id)->delete();
        DB::table('detail_subkategori')->where('id_project', $id)->delete();

        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('project.index');
    }

    public function close(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        if (strtolower($project->status ?? '') === 'ditutup') {
            return response()->json([
                'success' => false,
                'message' => 'Project sudah ditutup.'
            ], 400);
        }

        return DB::transaction(function () use ($project, $id) {

            $totalAnggaran = DB::table('detail_subkategori')
                ->where('id_project', $id)
                ->sum('nominal');

            $totalRealisasi = DB::table('detail_subkategori')
                ->where('id_project', $id)
                ->sum('realisasi_anggaran');

            $sisa = (int) $totalAnggaran - (int) $totalRealisasi;

            $kasMasuk = 0;

            if ($sisa > 0) {
                $already = DB::table('kas_transactions')
                    ->where('project_id', $id)
                    ->where('tipe', 'masuk')
                    ->where('kategori', 'Sisa Project')
                    ->exists();

                if (!$already) {
                    DB::table('kas_transactions')->insert([
                        'tanggal'           => Carbon::now()->toDateString(),
                        'tipe'              => 'masuk',
                        'kategori'          => 'Sisa Project',
                        'project_id'        => $id,
                        'nominal'           => $sisa,
                        'deskripsi'         => 'Penutupan project: ' . ($project->nama_project ?? ('#' . $id)),
                        'metode_pembayaran' => '-',
                        'bukti'             => null,
                        'created_by'        => auth()->id(),
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }

                $kasMasuk = $sisa;
            }

            $project->status = 'ditutup';
            $project->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_masuk'  => (int) $totalAnggaran,
                    'total_keluar' => (int) $totalRealisasi,
                    'sisa'         => (int) $sisa,
                    'kas_masuk'    => (int) $kasMasuk
                ]
            ]);
        });
    }
}
