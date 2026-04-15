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
use Illuminate\Support\Facades\Schema;

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
            'sumber_eksternal' => $sumber_eksternal,
        ]);
    }

    /**
     * API: Return sumber dana filtered by tipe_project (JSON).
     */
    public function getSumberDanaByTipe($tipe)
    {
        $data = Sumberdana::where('tipe_project', $tipe)
            ->select('id', 'tipe_project', 'jenis_pendanaan', 'nama_sumber_dana')
            ->orderBy('jenis_pendanaan')
            ->orderBy('nama_sumber_dana')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe_project'  => 'required|in:Penelitian,Abdimas',
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

            // ✅ Validasi backend: pastikan tipe_project sumber dana cocok
            $sumberDana = Sumberdana::findOrFail($sumberDanaId);
            if ($sumberDana->tipe_project !== $request->tipe_project) {
                return redirect()->back()->withInput()->with('error', 'Sumber dana yang dipilih tidak sesuai dengan tipe project.');
            }

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
                    'tipe_project'    => $request->tipe_project,
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

        $hasUserProfilePhotoCol = Schema::hasColumn('users', 'profile_photo');
        $anggotaSelects = ['u.id', 'u.name'];
        if ($hasUserProfilePhotoCol) {
            $anggotaSelects[] = 'u.profile_photo';
        }

        $anggota = DB::table('detail_project as dp')
            ->join('users as u', 'dp.id_user', '=', 'u.id')
            ->where('dp.id_project', $project->id)
            ->select($anggotaSelects)
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

        $hasTalanganAllocationCols = Schema::hasColumn('request_pembelian_header', 'project_id_alokasi_final')
            && Schema::hasColumn('request_pembelian_header', 'status_alokasi');

        $totalRequestQuery = DB::table('request_pembelian_detail as a')
            ->leftJoin('request_pembelian_header as b', 'a.id_request_pembelian_header', '=', 'b.id')
            ->where('b.status_request', 'done');

        if ($hasTalanganAllocationCols) {
            $totalRequestQuery->where(function ($w) use ($project) {
                $w->where(function ($q) use ($project) {
                    $q->where('b.id_project', $project->id)
                        ->where(function ($x) {
                            $x->whereNull('b.project_id_alokasi_final')
                                ->orWhere('b.status_alokasi', '!=', 'sudah');
                        });
                })->orWhere(function ($q) use ($project) {
                    $q->where('b.project_id_alokasi_final', $project->id)
                        ->where('b.status_alokasi', 'sudah');
                });
            });
        } else {
            $totalRequestQuery->where('b.id_project', $project->id);
        }

        $total_request_pembelian = $totalRequestQuery->sum(DB::raw('COALESCE(a.total_invoice, (a.kuantitas * a.harga))'));

        $totalBiayaAdminQuery = DB::table('request_pembelian_header')
            ->where('status_request', 'done');

        if ($hasTalanganAllocationCols) {
            $totalBiayaAdminQuery->where(function ($w) use ($project) {
                $w->where(function ($q) use ($project) {
                    $q->where('id_project', $project->id)
                        ->where(function ($x) {
                            $x->whereNull('project_id_alokasi_final')
                                ->orWhere('status_alokasi', '!=', 'sudah');
                        });
                })->orWhere(function ($q) use ($project) {
                    $q->where('project_id_alokasi_final', $project->id)
                        ->where('status_alokasi', 'sudah');
                });
            });
        } else {
            $totalBiayaAdminQuery->where('id_project', $project->id);
        }

        $total_biaya_admin_dana = (int) $totalBiayaAdminQuery->sum(DB::raw('COALESCE(biaya_admin_transfer, 0)'));

        $detailRequestQuery = DB::table('request_pembelian_detail as a')
            ->leftJoin('request_pembelian_header as b', 'a.id_request_pembelian_header', '=', 'b.id')
            ->leftJoin('subkategori_sumberdana as s', 'a.id_subkategori_sumberdana', '=', 's.id')
            ->where('b.status_request', 'done')
            ->select(
                'b.tgl_request',
                'b.no_request',
                'b.biaya_admin_transfer',
                'a.nama_barang',
                'a.kuantitas',
                'a.harga',
                'a.link_pembelian',
                'a.total_invoice',
                DB::raw("COALESCE(s.nama, 'Bahan Habis Pakai dan Peralatan') as subkategori_nama"),
                DB::raw('COALESCE(a.total_invoice, (a.kuantitas * a.harga)) as total')
            )
            ->orderByRaw("COALESCE(s.nama, 'Bahan Habis Pakai dan Peralatan')")
            ->orderBy('b.tgl_request')
            ->orderBy('a.id');

        if ($hasTalanganAllocationCols) {
            $detailRequestQuery->where(function ($w) use ($project) {
                $w->where(function ($q) use ($project) {
                    $q->where('b.id_project', $project->id)
                        ->where(function ($x) {
                            $x->whereNull('b.project_id_alokasi_final')
                                ->orWhere('b.status_alokasi', '!=', 'sudah');
                        });
                })->orWhere(function ($q) use ($project) {
                    $q->where('b.project_id_alokasi_final', $project->id)
                        ->where('b.status_alokasi', 'sudah');
                });
            });
        } else {
            $detailRequestQuery->where('b.id_project', $project->id);
        }

        $detail_request = $detailRequestQuery->get();

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
                'a.id_subkategori_sumberdana',
                'c.jenis_pendanaan',
                'c.nama_sumber_dana'
            )
            ->get();

        // Realisasi untuk request pembelian harus pakai nilai invoice fix (bukan estimasi).
        $realisasiRequestQuery = DB::table('request_pembelian_detail as d')
            ->join('request_pembelian_header as h', 'd.id_request_pembelian_header', '=', 'h.id')
            ->where('h.status_request', 'done')
            ->select(
                'd.id_subkategori_sumberdana',
                DB::raw('SUM(COALESCE(d.total_invoice, (d.kuantitas * d.harga))) as total_realisasi_invoice')
            )
            ->groupBy('d.id_subkategori_sumberdana');

        if ($hasTalanganAllocationCols) {
            $realisasiRequestQuery->where(function ($w) use ($project) {
                $w->where(function ($q) use ($project) {
                    $q->where('h.id_project', $project->id)
                        ->where(function ($x) {
                            $x->whereNull('h.project_id_alokasi_final')
                                ->orWhere('h.status_alokasi', '!=', 'sudah');
                        });
                })->orWhere(function ($q) use ($project) {
                    $q->where('h.project_id_alokasi_final', $project->id)
                        ->where('h.status_alokasi', 'sudah');
                });
            });
        } else {
            $realisasiRequestQuery->where('h.id_project', $project->id);
        }

        $realisasiRequestInvoiceBySubkategori = $realisasiRequestQuery->pluck('total_realisasi_invoice', 'id_subkategori_sumberdana');

        // Tambahkan transaksi manual (di luar request pembelian) agar nilai realisasi tetap utuh.
        $realisasiManualBySubkategori = DB::table('pencatatan_keuangan')
            ->where('project_id', $project->id)
            ->where('jenis_transaksi', 'pengeluaran')
            ->whereNull('request_pembelian_id')
            ->whereNotNull('sub_kategori_pendanaan')
            ->select(
                'sub_kategori_pendanaan',
                DB::raw('SUM(COALESCE(jumlah_transaksi, 0)) as total_manual')
            )
            ->groupBy('sub_kategori_pendanaan')
            ->pluck('total_manual', 'sub_kategori_pendanaan');

        $detail_dana = $detail_dana->map(function ($row) use ($realisasiRequestInvoiceBySubkategori, $realisasiManualBySubkategori) {
            $subkategoriId = (int) ($row->id_subkategori_sumberdana ?? 0);
            $realisasiRequest = (float) ($realisasiRequestInvoiceBySubkategori[$subkategoriId] ?? 0);
            $realisasiManual = (float) ($realisasiManualBySubkategori[$subkategoriId] ?? 0);
            $row->realisasi_anggaran = (int) round($realisasiRequest + $realisasiManual);
            return $row;
        });

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
            'total_biaya_admin_dana' => $total_biaya_admin_dana,
            'total_nominal' => $total_nominal,
            'total_realisasi' => $total_realisasi,
            'sumber_dana' => $project->sumberDana ?? $sumber_dana,

            // ✅ TAMBAHAN INI
            'fundedTotal' => $fundedTotal,
            'hasRabSubmitted' => $hasRabSubmitted,
        ]);
    }

    public function exportDetailPembelianExcel(Project $project)
    {
        $project = Project::findOrFail($project->id);

        $hasTalanganAllocationCols = Schema::hasColumn('request_pembelian_header', 'project_id_alokasi_final')
            && Schema::hasColumn('request_pembelian_header', 'status_alokasi');

        $detailRequestQuery = DB::table('request_pembelian_detail as a')
            ->leftJoin('request_pembelian_header as b', 'a.id_request_pembelian_header', '=', 'b.id')
            ->leftJoin('subkategori_sumberdana as s', 'a.id_subkategori_sumberdana', '=', 's.id')
            ->where('b.status_request', 'done')
            ->select(
                'b.tgl_request',
                'b.no_request',
                'b.biaya_admin_transfer',
                'a.nama_barang',
                'a.kuantitas',
                'a.harga',
                'a.link_pembelian',
                'a.total_invoice',
                DB::raw("COALESCE(s.nama, 'Bahan Habis Pakai dan Peralatan') as subkategori_nama"),
                DB::raw('COALESCE(a.total_invoice, (a.kuantitas * a.harga)) as total')
            )
            ->orderByRaw("COALESCE(s.nama, 'Bahan Habis Pakai dan Peralatan')")
            ->orderBy('b.tgl_request')
            ->orderBy('a.id');

        if ($hasTalanganAllocationCols) {
            $detailRequestQuery->where(function ($w) use ($project) {
                $w->where(function ($q) use ($project) {
                    $q->where('b.id_project', $project->id)
                        ->where(function ($x) {
                            $x->whereNull('b.project_id_alokasi_final')
                                ->orWhere('b.status_alokasi', '!=', 'sudah');
                        });
                })->orWhere(function ($q) use ($project) {
                    $q->where('b.project_id_alokasi_final', $project->id)
                        ->where('b.status_alokasi', 'sudah');
                });
            });
        } else {
            $detailRequestQuery->where('b.id_project', $project->id);
        }

        $detailRequest = $detailRequestQuery->get();

        $totalInvoice = 0;
        $totalBiayaAdmin = 0;
        $adminByRequest = [];
        $currentSection = null;
        $sectionNo = 0;
        $no = 1;

        $e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

        $rowsHtml = '';
        foreach ($detailRequest as $dr) {
            $sectionName = $dr->subkategori_nama ?? 'Bahan Habis Pakai dan Peralatan';
            if ($currentSection !== $sectionName) {
                $currentSection = $sectionName;
                $sectionNo++;
                $sectionPrefix = chr(64 + $sectionNo);
                $rowsHtml .= '<tr><td colspan="8" style="background:#fff700;font-weight:700;">'
                    . $e($sectionPrefix . '. ' . $sectionName) . '</td></tr>';
            }

            $qty = max(1, (int) ($dr->kuantitas ?? 0));
            $jumlahFix = (int) ($dr->total ?? 0);
            $hargaSatuanFix = (int) round($jumlahFix / $qty);
            $totalInvoice += $jumlahFix;

            $tgl = !empty($dr->tgl_request) ? Carbon::parse($dr->tgl_request)->format('d/m/Y') : '-';
            $link = !empty($dr->link_pembelian) ? $dr->link_pembelian : '-';

            $rowsHtml .= '<tr>'
                . '<td style="text-align:center;">' . $no++ . '</td>'
                . '<td style="text-align:center;">' . $e($tgl) . '</td>'
                . '<td>' . $e($dr->nama_barang ?? '-') . '</td>'
                . '<td style="text-align:center;">' . $qty . '</td>'
                . '<td style="text-align:right;">' . number_format($hargaSatuanFix, 0, ',', '.') . '</td>'
                . '<td style="text-align:center;">item</td>'
                . '<td style="text-align:right;">' . number_format($jumlahFix, 0, ',', '.') . '</td>'
                . '<td>' . $e($link) . '</td>'
                . '</tr>';

            $reqNo = (string) ($dr->no_request ?? '');
            if ($reqNo !== '') {
                if (!isset($adminByRequest[$reqNo])) {
                    $adminByRequest[$reqNo] = [
                        'date' => $dr->tgl_request ?? null,
                        'admin' => (int) ($dr->biaya_admin_transfer ?? 0),
                        'items' => [],
                    ];
                    $totalBiayaAdmin += (int) ($dr->biaya_admin_transfer ?? 0);
                }
                if (!in_array($dr->nama_barang, $adminByRequest[$reqNo]['items'], true)) {
                    $adminByRequest[$reqNo]['items'][] = $dr->nama_barang;
                }
            }
        }

        $summaryHtml = ''
            . '<tr><td colspan="6" style="font-weight:700;">Total Invoice Pembelian</td><td style="text-align:right;font-weight:700;">'
            . number_format($totalInvoice, 0, ',', '.') . '</td><td></td></tr>'
            . '<tr><td colspan="6" style="font-weight:700;">Total Biaya Admin</td><td style="text-align:right;font-weight:700;">'
            . number_format($totalBiayaAdmin, 0, ',', '.') . '</td><td></td></tr>'
            . '<tr><td colspan="6" style="font-weight:700;">Total Pembelian + Biaya Admin</td><td style="text-align:right;font-weight:700;">'
            . number_format($totalInvoice + $totalBiayaAdmin, 0, ',', '.') . '</td><td></td></tr>';

        $adminRowsHtml = '';
        $adminNotes = collect($adminByRequest)->filter(fn($v) => (int) ($v['admin'] ?? 0) > 0);
        if ($adminNotes->count() > 0) {
            $n = 1;
            foreach ($adminNotes as $reqNo => $note) {
                $tgl = !empty($note['date']) ? Carbon::parse($note['date'])->format('d/m/Y') : '-';
                $adminRowsHtml .= '<tr>'
                    . '<td style="text-align:center;">' . $n++ . '</td>'
                    . '<td style="text-align:center;">' . $e($tgl) . '</td>'
                    . '<td>' . $e(implode(', ', $note['items'] ?? [])) . '</td>'
                    . '<td>' . $e($reqNo) . '</td>'
                    . '<td style="text-align:right;">' . number_format((int) ($note['admin'] ?? 0), 0, ',', '.') . '</td>'
                    . '</tr>';
            }
        }

        $title = 'Detail Pembelian - ' . ($project->nama_project ?? 'Project');
        $html = '<html><head><meta charset="UTF-8"><style>'
            . 'body{font-family:Calibri,Arial,sans-serif;font-size:11pt;color:#111;}'
            . 'h2{margin:0 0 4px 0;} .meta{margin:0 0 12px 0;color:#555;}'
            . 'table{border-collapse:collapse;width:100%;margin-bottom:14px;}'
            . 'th,td{border:1px solid #cfd8e3;padding:6px;vertical-align:middle;}'
            . 'th{background:#eef2f7;text-align:center;font-weight:700;}'
            . '</style></head><body>'
            . '<h2>' . $e($title) . '</h2>'
            . '<div class="meta">Diekspor pada: ' . now()->format('d/m/Y H:i') . '</div>'
            . '<table>'
            . '<thead><tr>'
            . '<th style="width:45px;">No</th>'
            . '<th style="width:95px;">Tanggal</th>'
            . '<th>Keterangan (Pembelian)</th>'
            . '<th style="width:70px;">Volume</th>'
            . '<th style="width:120px;">Harga Satuan (Rp)</th>'
            . '<th style="width:70px;">Satuan</th>'
            . '<th style="width:120px;">Jumlah (Rp)</th>'
            . '<th style="width:180px;">Link Evidence</th>'
            . '</tr></thead><tbody>'
            . ($rowsHtml !== '' ? $rowsHtml : '<tr><td colspan="8" style="text-align:center;">Belum ada request pembelian.</td></tr>')
            . $summaryHtml
            . '</tbody></table>';

        if ($adminRowsHtml !== '') {
            $html .= '<table><thead><tr>'
                . '<th style="width:45px;">No</th>'
                . '<th style="width:95px;">Tanggal</th>'
                . '<th>Item Terkait</th>'
                . '<th style="width:180px;">No Request</th>'
                . '<th style="width:120px;">Biaya Admin (Rp)</th>'
                . '</tr></thead><tbody>'
                . $adminRowsHtml
                . '</tbody></table>';
        }

        $html .= '</body></html>';

        $filenameSafe = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) ($project->nama_project ?? 'project'));
        $filename = 'detail_pembelian_' . $filenameSafe . '_' . now()->format('Ymd_His') . '.xls';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
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

        // Filter berdasarkan tipe_project yang dimiliki project ini
        $tipe = $project->tipe_project ?? 'Penelitian';
        $sumber_internal  = Sumberdana::where('jenis_pendanaan', 'internal')->where('tipe_project', $tipe)->get();
        $sumber_eksternal = Sumberdana::where('jenis_pendanaan', 'eksternal')->where('tipe_project', $tipe)->get();

        return view('input_project', compact('project', 'sumber_internal', 'sumber_eksternal'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'tipe_project' => 'required|in:Penelitian,Abdimas',
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

        // ✅ Validasi backend: pastikan tipe_project sumber dana cocok
        $sumberDana = Sumberdana::findOrFail($new_sumber_dana_id);
        if ($sumberDana->tipe_project !== $request->tipe_project) {
            return redirect()->back()->withInput()->with('error', 'Sumber dana yang dipilih tidak sesuai dengan tipe project.');
        }

        $old_sumber_dana_id = $project->id_sumber_dana;

        $project->tipe_project    = $request->tipe_project;
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
        $role = strtolower(auth()->user()->role ?? '');

        if ($role !== 'admin' && $role !== 'bendahara') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menutup project.'
            ], 403);
        }

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
