<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detail Project</title>

  <!-- Fonts, Bootstrap, Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Tom Select -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0;
      --bg:#f6f7fb; --card:#ffffff;
      --shadow:0 10px 30px rgba(15,23,42,.08);
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      background:var(--bg);
      font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      color:var(--ink);
    }

    /* Topbar */
    .topbar{
      position:sticky; top:0; z-index:1030;
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff;
      border-bottom:1px solid rgba(255,255,255,.18);
      height:56px;
    }
    .brand{display:flex;align-items:center;gap:10px;font-weight:800;letter-spacing:.2px}
    .brand-badge{
      font-size:.72rem;font-weight:800;
      padding:.22rem .55rem;border-radius:999px;
      background:rgba(255,255,255,.16);
      border:1px solid rgba(255,255,255,.22);
      white-space:nowrap;
    }

    /* Layout */
    .app{display:flex;min-height:calc(100vh - 56px)}
    .sidebar{
      width:260px;background:var(--card);
      border-right:1px solid var(--line);
      padding:14px;
      position:sticky; top:56px;
      height:calc(100vh - 56px);
      overflow:auto;
    }
    .menu-title{
      font-size:.72rem;letter-spacing:.08em;color:var(--ink-600);
      text-transform:uppercase;margin:8px 0;font-weight:700;
    }
    .nav-link-custom{
      display:flex;align-items:center;gap:10px;
      padding:9px 10px;border-radius:14px;text-decoration:none;
      color:var(--ink);font-weight:600;font-size:.92rem;line-height:1;
      transition:.18s;white-space:nowrap;
    }
    .nav-link-custom i{font-size:1.05rem}
    .nav-link-custom:hover{background:var(--brand-50);color:var(--brand-700);transform:translateX(2px)}
    .nav-link-custom.active{
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff;box-shadow:0 16px 28px rgba(2,6,23,.12);font-weight:700;
    }

    .content{flex:1;padding:18px 18px 22px;min-width:0}
    .main-inner{width:100%;max-width:100%}

    /* Hero */
    .hero{
      border-radius:22px;padding:18px;
      background:
        radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
        radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
        linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
      border:1px solid rgba(226,232,240,.95);
      box-shadow:var(--shadow);
      position:relative;overflow:hidden;margin-bottom:14px;
    }
    .hero::after{
      content:"";position:absolute;inset:-1px;
      background:
        radial-gradient(600px 160px at 12% 0%, rgba(22,163,74,.18), transparent 55%),
        radial-gradient(500px 160px at 95% 0%, rgba(22,163,74,.10), transparent 55%);
      pointer-events:none;opacity:.65;
    }
    .hero-inner{position:relative;z-index:2;width:100%}
    .hero-left .title{font-size:1.65rem;font-weight:900;margin:0;letter-spacing:-.2px}
    .hero-left .sub{margin:6px 0 0;color:var(--ink-600);font-weight:500}

    .tools-row{margin-top:14px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
    .tools-right{margin-left:auto;display:flex;align-items:center;gap:10px;flex-wrap:wrap}

    .btn-manual{
      height:38px;display:inline-flex;align-items:center;gap:8px;
      border-radius:999px;font-weight:800;padding:0 14px;
      background:#fff;color:var(--ink);
      border:1px solid rgba(226,232,240,.95);
      box-shadow:0 10px 26px rgba(15,23,42,.05);
      white-space:nowrap;text-decoration:none;
    }
    .btn-manual:hover{background:var(--brand-50);transform:translateY(-1px);color:var(--brand-700)}

    /* Cards */
    .card{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      box-shadow:var(--shadow);
    }
    .card h5{font-weight:900}
    .subtle{color:var(--ink-600)}
    .label-sm{font-size:.72rem;letter-spacing:.08em;color:var(--ink-600);text-transform:uppercase;font-weight:900}
    .value-lg{font-weight:900}

    /* Status badge */
    .badge-proj{
      display:inline-flex;align-items:center;gap:6px;
      padding:.35rem .55rem;border-radius:10px;font-weight:900;border:1px solid transparent;
      white-space:nowrap;
    }
    .badge-proj-planned{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe}
    .badge-proj-on-going{background:#ecfdf5;color:#166534;border-color:#bbf7d0}
    .badge-proj-paused{background:#fff7ed;color:#9a3412;border-color:#fed7aa}
    .badge-proj-cancelled{background:#fef2f2;color:#b91c1c;border-color:#fecaca}
    .badge-proj-completed{background:#eefdfb;color:#0f766e;border-color:#99f6e4}
    .badge-proj-closed{background:#f1f5f9;color:#0f172a;border-color:#cbd5e1}

    /* Tables */
    .table-modern{
      margin:0;font-size:.92rem;
      border-collapse:separate;border-spacing:0;
      overflow:hidden;border-radius:16px;
      border:1px solid rgba(226,232,240,.95);
    }
    .table-modern thead th{
      background:#f8fafc;color:var(--ink-600);
      font-weight:900;text-transform:uppercase;
      font-size:.72rem;letter-spacing:.08em;
      padding:14px 12px;
      border-bottom:1px solid rgba(226,232,240,.95);
      position:sticky;top:0;z-index:1;
      white-space:nowrap;
    }
    .table-modern tbody td{
      padding:14px 12px;
      vertical-align:middle;
      border-top:1px solid #eef2f7;
      font-weight:600;
    }
    .table-modern tbody tr:hover{background:var(--brand-50);transition:.12s}
    .tnum{font-variant-numeric:tabular-nums}

    .tr-over{background:#fef2f2}
    .tr-near{background:#fff7ed}

    /* Anggota chips */
    .chips-scroll{max-height:160px;overflow:auto;padding-right:6px}
    .chips-wrap{display:flex;flex-wrap:wrap;gap:6px}
    .chip{
      background:#f1f5f9;border:1px solid #e2e8f0;color:#0f172a;
      border-radius:999px;padding:6px 10px;font-weight:800;
    }

    /* TomSelect + btn */
    .ts-wrapper{width:100%}
    .ts-wrapper .ts-control{min-height:40px}
    .btn-eq{
      height:40px;
      display:inline-flex;align-items:center;justify-content:center;
      border-radius:14px;font-weight:900;
    }

    /* Mobile sidebar */
    .backdrop{display:none;position:fixed;inset:0;background:rgba(15,23,42,.38);z-index:1035}
    .backdrop.show{display:block}
    @media (max-width:991.98px){
      .sidebar{position:fixed;left:-290px;top:56px;height:calc(100vh - 56px);z-index:1040;transition:left .2s}
      .sidebar.open{left:0}
      .content{padding:14px}
    }

    /* Print */
    @media print{
      .topbar,.sidebar,.hero,.backdrop{display:none !important}
      .content{padding:0}
      .card{box-shadow:none;border:1px solid #ccc}
    }
  </style>
</head>

<body>
  <!-- Topbar -->
  <nav class="navbar topbar">
    <div class="container-fluid">
      <button class="btn btn-outline-light d-lg-none me-2" id="sidebarToggle" aria-label="Buka tutup sidebar">
        <i class="bi bi-list"></i>
      </button>

      @php $isAdmin = strtolower(Auth::user()->role ?? '') === 'admin'; @endphp

      <div class="brand">
        <span>STAS-RG</span>
        <span class="brand-badge">{{ $isAdmin ? 'ADMIN' : 'PENELITI' }}</span>
      </div>

      <div class="ms-auto">@include('navbar')</div>
    </div>
  </nav>

  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="appSidebar">
      <div class="menu-title">Menu</div>

      <a class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>

      <a class="nav-link-custom {{ request()->routeIs('project.*') ? 'active' : '' }}" href="{{ route('project.index') }}">
        <i class="bi bi-kanban"></i> Project
      </a>

      <a class="nav-link-custom {{ request()->routeIs('requestpembelian.*') ? 'active' : '' }}" href="{{ route('requestpembelian.index') }}">
        <i class="bi bi-bag-check"></i> Request Pembelian
      </a>

      @if ($isAdmin)
        <div class="menu-title mt-3">Administrasi</div>

        <a class="nav-link-custom {{ request()->routeIs('sumberdana.*') ? 'active' : '' }}" href="{{ route('sumberdana.index') }}">
          <i class="bi bi-cash-coin"></i> Sumber Dana
        </a>

        <a class="nav-link-custom {{ request()->routeIs('kas.*') ? 'active' : '' }}" href="{{ route('kas.index') }}">
          <i class="bi bi-wallet2"></i> Kas
        </a>

        <a class="nav-link-custom {{ request()->routeIs('pencatatan_keuangan') ? 'active' : '' }}" href="{{ route('pencatatan_keuangan') }}">
          <i class="bi bi-journal-text"></i> Pencatatan Keuangan
        </a>

        <a class="nav-link-custom {{ request()->routeIs('laporan_keuangan') ? 'active' : '' }}" href="{{ route('laporan_keuangan') }}">
          <i class="bi bi-graph-up"></i> Laporan Keuangan
        </a>

        <a class="nav-link-custom {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
          <i class="bi bi-people"></i> Management User
        </a>
      @endif
    </aside>

    <div class="backdrop" id="backdrop"></div>

    <!-- Content -->
    <main class="content">
      <div class="main-inner">

        @if ($message = Session::get('success'))
          <div class="alert alert-success">{{ $message }}</div>
        @endif
        @if ($message = Session::get('error'))
          <div class="alert alert-danger">{{ $message }}</div>
        @endif

        <!-- HERO -->
        <section class="hero">
          <div class="hero-inner">
            <div class="hero-left">
              <h1 class="title">Detail Project</h1>
              <p class="sub">Ringkasan proyek, anggota tim, pendanaan, anggaran, dan detail pembelian.</p>
            </div>

            <div class="tools-row">
              <div class="tools-right">
                <a class="btn-manual"
                   href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
                   target="_blank" rel="noopener">
                  <i class="bi bi-book"></i> Manual Book
                </a>
              </div>
            </div>
          </div>
        </section>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-2">
          <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Project</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail</li>
          </ol>
        </nav>

        <!-- Header -->
        @php
          $statusRaw = strtolower($project->status_project ?? $project->status ?? 'on_going');

          $statusMap = [
            'planned'   => ['cls'=>'badge-proj-planned',   'label'=>'Planned'],
            'on_going'  => ['cls'=>'badge-proj-on-going',  'label'=>'On Going'],
            'ongoing'   => ['cls'=>'badge-proj-on-going',  'label'=>'On Going'],
            'paused'    => ['cls'=>'badge-proj-paused',    'label'=>'Paused'],
            'cancelled' => ['cls'=>'badge-proj-cancelled', 'label'=>'Cancelled'],
            'canceled'  => ['cls'=>'badge-proj-cancelled', 'label'=>'Cancelled'],
            'completed' => ['cls'=>'badge-proj-completed', 'label'=>'Completed'],
            'done'      => ['cls'=>'badge-proj-completed', 'label'=>'Completed'],
            'ditutup'   => ['cls'=>'badge-proj-closed',    'label'=>'Ditutup'],
            'closed'    => ['cls'=>'badge-proj-closed',    'label'=>'Ditutup'],
            'aktif'     => ['cls'=>'badge-proj-on-going',  'label'=>'Aktif'],
          ];

          $projBadge = $statusMap[$statusRaw]['cls'] ?? 'badge-proj-on-going';
          $projLabel = $statusMap[$statusRaw]['label'] ?? ucwords(str_replace('_',' ', $statusRaw));

          $isClosed = (strtolower($project->status ?? 'aktif') === 'ditutup');
        @endphp

        <div class="d-flex flex-wrap align-items-baseline justify-content-between mb-1">
          <div class="mb-2">
            <div class="h3 mb-1" style="font-weight:900">{{ $project->nama_project }}</div>
            <div class="subtle">
              Ringkasan proyek & anggaran • Diupdate {{ \Carbon\Carbon::parse($project->updated_at ?? now())->diffForHumans() }}
            </div>
          </div>
          <span class="badge-proj {{ $projBadge }}">{{ $projLabel }}</span>
        </div>

        <!-- Row 1 -->
        <div class="row g-3 mt-1">
          <div class="col-lg-8">
            <div class="card p-3 h-100">
              <h5 class="mb-2 text-center">Deskripsi Project</h5>

              @php
                $plain = trim(preg_replace('/\s+/u',' ', strip_tags($project->deskripsi ?? '')));
                $wordLimit = 60;
                $words = preg_split('/\s+/u', $plain, -1, PREG_SPLIT_NO_EMPTY);
                $snippet = implode(' ', array_slice($words, 0, $wordLimit));
                $isTrimmed = count($words) > $wordLimit;
              @endphp

              <p class="mb-2">{{ $snippet }}@if($isTrimmed)…@endif</p>

              <h5 class="mt-3 mb-2 text-center">Anggota Tim Riset</h5>

              @php $anggotaCount = count($anggota ?? []); @endphp

              @if($anggotaCount === 0)
                <div class="text-center subtle py-4">Belum ada anggota tim.</div>
              @else
                <div class="chips-scroll">
                  <div class="chips-wrap">
                    @foreach($anggota as $a)
                      <span class="chip" title="{{ $a->name }}">{{ $a->name }}</span>
                    @endforeach
                  </div>
                </div>
                <div class="small subtle mt-1">Total: {{ $anggotaCount }} orang</div>
              @endif

              @if ($isAdmin)
                <div class="mt-3">
                  <form id="addMemberForm" action="{{ route('detailproject.store') }}" method="POST" class="row g-2 align-items-end">
                    @csrf
                    <input type="hidden" name="id_project" value="{{ $project->id }}">

                    <div class="col-md-9">
                      <label for="id_user" class="form-label mb-1">Tambah Anggota (Search)</label>
                      <select id="id_user" name="id_user" class="form-select"
                              placeholder="Ketik nama untuk mencari…" autocomplete="off"
                              data-url="{{ \Illuminate\Support\Facades\Route::has('users.search') ? route('users.search') : '' }}">
                        @foreach ($users as $user)
                          <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-3">
                      <button type="submit" class="btn btn-success w-100 btn-eq">Tambah</button>
                    </div>

                    <div class="col-12">
                      <div class="form-text">Cari nama. Support pencarian server-side jika route <code>users.search</code> tersedia.</div>
                    </div>
                  </form>
                </div>
              @endif
            </div>
          </div>

          <div class="col-lg-4 d-flex flex-column gap-3">
            <!-- Aksi -->
            <div class="card p-3">
              <h5 class="mb-3">Aksi</h5>

              <div class="d-grid gap-2">
                <a class="btn btn-outline-secondary btn-eq" href="{{ route('project.downloadproposal', $project->id) }}">
                  <i class="bi bi-download me-1"></i> Download Proposal
                </a>

                <a class="btn btn-outline-secondary btn-eq" href="{{ route('project.downloadrab', $project->id) }}">
                  <i class="bi bi-download me-1"></i> Download RAB
                </a>

                @if($isAdmin)
                  @if(!$isClosed)
                    <button type="button"
                      class="btn btn-danger btn-eq js-close-project"
                      data-id="{{ $project->id }}"
                      data-nama="{{ $project->nama_project }}">
                      <i class="bi bi-lock-fill me-1"></i> Tutup Project
                    </button>

                    <small class="subtle">
                      Saat ditutup, sistem menghitung sisa dana dan otomatis memasukkannya ke Kas (jika ada).
                    </small>
                  @else
                    <div class="alert alert-secondary mb-0">
                      <strong><i class="bi bi-check-circle me-1"></i> Project sudah ditutup.</strong><br>
                      Transaksi project akan terkunci (read-only).
                    </div>
                  @endif
                @endif
              </div>
            </div>

            <!-- Pendanaan -->
            @php
              $sdObj   = $project->sumberDana ?? ($sumber_dana ?? null);
              $sdJenis = $sdObj ? ucfirst(strtolower($sdObj->jenis_pendanaan ?? 'Internal')) : 'Internal';
              $sdNama  = $sdObj->nama_sumber_dana ?? 'Tidak tersedia';
            @endphp

            <div class="card p-3">
              <h5 class="mb-3">Pendanaan</h5>
              <div class="row g-2">
                <div class="col-6">
                  <div class="label-sm">Sumber Dana</div>
                  <div class="value-lg">{{ $sdJenis }}</div>
                </div>
                <div class="col-6">
                  <div class="label-sm">Kategori</div>
                  <div class="value-lg">{{ $sdNama }}</div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Detail Dana -->
        <div class="row g-3 mt-1">
          <div class="col-12">
            <div class="card p-3">
              <h5 class="text-center">Detail Dana</h5>

              <div class="table-responsive">
                <table class="table table-modern table-striped align-middle mt-3" width="100%">
                  <thead>
                    <tr>
                      <th class="text-center">Sub Kategori</th>
                      <th class="text-center">Rincian Anggaran</th>
                      <th class="text-center">Realisasi Anggaran</th>
                      <th class="text-center">Sisa Anggaran</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $total_nominal_local = 0; $total_realisasi_local = 0; @endphp

                    @forelse ($detail_dana as $dd)
                      @php
                        $sisa = $dd->nominal - $dd->realisasi_anggaran;
                        $rowClass = $sisa < 0 ? 'tr-over' : ($dd->nominal > 0 && $sisa <= ($dd->nominal * 0.10) ? 'tr-near' : '');
                        $total_nominal_local += $dd->nominal;
                        $total_realisasi_local += $dd->realisasi_anggaran;
                      @endphp
                      <tr class="{{ $rowClass }}">
                        <td>{{ $dd->nama_subkategori }}</td>
                        <td class="text-end tnum">Rp {{ number_format($dd->nominal, 0, ',', '.') }}</td>
                        <td class="text-end tnum">Rp {{ number_format($dd->realisasi_anggaran, 0, ',', '.') }}</td>
                        <td class="text-end tnum">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center subtle py-4">Belum ada data anggaran.</td>
                      </tr>
                    @endforelse

                    <tr class="fw-bold">
                      <td>Total</td>
                      <td class="text-end tnum">Rp {{ number_format($total_nominal_local, 0, ',', '.') }}</td>
                      <td class="text-end tnum">Rp {{ number_format($total_realisasi_local, 0, ',', '.') }}</td>
                      <td class="text-end tnum">Rp {{ number_format($total_nominal_local - $total_realisasi_local, 0, ',', '.') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              @php
                $sisaTotal = $total_nominal_local - $total_realisasi_local;
                $ratio = $total_nominal_local > 0 ? ($sisaTotal / $total_nominal_local) : 0;
                $pct = $total_nominal_local > 0 ? round($total_realisasi_local / $total_nominal_local * 100) : 0;
                $pct = max(0, min(100, $pct));
                $barClass = $pct >= 90 ? 'bg-warning' : 'bg-success';
                $badgeClass = $sisaTotal < 0 ? 'bg-danger-subtle text-danger'
                              : ($ratio <= 0.10 ? 'bg-warning-subtle text-warning-emphasis'
                                                : 'bg-success-subtle text-success');
              @endphp

              <div class="mt-2">
                <div class="progress" style="height:10px">
                  <div class="progress-bar {{ $barClass }}" style="width: {{ $pct }}%"></div>
                </div>
                <div class="d-flex justify-content-between small subtle mt-1">
                  <span>Realisasi {{ $pct }}% dari anggaran</span>
                  <span class="badge {{ $badgeClass }} p-2">Sisa: Rp {{ number_format($sisaTotal, 0, ',', '.') }}</span>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- Detail Pembelian -->
        <div class="row g-3 mt-1">
          <div class="col-12">
            <div class="card p-3">
              <h5 class="text-center">Detail Pembelian</h5>

              <div class="table-responsive">
                <table class="table table-modern table-striped align-middle mt-3" width="100%">
                  <thead>
                    <tr>
                      <th class="text-center">Nama Barang</th>
                      <th class="text-center">Kuantitas</th>
                      <th class="text-center">Harga</th>
                      <th class="text-center">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $total_request_local = 0; @endphp

                    @forelse ($detail_request as $dr)
                      <tr>
                        <td class="text-center">{{ $dr->nama_barang }}</td>
                        <td class="text-center tnum">{{ $dr->kuantitas }}</td>
                        <td class="text-end tnum">Rp {{ number_format($dr->harga, 0, ',', '.') }}</td>
                        <td class="text-end tnum">Rp {{ number_format($dr->total, 0, ',', '.') }}</td>
                      </tr>
                      @php $total_request_local += $dr->total; @endphp
                    @empty
                      <tr>
                        <td colspan="4" class="text-center subtle py-4">Belum ada request pembelian.</td>
                      </tr>
                    @endforelse

                    @if (count($detail_request) > 0)
                      @php
                        $totalReqShow = isset($total_request_pembelian) ? $total_request_pembelian : $total_request_local;
                      @endphp
                      <tr class="fw-bold">
                        <td colspan="3">Total Request Pembelian</td>
                        <td class="text-end tnum">Rp {{ number_format($totalReqShow, 0, ',', '.') }}</td>
                      </tr>
                    @endif
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Sidebar mobile toggle
    const sidebar   = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop  = document.getElementById('backdrop');

    const openSidebar  = ()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); };
    const closeSidebar = ()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); };

    toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    backdrop?.addEventListener('click', closeSidebar);

    // Tom Select (anggota)
    (function(){
      const el = document.getElementById('id_user');
      if(!el) return;

      const remoteUrl = el.getAttribute('data-url') || "";

      new TomSelect(el,{
        create:false,
        maxItems:1,
        persist:false,
        preload:'focus',
        valueField:'id',
        labelField:'name',
        searchField:['name'],
        sortField:{field:'name',direction:'asc'},
        maxOptions:50,
        render:{
          option:(d,e)=> `<div>${e(d.name||d.text||'')}</div>`,
          item:(d,e)=> `<div>${e(d.name||d.text||'')}</div>`
        },
        load:(q,cb)=>{
          if(!remoteUrl){ cb(); return; }
          fetch(remoteUrl + '?q=' + encodeURIComponent(q||''), {headers:{'Accept':'application/json'}})
            .then(r=> r.ok ? r.json() : [])
            .then(arr=> cb((arr||[]).map(u=>({id:u.id, name:u.name||u.text||''}))))
            .catch(()=> cb());
        }
      });
    })();

    // tombol close project
    document.addEventListener('click', function(e){
      const btn = e.target.closest('.js-close-project');
      if(!btn) return;

      const id   = btn.getAttribute('data-id');
      const nama = btn.getAttribute('data-nama');
      closeProject(id, nama);
    });

    async function closeProject(id, nama) {
      const result = await Swal.fire({
        title: 'Tutup Project?',
        html: 'Project <b>' + nama + '</b> akan dikunci. Sistem akan menghitung sisa dana dan memasukkannya ke Kas (jika ada).',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, tutup',
        cancelButtonText: 'Batal'
      });

      if (!result.isConfirmed) return;

      Swal.fire({
        title:'Memproses...',
        allowOutsideClick:false,
        didOpen:()=>Swal.showLoading()
      });

      try{
        const res = await fetch('/project/' + id + '/close', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        });

        const data = await res.json();
        Swal.close();

        if (data.success) {
          const fmt = (n)=> new Intl.NumberFormat('id-ID').format(Number(n||0));
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            html:
              'Project ditutup.<br><br>' +
              'Total Masuk: <b>Rp ' + fmt(data.data.total_masuk) + '</b><br>' +
              'Total Keluar: <b>Rp ' + fmt(data.data.total_keluar) + '</b><br>' +
              'Sisa: <b>Rp ' + fmt(data.data.sisa) + '</b><br>' +
              'Masuk Kas: <b>Rp ' + fmt(data.data.kas_masuk) + '</b>',
            confirmButtonText: 'OK'
          }).then(()=> location.reload());
        } else {
          Swal.fire({ icon:'error', title:'Gagal', text: data.message || 'Terjadi kesalahan.' });
        }
      } catch(e){
        Swal.close();
        Swal.fire({ icon:'error', title:'Error', text:'Tidak bisa menghubungi server.' });
      }
    }
  </script>
</body>
</html>
