{{-- resources/views/layouts/panel.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>@yield('title','Dashboard')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0;
      --bg:#f6f7fb; --card:#ffffff;
      --danger:#ef4444; --warn:#f59e0b; --info:#3b82f6;
      --shadow:0 10px 30px rgba(15,23,42,.08);
      --shadow2:0 18px 40px rgba(15,23,42,.10);

      /* boleh tetep fixed 260 dulu biar stabil */
      --sidebar-w: 260px;
      --topbar-h: 56px;
      --content-max: 1280px;
    }

    *{ box-sizing:border-box; }
    body{
      margin:0;
      background:var(--bg);
      font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      color:var(--ink);
      font-weight:500;
    }

    .topbar{
      position:sticky;
      top:0;
      z-index:1030;
      height:var(--topbar-h);
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff;
      border-bottom:1px solid rgba(255,255,255,.18);
    }

    .brand{ display:flex; align-items:center; gap:10px; font-weight:800; letter-spacing:.2px; }
    .brand-badge{
      font-size:.72rem; font-weight:700;
      padding:.22rem .55rem; border-radius:999px;
      background:rgba(255,255,255,.16);
      border:1px solid rgba(255,255,255,.22);
      white-space:nowrap;
    }

    .app{
      display:flex;
      min-height:calc(100vh - var(--topbar-h));
      width:100%;
      min-width:0;
    }

    /* ✅ FIX 1: sidebar jangan bisa “ketutup” konten */
    .sidebar{
      flex: 0 0 var(--sidebar-w);
      width: var(--sidebar-w);
      background:var(--card);
      border-right:1px solid var(--line);
      padding:14px;

      position:sticky;
      top:var(--topbar-h);
      height:calc(100vh - var(--topbar-h));
      overflow:auto;

      z-index: 1025;            /* ✅ PENTING */
    }

    .menu-title{
      font-size:.72rem; letter-spacing:.08em;
      color:var(--ink-600); text-transform:uppercase;
      margin:8px 0; font-weight:700;
    }

    .nav-link-custom{
      display:flex; align-items:center; gap:10px;
      padding:9px 10px; border-radius:14px;
      text-decoration:none; color:var(--ink);
      font-weight:600; font-size:.92rem; line-height:1;
      transition:.18s; white-space:nowrap;
    }
    .nav-link-custom i{ font-size:1.05rem; }
    .nav-link-custom:hover{
      background:var(--brand-50); color:var(--brand-700);
      transform:translateX(2px);
    }
    .nav-link-custom.active{
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff; box-shadow:0 16px 28px rgba(2,6,23,.12);
    }

    /* ✅ FIX 2: content jangan bikin overlay layer */
    .content{
      flex: 1 1 auto;
      min-width: 0;
      padding:18px 18px 22px;

      position: relative;
      z-index: 1;               /* ✅ PENTING (lebih rendah dari sidebar) */
    }

    .content-inner{
      max-width: var(--content-max);
      margin: 0 auto;
      width: 100%;
      min-width: 0;
    }

    /* Mobile drawer */
    .backdrop{
      display:none;
      position:fixed;
      inset:0;
      background:rgba(15,23,42,.38);
      z-index:1035;
    }
    .backdrop.show{ display:block; }

    @media(max-width:991.98px){
      .sidebar{
        position:fixed;
        left:-290px;
        top:var(--topbar-h);
        height:calc(100vh - var(--topbar-h));
        z-index:1040;
        transition:left .2s;
      }
      .sidebar.open{ left:0; }
      .content{ padding:14px; }
    }
  </style>

  @stack('styles')
</head>

<body>
  <nav class="navbar topbar">
    <div class="container-fluid">
      <button class="btn btn-outline-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>

      <div class="brand">
        <span>STAS-RG</span>
        <span class="brand-badge">
          @if(Auth::user()->role === 'admin') ADMIN
          @elseif(Auth::user()->role === 'bendahara') BENDAHARA
          @else PENELITI
          @endif
        </span>
      </div>

      <div class="ms-auto">@include('navbar')</div>
    </div>
  </nav>

  <div class="app">
    <aside class="sidebar" id="appSidebar">
      <div class="menu-title">Menu</div>

      @php
        $dashRoute = Auth::user()->role === 'bendahara'
          ? 'bendahara.dashboard'
          : (Auth::user()->role === 'admin' ? 'dashboard' : 'peneliti.dashboard');
      @endphp

      <a class="nav-link-custom {{ request()->routeIs($dashRoute) ? 'active' : '' }}" href="{{ route($dashRoute) }}">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>

      <a class="nav-link-custom {{ request()->routeIs('project.*') ? 'active' : '' }}" href="{{ route('project.index') }}">
        <i class="bi bi-kanban"></i> Project
      </a>

      <a class="nav-link-custom {{ request()->routeIs('requestpembelian.*') ? 'active' : '' }}" href="{{ route('requestpembelian.index') }}">
        <i class="bi bi-bag-check"></i> Request Pembelian
      </a>

      @if(in_array(Auth::user()->role, ['admin','bendahara']))
        <div class="menu-title mt-3">Keuangan</div>

        <a class="nav-link-custom {{ request()->routeIs('sumberdana.*') ? 'active' : '' }}" href="{{ route('sumberdana.index') }}">
          <i class="bi bi-cash-coin"></i> Sumber Dana
        </a>

        <a class="nav-link-custom {{ request()->routeIs('funding.*') ? 'active' : '' }}" href="{{ route('funding.index') }}">
          <i class="bi bi-cash-coin"></i> Dana Cair
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
      @endif

      @if(Auth::user()->role === 'admin')
        <div class="menu-title mt-3">Administrasi</div>
        <a class="nav-link-custom {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
          <i class="bi bi-people"></i> Management User
        </a>
      @endif
    </aside>

    <div class="backdrop" id="backdrop"></div>

    <main class="content">
      <div class="content-inner">
        @yield('content')
      </div>
    </main>
  </div> {{-- ✅ PENTING: PENUTUP .app (di file kamu tadi belum ada) --}}

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const sidebar  = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('backdrop');

    const openSidebar = ()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    const closeSidebar = ()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }

    toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    backdrop?.addEventListener('click', closeSidebar);
  </script>

  @stack('scripts')
</body>
</html>
