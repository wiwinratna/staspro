<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <title>Project</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0; --bg:#f6f7fb; --card:#fff;
    }
    *{box-sizing:border-box}
    body{ background:var(--bg); font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; color:var(--ink); }

    /* Topbar */
    .topbar{ background:linear-gradient(135deg,var(--brand-700),var(--brand)); color:#fff; }
    .topbar .brand-title{ font-weight:700; letter-spacing:.2px; }

    /* Shell */
    .app{ display:flex; min-height:calc(100vh - 56px); }
    .sidebar{ width:260px; background:var(--card); border-right:1px solid var(--line); padding:18px; position:sticky; top:0; height:calc(100vh - 56px); }
    .menu-title{ font-size:.8rem; letter-spacing:.06em; color:var(--ink-600); text-transform:uppercase; margin:6px 0 10px; font-weight:600; }
    .nav-link-custom{ display:flex; align-items:center; gap:10px; padding:10px 12px; color:var(--ink); border-radius:12px; text-decoration:none; transition:all .18s; font-weight:500; }
    .nav-link-custom:hover{ background:var(--brand-50); color:var(--brand-700); }
    .nav-link-custom.active{ background:var(--brand); color:#fff; box-shadow:0 6px 16px rgba(22,163,74,.18); }

    .content{ flex:1; padding:24px; }
    .page-title{ font-size:1.5rem; font-weight:700; margin-bottom:4px; }
    .page-sub{ color:var(--ink-600); margin-bottom:18px; }

    /* Cards */
    .proj-card{
      background:linear-gradient(160deg,var(--brand),var(--brand-700));
      color:#fff; border:0; border-radius:18px; height:170px;
      display:flex; align-items:center; justify-content:center; text-align:center;
      position:relative; overflow:hidden;
      box-shadow:0 10px 24px rgba(22,163,74,.18);
      transition: transform .18s ease, box-shadow .18s ease;
      cursor:pointer;
    }
    .proj-card:hover{ transform: translateY(-3px); box-shadow:0 16px 34px rgba(22,163,74,.28); }
    .proj-title{ font-size:1.05rem; font-weight:800; line-height:1.35; padding:0 14px; }
    .proj-chip{
      position:absolute; left:12px; top:12px; font-size:.75rem;
      background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.28);
      padding:.2rem .5rem; border-radius:999px; backdrop-filter: blur(2px);
    }
    .proj-actions{
      position:absolute; right:10px; top:10px; display:flex; gap:6px;
      opacity:0; transition:opacity .18s ease;
    }
    .proj-card:hover .proj-actions{ opacity:1; }
    .proj-actions .btn{
      --bs-btn-padding-y:.2rem; --bs-btn-padding-x:.45rem; --bs-btn-border-radius:.55rem;
      --bs-btn-bg: rgba(255,255,255,.12); --bs-btn-border-color: rgba(255,255,255,.25);
      --bs-btn-hover-bg: rgba(255,255,255,.22); color:#fff;
    }

    /* Utilities */
    .btn-brand{ background:var(--brand); border-color:var(--brand); color:#fff; }
    .btn-brand:hover{ background:var(--brand-700); border-color:var(--brand-700); }

    @media (max-width:991.98px){
      .sidebar{ position:fixed; left:-280px; z-index:1040; transition:left .2s; }
      .sidebar.open{ left:0; }
      .content{ padding:18px; }
      .backdrop{ display:none; position:fixed; inset:0; background:rgba(15,23,42,.38); z-index:1035; }
      .backdrop.show{ display:block; }
    }
  </style>
</head>
<body>
  <!-- Topbar -->
  <nav class="navbar topbar navbar-expand-lg">
    <div class="container-fluid">
      <button class="btn btn-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>
      <div class="brand-title">STAS-RG • Project</div>
      <div class="ms-auto">@include('navbar')</div>
    </div>
  </nav>

  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="appSidebar">
      <div class="menu-title">Menu</div>
      <a class="nav-link-custom" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a class="nav-link-custom active" href="{{ route('project.index') }}"><i class="bi bi-kanban"></i> Project</a>
      <a class="nav-link-custom" href="{{ route('requestpembelian.index') }}"><i class="bi bi-bag-check"></i> Request Pembelian</a>

      @if (Auth::user()->role == 'admin')
        <div class="menu-title mt-3">Administrasi</div>
        <a class="nav-link-custom" href="{{ route('sumberdana.index') }}"><i class="bi bi-cash-coin"></i> Sumber Dana</a>
        <a class="nav-link-custom" href="{{ route('pencatatan_keuangan') }}"><i class="bi bi-journal-text"></i> Pencatatan Keuangan</a>
        <a class="nav-link-custom" href="{{ route('laporan_keuangan') }}"><i class="bi bi-graph-up"></i> Laporan Keuangan</a>
        <a class="nav-link-custom" href="{{ route('users.index') }}"><i class="bi bi-people"></i> Management User</a>
      @endif
    </aside>
    <div class="backdrop" id="backdrop"></div>

    <!-- Main -->
    <main class="content">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-2">
        <div>
          <div class="page-title">Project</div>
          <div class="page-sub">Daftar project aktif & arsip.</div>
        </div>
        @if (Auth::user()->role == 'admin')
          <a href="{{ route('project.create') }}" class="btn btn-brand">
            <i class="bi bi-plus-lg me-1"></i> Input Project
          </a>
        @endif
      </div>

      @if ($message = Session::get('success'))
        <div class="alert alert-success mt-1">{{ $message }}</div>
      @endif
      @if ($message = Session::get('error'))
        <div class="alert alert-danger mt-1">{{ $message }}</div>
      @endif

      <div class="row g-3">
        @foreach ($projects as $p)
          <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
            <div class="proj-card" role="button" tabindex="0"
                 onclick="window.location='{{ route('project.show', $p->id) }}'"
                 onkeydown="if(event.key==='Enter'){ this.click(); }">
              <span class="proj-chip">{{ $p->tahun }}</span>

              @if (Auth::user()->role == 'admin')
              <div class="proj-actions" onclick="event.stopPropagation()">
                <a href="{{ route('project.edit', $p->id) }}" class="btn btn-sm" title="Edit Project">
                  <i class="bi bi-pencil-square"></i>
                </a>
              </div>
              @endif

              <div class="proj-title">{{ $p->nama_project }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.querySelector('.backdrop');
    const openSidebar = ()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    const closeSidebar = ()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
    toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    backdrop?.addEventListener('click', closeSidebar);
  </script>

  @if (session('success'))
  <script>
    Swal.fire({ icon:'success', title:'Berhasil!', text:'{{ session("success") }}', timer:1800, showConfirmButton:false });
  </script>
  @endif
  @if (session('error'))
  <script>
    Swal.fire({ icon:'error', title:'Gagal!', text:'{{ session("error") }}' });
  </script>
  @endif
</body>
</html>
