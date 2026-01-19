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
        $query = Project::with('sumberDana');

        if (Auth::user()->role === 'peneliti') {
            $query->where(function ($q) {
                $q->where('workflow_status', 'finalized')
                    ->orWhere('user_id_created', Auth::id()); // biar pengaju tetap bisa lihat prosesnya
            });
        }

        $projects = $query->orderByDesc('id')->get();

        $joinedProjectIds = [];

        if (strtolower(Auth::user()->role ?? '') !== 'admin') {
            $joinedProjectIds = DB::table('detail_project')
                ->where('id_user', Auth::id())
                ->pluck('id_project')
                ->map(fn ($v) => (int) $v)
                ->unique()
                ->toArray();
        }

        return view('project', compact('projects', 'joinedProjectIds'));
    }

    public function tracking()
    {
        $query = Project::with('sumberDana');

        if (Auth::user()->role === 'peneliti') {
            $query->where('user_id_created', Auth::id());
        }

        $projects = $query
            ->whereIn('workflow_status', ['submitted', 'approved', 'funded'])
            ->orderByDesc('id')
            ->get();

        return view('project.tracking', compact('projects'));
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

            DB::transaction(function () use (
                $request,
                $filename_proposal,
                $filename_rab,
                $sumberDanaId,
                $subkategori_sumberdana,
                &$project
            ) {
                $project = Project::create([
                    'tahun'           => $request->tahun,
                    'nama_project'    => $request->nama_project,
                    'id_sumber_dana'  => $sumberDanaId,
                    'durasi'          => $request->durasi,
                    'deskripsi'       => $request->deskripsi,
                    'file_proposal'   => $filename_proposal,
                    'file_rab'        => $filename_rab,

                    // ✅ workflow
                    'workflow_status' => 'submitted',
                    'submitted_at'    => now(),

                    // ✅ ketua default = pengaju
                    'ketua_id'        => Auth::id(),

                    'user_id_created' => Auth::id(),
                    'user_id_updated' => Auth::id(),
                ]);

                // ✅ AUTO: pengaju otomatis jadi anggota detail_project
                DB::table('detail_project')->updateOrInsert(
                    ['id_project' => $project->id, 'id_user' => Auth::id()],
                    [
                        'user_id_created' => Auth::id(),
                        'user_id_updated' => Auth::id(),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]
                );

                foreach ($subkategori_sumberdana as $subkategori) {
                    $nama_form = $subkategori->nama_form;

                    if ($request->has($nama_form)) {
                        $nominal = str_replace(['Rp.', '.', ','], ['', '', '.'], $request->$nama_form);
                        $nominal = (float) $nominal;

                        DetailSubkategori::create([
                            'nominal'                   => $nominal,
                            'id_subkategori_sumberdana' => $subkategori->id,
                            'id_project'                => $project->id,
                            'user_id_created'           => Auth::id(),
                            'user_id_updated'           => Auth::id(),
                        ]);
                    }
                }
            });

            return redirect()->route('project.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('project.index')->with('error', $e->getMessage());
        }
    }

    public function show(Project $project)
    {
        $project = Project::with('sumberDana')->findOrFail($project->id);

        $sumber_dana = null;
        if (!$project->sumberDana) {
            $sumber_dana = DB::table('sumber_dana')
                ->where('id', $project->id_sumber_dana)
                ->first();
        }

        $anggota = DB::table('detail_project as dp')
            ->join('users as u', 'dp.id_user', '=', 'u.id')
            ->where('dp.id_project', $project->id)
            ->select('u.id', 'u.name')
            ->orderBy('u.name')
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
                'a.anggaran_revisi',
                'a.realisasi_anggaran',
                'a.id',
                'c.jenis_pendanaan',
                'c.nama_sumber_dana'
            )
            ->get();

        // NOTE: jenis & nama sumber dana tetap dihitung (biar view kamu aman)
        $jenis_pendanaan = $project->sumberDana
            ? $project->sumberDana->jenis_pendanaan
            : ($sumber_dana ? $sumber_dana->jenis_pendanaan : 'internal');

        $nama_sumber_dana = $project->sumberDana
            ? $project->sumberDana->nama_sumber_dana
            : ($sumber_dana ? $sumber_dana->nama_sumber_dana : null);

        /**
         * ✅ PERBAIKAN UTAMA (TANPA MERUBAH YANG UDAH BENER)
         * Karena realisasi_anggaran kamu sudah benar di DB,
         * jangan ditambah lagi dengan total_request_pembelian.
         *
         * Jadi blok $detail_dana->map(...) yang sebelumnya "menambahkan" request,
         * dihapus/dimatikan.
         */

        $total_nominal   = (int) $detail_dana->sum('nominal');
        $total_realisasi = (int) $detail_dana->sum('realisasi_anggaran');

        // ✅ 1) Dana cair (limit) ambil dari tabel project_funding
        $fundedTotal = (int) DB::table('project_funding')
            ->where('project_id', $project->id)
            ->sum('nominal');

        // ✅ 2) penanda: revisi RAB udah pernah disimpan atau belum
        $hasRabSubmitted = DB::table('detail_subkategori')
            ->where('id_project', $project->id)
            ->whereNotNull('anggaran_revisi')
            ->whereRaw('anggaran_revisi <> nominal')
            ->exists();

        return view('detail_project', [
            'project' => $project,
            'anggota' => $anggota,
            'users' => $users,
            'detail_dana' => $detail_dana,
            'detail_request' => $detail_request,
            'total_request_pembelian' => $total_request_pembelian,
            'total_nominal' => $total_nominal,
            'total_realisasi' => $total_realisasi,
            'sumber_dana' => $project->sumberDana ?? $sumber_dana,

            // ✅ TAMBAHAN INI
            'fundedTotal' => $fundedTotal,
            'hasRabSubmitted' => $hasRabSubmitted,
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

    public function rabRevise(Project $project)
    {
        $fundedTotal = (int) DB::table('project_funding')
            ->where('project_id', $project->id)
            ->sum('nominal');

        // NOTE: ini query kamu—tetap dipakai (kalau memang kolomnya ada di tabel detail_subkategori)
        $detail_dana = DB::table('detail_subkategori')
            ->where('id_project', $project->id)
            ->select('id', 'nama_subkategori', 'nominal', 'realisasi_anggaran')
            ->get();

        $detail_request = DB::table('request_pembelian_detail')
            ->where('id_project', $project->id)
            ->select('nama_barang', 'kuantitas', 'harga', 'total')
            ->get();

        return view('detail_project', [
            'project' => $project,
            'detail_dana' => $detail_dana,
            'detail_request' => $detail_request,
            'fundedTotal' => $fundedTotal,
            'modeRabRevise' => true,
        ]);
    }

    public function rabReviseSave(Request $request, Project $project)
    {
        $fundedTotal = (int) DB::table('project_funding')
            ->where('project_id', $project->id)
            ->sum('nominal');

        $revisi = $request->input('revisi', []);
        $revisi = is_array($revisi) ? $revisi : [];

        $clean = [];
        $sum = 0;

        foreach ($revisi as $rowId => $val) {
            $n = (int) preg_replace('/[^0-9]/', '', (string) $val);
            $n = max(0, $n);
            $clean[(int) $rowId] = $n;
            $sum += $n;
        }

        if ($fundedTotal > 0 && $sum > $fundedTotal) {
            return redirect()
                ->route('project.show', $project->id)
                ->with('error', 'Total revisi tidak boleh melebihi dana cair. Total: Rp ' . number_format($sum, 0, ',', '.'));
        }

        DB::transaction(function () use ($clean, $project) {
            foreach ($clean as $rowId => $n) {
                DB::table('detail_subkategori')
                    ->where('id_project', $project->id)
                    ->where('id', $rowId)
                    ->update([
                        'anggaran_revisi' => $n,
                        'updated_at' => now(),
                    ]);
            }
        });

        return redirect()
            ->route('project.show', $project->id)
            ->with('success', 'Revisi RAB berhasil disimpan. Menunggu finalisasi admin/bendahara.');
    }

    public function setKetua(Request $request, Project $project)
    {
        $role = strtolower(Auth::user()->role ?? '');
        $isAdmin = ($role === 'admin');

        $creatorId = (int)($project->user_id_created ?? 0);
        $isCreator = $creatorId > 0 ? ((int)Auth::id() === $creatorId) : false;

        $isClosed = (strtolower($project->status ?? 'aktif') === 'ditutup');
        $wf = strtolower($project->workflow_status ?? '');
        $isFinalized = ($wf === 'finalized');

        if ($isClosed) {
            return back()->with('error', 'Project sudah ditutup, ketua tidak bisa diubah.');
        }

        // Yang boleh ganti: admin atau pengaju/creator
        if (!$isAdmin && !$isCreator) {
            return back()->with('error', 'Kamu tidak punya akses untuk mengganti ketua.');
        }

        $request->validate([
            'ketua_id' => 'required|integer'
        ]);

        $ketuaId = (int) $request->ketua_id;

        // Validasi: ketua harus anggota di detail_project
        $isMember = DB::table('detail_project')
            ->where('id_project', $project->id)
            ->where('id_user', $ketuaId)
            ->exists();

        if (!$isMember) {
            return back()->with('error', 'Ketua harus dipilih dari anggota project.');
        }

        $project->ketua_id = $ketuaId;
        $project->user_id_updated = Auth::id();
        $project->save();

        return back()->with('success', 'Ketua project berhasil diperbarui.');
    }

    public function removeMember(Project $project, User $user)
    {
        $role = strtolower(auth()->user()->role ?? '');
        $isAdmin = $role === 'admin';

        $creatorId = (int)($project->user_id_created ?? $project->created_by ?? 0);
        $isCreator = (int)auth()->id() === $creatorId;

        if (!$isAdmin && !$isCreator) {
            return back()->with('error', 'Kamu tidak punya akses menghapus anggota.');
        }

        if (strtolower($project->status ?? 'aktif') === 'ditutup') {
            return back()->with('error', 'Project sudah ditutup. Anggota tidak bisa dihapus.');
        }

        // jangan boleh hapus ketua
        if ((int)($project->ketua_id ?? 0) === (int)$user->id) {
            return back()->with('error', 'Ketua tidak bisa dihapus. Ganti ketua dulu.');
        }

        DB::table('detail_project')
            ->where('id_project', $project->id)
            ->where('id_user', $user->id)
            ->delete();

        return back()->with('success', 'Anggota berhasil dihapus dari project.');
    }
}
