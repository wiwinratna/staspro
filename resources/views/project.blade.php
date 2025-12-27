<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <title>Project</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

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

      --danger:#ef4444;

      --shadow:0 10px 30px rgba(15,23,42,.08);
      --shadow2:0 18px 40px rgba(15,23,42,.10);
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

    /* HERO ala dashboard */
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

    /* Tools */
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
    }

    .search-wrap{
      height:38px;
      display:flex;
      align-items:center;
      gap:10px;
      background:#fff;
      border:1px solid rgba(226,232,240,.95);
      border-radius:999px;
      padding:0 12px;
      box-shadow:0 10px 26px rgba(15,23,42,.05);
      width:420px;
      max-width:100%;
    }
    .search-wrap i{ color:var(--ink-600); line-height:1; }

    .search-input{
      height:100%;
      width:100%;
      border:0;
      outline:0;
      font-weight:600;
      background:transparent;
      padding:0;
    }

    .btn-apply{
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
      white-space:nowrap;
    }
    .btn-apply:hover{ filter:brightness(.98); transform:translateY(-1px); }
    .btn-apply i{ line-height:1; }

    /* tombol manual book */
    .btn-manual{
      height:36px;
      display:inline-flex; align-items:center; gap:8px;
      padding:0 14px;
      border-radius:999px;
      font-weight:700;
      background:#fff;
      color:var(--ink);
      border:1px solid rgba(226,232,240,.95);
      text-decoration:none;
      box-shadow:0 12px 24px rgba(15,23,42,.06);
      white-space:nowrap;
      transition:.15s;
    }
    .btn-manual:hover{
      background:var(--brand-50);
      color:var(--brand-700);
      transform:translateY(-1px);
    }
    .btn-manual i{ line-height:1; }

    @media(max-width:991.98px){
      .search-wrap{ width:100%; }
      .tools-right{
        width:100%;
        margin-left:0;
        justify-content:flex-start;
      }
    }

    /* Section */
    .section-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      margin:18px 0 12px;
      flex-wrap:wrap;
    }
    .section-title{
      font-weight:800;
      font-size:1.02rem;
      margin:0;
      display:flex;
      align-items:center;
      gap:8px;
    }
    .section-pill{
      height:34px;
      display:inline-flex; align-items:center; gap:8px;
      padding:0 12px;
      border-radius:999px;
      background:var(--brand-50);
      color:var(--brand-700);
      border:1px solid rgba(2,6,23,.06);
      font-weight:700;
      white-space:nowrap;
      font-size:.82rem;
    }

    /* grid rapih */
    .row.g-3 > [class*="col-"]{ display:flex; }

    /* card project */
    .proj-card{
      width:100%;
      background:
        radial-gradient(900px 220px at 18% 0%, rgba(255,255,255,.16), transparent 60%),
        radial-gradient(600px 200px at 85% 12%, rgba(255,255,255,.10), transparent 55%),
        linear-gradient(180deg,#1a8f4a,#157a3b);
      color:#fff;
      border-radius:20px;
      padding:16px;
      height:180px;
      position:relative;
      box-shadow:0 10px 26px rgba(15,23,42,.10);
      transition: transform .15s ease, box-shadow .15s ease;
      cursor:pointer;
      overflow:hidden;
      border:1px solid rgba(255,255,255,.14);
    }
    .proj-card:hover{ transform:translateY(-2px); box-shadow:var(--shadow2); }
    .proj-card.archived{
      background:
        radial-gradient(900px 220px at 18% 0%, rgba(255,255,255,.14), transparent 60%),
        radial-gradient(600px 200px at 85% 12%, rgba(255,255,255,.08), transparent 55%),
        linear-gradient(180deg,#94a3b8,#64748b);
      border:1px solid rgba(255,255,255,.18);
    }

    .proj-year{
      position:absolute; top:14px; left:14px;
      font-size:.72rem;
      padding:.22rem .6rem;
      border-radius:999px;
      background:rgba(255,255,255,.16);
      border:1px solid rgba(255,255,255,.22);
      font-weight:700;
      white-space:nowrap;
    }
    .proj-status{
      position:absolute; top:14px; right:14px;
      font-size:.72rem;
      font-weight:700;
      padding:.22rem .6rem;
      border-radius:999px;
      background:rgba(255,255,255,.16);
      border:1px solid rgba(255,255,255,.22);
      white-space:nowrap;
    }
    .proj-title{
      margin-top:40px;
      font-size:1.05rem;
      font-weight:800;
      line-height:1.35;
      letter-spacing:-.1px;
    }
    .proj-meta{
      margin-top:10px;
      font-size:.82rem;
      opacity:.95;
      display:flex;
      flex-direction:column;
      gap:4px;
      font-weight:500;
    }

    /* actions */
    .proj-actions{
      position:absolute;
      right:12px;
      bottom:12px;
      display:flex;
      gap:8px;
      opacity:0;
      transition:.15s;
    }
    .proj-card:hover .proj-actions{ opacity:1; }
    .proj-actions .btn{
      --bs-btn-padding-y:.2rem;
      --bs-btn-padding-x:.5rem;
      --bs-btn-bg:rgba(255,255,255,.18);
      --bs-btn-border-color:rgba(255,255,255,.30);
      --bs-btn-hover-bg:rgba(255,255,255,.26);
      color:#fff;
      border-radius:12px;
      font-weight:700;
    }
    .proj-actions form{ margin:0; }

    /* badge anggota (clean: tampil hanya jika join) */
    .proj-member{
      position:absolute;
      left:14px;
      bottom:14px;
      font-size:.72rem;
      font-weight:800;
      padding:.22rem .6rem;
      border-radius:999px;
      background:rgba(255,255,255,.22);
      border:1px solid rgba(255,255,255,.30);
      display:inline-flex;
      align-items:center;
      gap:6px;
      white-space:nowrap;
    }
    .proj-card.joined{
      outline:2px solid rgba(255,255,255,.35);
      box-shadow:0 18px 42px rgba(2,6,23,.18);
    }

    /* mobile sidebar */
    .backdrop{
      display:none;
      position:fixed;
      inset:0;
      background:rgba(15,23,42,.38);
      z-index:1035;
    }
    .backdrop.show{ display:block; }
    @media (max-width:991.98px){
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
    }
  </style>
</head>

<body>

<nav class="navbar topbar">
  <div class="container-fluid">
    <button class="btn btn-outline-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
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

    @if (Auth::user()->role == 'admin')
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

  <main class="content">

    <!-- HERO -->
    <section class="hero">
      <div class="hero-inner">
        <div class="hero-left">
          <h1 class="title">Project</h1>
          <p class="sub">Daftar project aktif & arsip.</p>
        </div>

        <div class="tools-row">
          <div class="tools-left">
            <div class="search-wrap">
              <i class="bi bi-search"></i>
              <input id="searchProject" class="search-input" placeholder="Cari project (nama / tahun / status)">
            </div>

            @if(Auth::user()->role=='admin')
              <a href="{{ route('project.create') }}" class="btn btn-sm btn-apply text-white">
                <i class="bi bi-plus-lg"></i> Input Project
              </a>
            @endif
          </div>

          <div class="tools-right">
            <a class="btn-manual"
               href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
               target="_blank" rel="noopener" title="Buka Manual Book">
              <i class="bi bi-book"></i> Manual Book
            </a>
          </div>
        </div>
      </div>
    </section>

    @if ($message = Session::get('success'))
      <div class="alert alert-success">{{ $message }}</div>
    @endif
    @if ($message = Session::get('error'))
      <div class="alert alert-danger">{{ $message }}</div>
    @endif

    @php
      $aktif = $projects->filter(fn($p)=>($p->status ?? 'aktif')==='aktif');
      $arsip = $projects->filter(fn($p)=>($p->status ?? '')==='ditutup');
    @endphp

    <div class="section-head">
      <h6 class="section-title"><i class="bi bi-play-circle-fill"></i> Project Aktif</h6>
      <span class="section-pill"><i class="bi bi-layers"></i> {{ $aktif->count() }} item</span>
    </div>

    <div class="row g-3">
      @foreach($aktif as $p)
      @php
        $isJoined = Auth::user()->role !== 'admin' && in_array($p->id, $joinedProjectIds ?? []);
      @endphp

      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 project-item"
           data-search="{{ strtolower($p->nama_project.' '.$p->tahun.' on going ongoing aktif') }}">
        <div class="proj-card {{ $isJoined ? 'joined' : '' }}" role="button" tabindex="0"
             onclick="location.href='{{ route('project.show',$p->id) }}'"
             onkeydown="if(event.key==='Enter'){ this.click(); }">

          <span class="proj-year">{{ $p->tahun }}</span>
          <span class="proj-status">ON GOING</span>

          <div class="proj-title">{{ $p->nama_project }}</div>

          <div class="proj-meta">
            <div>Sumber Dana: {{ $p->sumberDana->jenis_pendanaan ?? '-' }}</div>
          </div>

          @if(Auth::user()->role !== 'admin' && $isJoined)
            <span class="proj-member">
              <i class="bi bi-people-fill"></i> Kamu tergabung
            </span>
          @endif

          @if(Auth::user()->role=='admin')
          <div class="proj-actions" onclick="event.stopPropagation()">
            <a href="{{ route('project.edit',$p->id) }}" class="btn btn-sm" title="Edit Project">
              <i class="bi bi-pencil"></i>
            </a>

            <form action="{{ route('project.destroy',$p->id) }}" method="POST"
                  onsubmit="return confirm('Yakin hapus project ini? (soft delete)');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm" title="Hapus Project">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </div>
          @endif

        </div>
      </div>
      @endforeach
    </div>

    <div class="section-head">
      <h6 class="section-title"><i class="bi bi-archive-fill"></i> Arsip (Ditutup)</h6>
      <span class="section-pill"><i class="bi bi-layers"></i> {{ $arsip->count() }} item</span>
    </div>

    <div class="row g-3">
      @foreach($arsip as $p)
      @php
        $isJoined = Auth::user()->role !== 'admin' && in_array($p->id, $joinedProjectIds ?? []);
      @endphp

      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 project-item"
           data-search="{{ strtolower($p->nama_project.' '.$p->tahun.' ditutup arsip') }}">
        <div class="proj-card archived {{ $isJoined ? 'joined' : '' }}" role="button" tabindex="0"
             onclick="location.href='{{ route('project.show',$p->id) }}'"
             onkeydown="if(event.key==='Enter'){ this.click(); }">

          <span class="proj-year">{{ $p->tahun }}</span>
          <span class="proj-status">DITUTUP</span>

          <div class="proj-title">{{ $p->nama_project }}</div>
          <div class="proj-meta">Project telah diarsipkan</div>

          @if(Auth::user()->role !== 'admin' && $isJoined)
            <span class="proj-member">
              <i class="bi bi-people-fill"></i> Kamu tergabung
            </span>
          @endif

          @if(Auth::user()->role=='admin')
          <div class="proj-actions" onclick="event.stopPropagation()">
            <a href="{{ route('project.edit',$p->id) }}" class="btn btn-sm" title="Edit Project">
              <i class="bi bi-pencil"></i>
            </a>

            <form action="{{ route('project.destroy',$p->id) }}" method="POST"
                  onsubmit="return confirm('Yakin hapus project ini? (soft delete)');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm" title="Hapus Project">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </div>
          @endif

        </div>
      </div>
      @endforeach
    </div>

  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // sidebar toggle (mobile)
  const sidebar = document.getElementById('appSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const backdrop = document.getElementById('backdrop');

  const openSidebar = () => { sidebar.classList.add('open'); backdrop.classList.add('show'); }
  const closeSidebar = () => { sidebar.classList.remove('open'); backdrop.classList.remove('show'); }

  toggleBtn?.addEventListener('click', () => {
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });
  backdrop?.addEventListener('click', closeSidebar);

  // search
  document.getElementById('searchProject')?.addEventListener('input', function(){
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.project-item').forEach(el=>{
      el.style.display = (el.dataset.search || '').includes(q) ? '' : 'none';
    });
  });
</script>

</body>
</html>
