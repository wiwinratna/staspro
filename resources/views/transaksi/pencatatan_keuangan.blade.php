<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Pencatatan Keuangan</title>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

  <style>
    :root{
      --brand:#16a34a;
      --brand-700:#15803d;
      --brand-50:#ecfdf5;

      --ink:#0f172a;
      --ink-600:#475569;
      --line:#e2e8f0;

      --bg:#f6f7fb;
      --card:#ffffff;

      --shadow:0 10px 30px rgba(15,23,42,.08);
      --shadow2:0 18px 40px rgba(15,23,42,.10);

      --danger:#ef4444;
      --warning:#f59e0b;
    }

    *{ box-sizing:border-box }
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
    .brand{
      display:flex;align-items:center;gap:10px;
      font-weight:800;
      letter-spacing:.2px;
    }
    .brand-badge{
      font-size:.72rem; font-weight:800;
      padding:.22rem .55rem; border-radius:999px;
      background:rgba(255,255,255,.16);
      border:1px solid rgba(255,255,255,.22);
      white-space:nowrap;
    }

    /* Layout */
    .app{ display:flex; min-height:calc(100vh - 56px); }
    .sidebar{
      width:260px;
      background:var(--card);
      border-right:1px solid var(--line);
      padding:14px;
      position:sticky; top:56px;
      height:calc(100vh - 56px);
      overflow:auto;
    }

    .menu-title{
      font-size:.72rem;
      letter-spacing:.08em;
      color:var(--ink-600);
      text-transform:uppercase;
      margin:8px 0;
      font-weight:700;
    }

    .nav-link-custom{
      display:flex; align-items:center; gap:10px;
      padding:9px 10px;
      border-radius:14px;
      text-decoration:none;
      color:var(--ink);
      font-weight:600;
      font-size:.92rem;
      line-height:1;
      transition:.18s;
      white-space:nowrap;
    }
    .nav-link-custom i{ font-size:1.05rem; }

    .nav-link-custom:hover{
      background:var(--brand-50);
      color:var(--brand-700);
      transform:translateX(2px);
    }
    .nav-link-custom.active{
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff;
      box-shadow:0 16px 28px rgba(2,6,23,.12);
      font-weight:700;
    }

    .content{ flex:1; padding:18px 18px 22px; }

    /* HERO ala Kas */
    .hero{
      border-radius:22px;
      padding:18px;
      background:
        radial-gradient(900px 240px at 18% 0%, rgba(22,163,74,.22), transparent 60%),
        radial-gradient(700px 220px at 85% 10%, rgba(22,163,74,.14), transparent 55%),
        linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
      border:1px solid rgba(226,232,240,.95);
      box-shadow:var(--shadow);
      position:relative;
      overflow:hidden;
      margin-bottom:14px;
    }
    .hero::after{
      content:"";
      position:absolute; inset:-1px;
      background:
        radial-gradient(600px 160px at 12% 0%, rgba(22,163,74,.18), transparent 55%),
        radial-gradient(500px 160px at 95% 0%, rgba(22,163,74,.10), transparent 55%);
      pointer-events:none;
      opacity:.65;
    }
    .hero-inner{ position:relative; z-index:2; width:100%; }

    .hero-left .title{
      font-size:1.65rem;
      font-weight:800;
      margin:0;
      letter-spacing:-.2px;
    }
    .hero-left .sub{
      margin:6px 0 0;
      color:var(--ink-600);
      font-weight:500;
    }

    .tools-row{
      margin-top:14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
    }
    .tools-left{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }
    .tools-right{
      margin-left:auto;
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }

    .btn-brand{
      height:38px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      border-radius:999px;
      font-weight:800;
      padding:0 14px;
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      border:0;
      box-shadow:0 16px 28px rgba(22,163,74,.18);
      color:#fff;
      white-space:nowrap;
      text-decoration:none;
    }
    .btn-brand:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }
    .btn-brand i{ line-height:1; }

    .btn-soft{
      height:38px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      border-radius:999px;
      font-weight:800;
      padding:0 14px;
      background:#fff;
      color:var(--ink);
      border:1px solid rgba(226,232,240,.95);
      box-shadow:0 10px 26px rgba(15,23,42,.05);
      white-space:nowrap;
      text-decoration:none;
    }
    .btn-soft:hover{
      background:var(--brand-50);
      transform:translateY(-1px);
      color:var(--brand-700);
      border-color:rgba(226,232,240,.95);
    }

    /* Summary cards (ikut Kas) */
    .stats-grid{
      display:grid;
      grid-template-columns: repeat(2, 1fr);
      gap:14px;
      margin-top:12px;
    }
    .stat-card{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      padding:14px;
      box-shadow:var(--shadow);
    }
    .stat-label{
      font-size:.72rem;
      letter-spacing:.08em;
      text-transform:uppercase;
      font-weight:900;
      color:var(--ink-600);
    }
    .stat-value{
      margin-top:6px;
      font-weight:900;
      font-size:1.35rem;
    }
    .tnum{ font-variant-numeric: tabular-nums; }

    /* Filter row card */
    .filter-card{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      padding:12px 14px;
      box-shadow:var(--shadow);
      margin-top:14px;
    }
    .filter-row{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }
    .form-date{
      height:38px;
      border-radius:999px;
      border:1px solid rgba(226,232,240,.95);
      padding:0 12px;
      font-weight:700;
      background:#fff;
    }
    .icon-btn{
      height:38px; width:38px;
      border-radius:999px;
      border:1px solid rgba(226,232,240,.95);
      background:#fff;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      box-shadow:0 10px 26px rgba(15,23,42,.05);
      text-decoration:none;
      color:var(--ink);
    }
    .icon-btn:hover{ background:var(--brand-50); color:var(--brand-700); transform:translateY(-1px); }

    /* Tabs pill */
    .tabs-wrap{
      margin-top:12px;
      display:flex;
      justify-content:center;
    }
    .tabs{
      display:inline-flex;
      gap:8px;
      background:#fff;
      border:1px solid rgba(226,232,240,.95);
      border-radius:999px;
      padding:6px;
      box-shadow:0 10px 26px rgba(15,23,42,.05);
    }
    .tab-pill{
      height:32px;
      display:inline-flex;
      align-items:center;
      padding:0 12px;
      border-radius:999px;
      font-weight:900;
      font-size:.78rem;
      text-transform:uppercase;
      letter-spacing:.06em;
      color:var(--ink-600);
      text-decoration:none;
      border:1px solid transparent;
      white-space:nowrap;
    }
    .tab-pill.active{
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff;
      box-shadow:0 16px 28px rgba(22,163,74,.18);
    }
    .tab-pill:hover{ background:var(--brand-50); color:var(--brand-700); }

    /* Table */
    .table-wrap{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      overflow:hidden;
      margin-top:14px;
      box-shadow:var(--shadow);
    }
    .table-responsive{ max-height:68vh; overflow-y:auto; }

    .table-modern{
      margin:0;
      font-size:.92rem;
      border-collapse:separate;
      border-spacing:0;
      table-layout:fixed;     /* <<< KUNCI: header & isi lurus */
      width:100%;
    }
    .table-modern thead th{
      background:#f8fafc;
      color:var(--ink-600);
      font-weight:900;
      text-transform:uppercase;
      font-size:.72rem;
      letter-spacing:.08em;
      padding:14px 12px;
      border-bottom:1px solid rgba(226,232,240,.95);
      position:sticky;
      top:0;
      z-index:5;
      white-space:nowrap;
    }
    .table-modern tbody td{
      padding:14px 12px;
      vertical-align:middle;
      border-top:1px solid #eef2f7;
      font-weight:500;
      overflow:hidden; /* biar clamp rapi */
    }
    .table-striped > tbody > tr:nth-of-type(odd){ background:#fcfcfd; }
    .table-modern tbody tr:hover{ background:var(--brand-50); transition:.12s; }

    /* Clamp 2 lines */
    .clamp-2{
      display:-webkit-box;
      -webkit-box-orient:vertical;
      -webkit-line-clamp:2;
      overflow:hidden;
      word-break:break-word;
    }
    .link-detail{
      display:inline-flex;
      align-items:center;
      gap:6px;
      margin-top:4px;
      font-weight:900;
      font-size:.82rem;
      text-decoration:none;
      color:var(--brand-700);
      white-space:nowrap;
    }
    .link-detail:hover{ text-decoration:underline; }

    /* Aksi buttons */
    .btn-act{
      width:34px; height:34px;
      border-radius:10px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border:1px solid rgba(226,232,240,.95);
      background:#fff;
    }
    .btn-act.edit{ border-color:rgba(245,158,11,.35); color:#92400e; background:#fff7ed; }
    .btn-act.edit:hover{ filter:brightness(.98); }
    .btn-act.del{ border-color:rgba(239,68,68,.30); color:#991b1b; background:#fef2f2; }
    .btn-act.del:hover{ filter:brightness(.98); }

    /* Mobile */
    .backdrop{
      display:none;
      position:fixed;
      inset:0;
      background:rgba(15,23,42,.38);
      z-index:1035;
    }
    .backdrop.show{ display:block; }

    @media(max-width:991px){
      .sidebar{
        position:fixed;
        left:-290px;
        top:56px;
        height:calc(100vh - 56px);
        z-index:1040;
        transition:left .2s;
      }
      .sidebar.open{ left:0; }
      .content{ padding:14px; }
      .stats-grid{ grid-template-columns: 1fr; }
    }
  </style>
</head>

<body>

<!-- TOPBAR -->
<nav class="navbar topbar">
  <div class="container-fluid">
    <button class="btn btn-outline-light d-lg-none me-2" id="sidebarToggle">
      <i class="bi bi-list"></i>
    </button>

    <div class="brand">
      <span>STAS-RG</span>
      <span class="brand-badge">{{ Auth::user()->role === 'admin' ? 'ADMIN' : 'PENELITI' }}</span>
    </div>

    <div class="ms-auto">@include('navbar')</div>
  </div>
</nav>

<div class="app">

  <!-- SIDEBAR -->
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

    @if(Auth::user()->role == 'admin')
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

  <!-- CONTENT -->
  <main class="content">

    <!-- HERO -->
    <section class="hero">
      <div class="hero-inner">
        <div class="hero-left">
          <h1 class="title">Pencatatan Keuangan</h1>
          <p class="sub">
            @if(request()->has('start_date') && request()->has('end_date'))
              @if(request('start_date') === request('end_date'))
                Total transaksi pada {{ \Carbon\Carbon::parse(request('start_date'))->locale('id')->translatedFormat('d F Y') }}.
              @else
                Total transaksi dari {{ \Carbon\Carbon::parse(request('start_date'))->locale('id')->translatedFormat('d F Y') }}
                sampai {{ \Carbon\Carbon::parse(request('end_date'))->locale('id')->translatedFormat('d F Y') }}.
              @endif
            @else
              Total nominal transaksi keseluruhan.
            @endif
          </p>
        </div>

        <div class="tools-row">
          <div class="tools-left">
            <a href="{{ route('form_input_pencatatan_keuangan') }}" class="btn btn-brand">
              <i class="bi bi-plus-lg"></i> Tambah Pencatatan
            </a>
          </div>

          <div class="tools-right">
            <a
              href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
              target="_blank"
              rel="noopener"
              class="btn btn-soft"
              title="Buka Manual Book"
            >
              <i class="bi bi-journal-bookmark"></i> Manual Book
            </a>
          </div>
        </div>
      </div>
    </section>

    @if(session('success'))
      <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif

    <!-- SUMMARY (mirip Kas) -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Total Pemasukan</div>
        <div class="stat-value tnum" id="total-pemasukan">Rp 0</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Total Pengeluaran</div>
        <div class="stat-value tnum" id="total-pengeluaran">Rp 0</div>
      </div>
    </div>

    <!-- TABS -->
    <div class="tabs-wrap">
      <div class="tabs" id="filterTabs">
        <a href="#" class="tab-pill active" data-filter="semua">Semua</a>
        <a href="#" class="tab-pill" data-filter="pemasukan">Pemasukan</a>
        <a href="#" class="tab-pill" data-filter="pengeluaran">Pengeluaran</a>
      </div>
    </div>

    <!-- FILTER DATE -->
    <div class="filter-card">
      <div class="filter-row">
        <div class="fw-bold">Filter Tanggal</div>

        <input type="date" id="startDate" name="start_date" class="form-date" form="filterForm">
        <span class="text-muted fw-bold">sampai</span>
        <input type="date" id="endDate" name="end_date" class="form-date" form="filterForm">

        <a href="{{ route('pencatatan_keuangan') }}" class="icon-btn" title="Reset Filter">
          <i class="bi bi-arrow-counterclockwise"></i>
        </a>

        <div class="ms-auto">
          <button type="submit" form="filterForm" class="btn btn-brand">
            <i class="bi bi-funnel"></i> Terapkan
          </button>
        </div>
      </div>
      <form id="filterForm" method="GET" action="{{ route('filter_pencatatan_keuangan') }}"></form>
    </div>

    <!-- TABLE -->
    <div class="table-wrap">
      <div class="table-responsive">
        <table class="table table-modern table-striped align-middle">
          <thead>
            <tr>
              <th style="width:60px">No</th>
              <th style="width:160px">Tanggal</th>
              <th style="width:170px">Tim</th>
              <th style="width:250px">Sub Kategori</th>
              <th style="width:300px">Deskripsi</th>
              <th class="text-end" style="width:160px">Jumlah</th>
              <th style="width:160px">Metode</th>
              <th style="width:90px">Bukti</th>
              <th style="width:120px">Aksi</th>
            </tr>
          </thead>

          <tbody>
            @foreach($pencatatanKeuangans as $index => $transaksi)
              @php
                $tim  = $transaksi->project->nama_project ?? 'Tidak Ada';
                $sub  = $transaksi->subKategoriPendanaan->nama ?? 'Tidak Ada';
                $desk = $transaksi->deskripsi_transaksi ?? '-';
              @endphp

              <tr data-jenis="{{ strtolower($transaksi->jenis_transaksi ?? 'pengeluaran') }}">
                <td>{{ $index + 1 }}</td>

                <td>
                  <div class="fw-bold">{{ $transaksi->created_at->timezone('Asia/Jakarta')->format('d-m-Y') }}</div>
                  <div style="font-size:.82rem;color:var(--ink-600);font-weight:700;">
                    {{ $transaksi->created_at->timezone('Asia/Jakarta')->format('H:i') }}
                  </div>
                </td>

                <td title="{{ $tim }}" class="fw-semibold">
                  {{ \Illuminate\Support\Str::limit($tim, 24) }}
                </td>

                <td title="{{ $sub }}">
                  <div class="clamp-2 fw-semibold">{{ $sub }}</div>
                  <a class="link-detail" href="#" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $transaksi->id }}">
                    <i class="bi bi-eye"></i> Detail
                  </a>
                </td>

                <td title="{{ $desk }}">
                  <div class="clamp-2">{{ $desk }}</div>
                </td>

                <td class="text-end tnum fw-bold">
                  Rp {{ number_format($transaksi->jumlah_transaksi, 0, ',', '.') }}
                </td>

                <td class="fw-semibold">{{ strtoupper($transaksi->metode_pembayaran) }}</td>

                <td>
                  @if($transaksi->bukti_transaksi)
                    <a href="#" class="link-detail" style="margin-top:0"
                       data-bs-toggle="modal" data-bs-target="#modalBukti{{ $transaksi->id }}">
                      Lihat
                    </a>

                    <div class="modal fade" id="modalBukti{{ $transaksi->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Bukti Transaksi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body text-center">
                            <img src="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" alt="Bukti" class="img-fluid rounded shadow-sm mb-3">
                            <a href="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" class="btn btn-brand" download>
                              <i class="bi bi-download"></i> Unduh
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>

                <td>
                  <div class="d-flex gap-1">
                    <a href="{{ route('pencatatan_keuangan.edit', $transaksi->id) }}" class="btn-act edit" title="Edit">
                      <i class="bi bi-pencil-square"></i>
                    </a>

                    <form action="{{ route('pencatatan_keuangan.destroy', $transaksi->id) }}" method="POST" class="m-0">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn-act del" title="Hapus" onclick="confirmDelete({{ $transaksi->id }})">
                        <i class="bi bi-trash-fill"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>

              <!-- Modal Detail -->
              <div class="modal fade" id="modalDetail{{ $transaksi->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Detail Transaksi</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <div class="text-muted" style="font-weight:800;font-size:.78rem;letter-spacing:.06em;text-transform:uppercase;">Tim</div>
                          <div class="fw-bold">{{ $tim }}</div>
                        </div>
                        <div class="col-md-6">
                          <div class="text-muted" style="font-weight:800;font-size:.78rem;letter-spacing:.06em;text-transform:uppercase;">Tanggal</div>
                          <div class="fw-bold">{{ $transaksi->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</div>
                        </div>
                        <div class="col-md-6">
                          <div class="text-muted" style="font-weight:800;font-size:.78rem;letter-spacing:.06em;text-transform:uppercase;">Sub Kategori</div>
                          <div class="fw-bold">{{ $sub }}</div>
                        </div>
                        <div class="col-md-6">
                          <div class="text-muted" style="font-weight:800;font-size:.78rem;letter-spacing:.06em;text-transform:uppercase;">Metode</div>
                          <div class="fw-bold">{{ strtoupper($transaksi->metode_pembayaran) }}</div>
                        </div>
                        <div class="col-12">
                          <div class="text-muted" style="font-weight:800;font-size:.78rem;letter-spacing:.06em;text-transform:uppercase;">Deskripsi</div>
                          <div class="fw-semibold">{{ $desk }}</div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-soft" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Tutup
                      </button>
                    </div>
                  </div>
                </div>
              </div>

            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // sidebar mobile toggle
  const sidebar = document.getElementById('appSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const backdrop = document.getElementById('backdrop');

  const openSidebar = ()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
  const closeSidebar = ()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }

  toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
  backdrop?.addEventListener('click', closeSidebar);

  // SweetAlert Delete (AJAX)
  function confirmDelete(transaksiId) {
    Swal.fire({
      title: "Apakah Anda yakin?",
      text: "Data pencatatan keuangan akan dihapus permanen!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Hapus!",
      cancelButtonText: "Batal",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
    }).then((result) => {
      if (result.isConfirmed) {
        fetch(`/pencatatan_keuangan/${transaksiId}`, {
          method: "DELETE",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Content-Type": "application/json",
            "Accept": "application/json"
          }
        })
        .then(r => { if(!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        .then(data => {
          if (data.success) {
            Swal.fire({ title:"Berhasil!", text:data.message, icon:"success", timer:1500, showConfirmButton:false });
            setTimeout(()=> location.reload(), 1500);
          } else {
            Swal.fire({ title:"Gagal!", text:data.message || "Terjadi kesalahan", icon:"error" });
          }
        })
        .catch(err => Swal.fire({ title:"Gagal!", text:"Server error: " + err.message, icon:"error" }));
      }
    });
  }

  // Tabs filter + hitung KPI berdasarkan yang tampil
  (function(){
    const rows = document.querySelectorAll('table tbody tr');
    const tabs = document.querySelectorAll('#filterTabs [data-filter]');
    const elIn  = document.getElementById('total-pemasukan');
    const elOut = document.getElementById('total-pengeluaran');

    const num = s => Number((s||'').replace(/[^0-9]/g,''));
    const rup = n => new Intl.NumberFormat('id-ID').format(n||0);

    function recompute(){
      let tIn=0, tOut=0;
      rows.forEach(tr=>{
        if (tr.style.display==='none') return;
        const jenis = (tr.dataset.jenis||'').toLowerCase();
        const cell = tr.querySelector('td:nth-child(6)');
        const val = num(cell ? cell.textContent : '');
        if (jenis==='pemasukan') tIn += val; else tOut += val;
      });
      elIn.textContent  = 'Rp ' + rup(tIn);
      elOut.textContent = 'Rp ' + rup(tOut);
    }

    function applyFilter(filter){
      tabs.forEach(tab => tab.classList.toggle('active', tab.dataset.filter===filter));
      rows.forEach(tr=>{
        const j = (tr.dataset.jenis||'').toLowerCase();
        tr.style.display = (filter==='semua' || j===filter) ? '' : 'none';
      });
      recompute();
    }

    tabs.forEach(tab => tab.addEventListener('click', e=>{
      e.preventDefault();
      applyFilter(tab.dataset.filter);
    }));

    applyFilter('semua');
  })();

  // Filter tanggal: set max & min
  document.addEventListener("DOMContentLoaded", function () {
    const startDateInput = document.getElementById("startDate");
    const endDateInput = document.getElementById("endDate");

    const today = new Date().toISOString().split('T')[0];
    startDateInput?.setAttribute('max', today);
    endDateInput?.setAttribute('max', today);

    startDateInput?.addEventListener("change", function () {
      if (startDateInput.value) endDateInput.setAttribute('min', startDateInput.value);
    });
  });
</script>

</body>
</html>
