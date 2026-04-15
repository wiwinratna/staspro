{{-- resources/views/layouts/panel.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>@yield('title','Dashboard')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

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

      --sidebar-w: 308px;
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
      font-size:14px;
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
      padding:12px 10px 14px;

      position:sticky;
      top:var(--topbar-h);
      height:calc(100vh - var(--topbar-h));
      overflow:auto;

      z-index: 1025;            /* ✅ PENTING */
    }

    .menu-title{
      font-size:.70rem; letter-spacing:.08em;
      color:var(--ink-600); text-transform:uppercase;
      margin:10px 6px 8px; font-weight:800;
    }

    .nav-link-custom{
      display:flex; align-items:center; gap:9px;
      padding:8px 10px; border-radius:10px;
      text-decoration:none; color:var(--ink);
      font-weight:600; font-size:.82rem; line-height:1.25;
      transition:.16s;
      white-space:normal;
      word-break:break-word;
    }
    .nav-link-custom .menu-text{
      min-width:0;
      overflow-wrap:anywhere;
      line-height:1.25;
    }
    .nav-link-custom i{ font-size:.92rem; }
    .nav-link-custom:hover{
      background:var(--brand-50); color:var(--brand-700);
      transform:translateX(1px);
    }
    .nav-link-custom.active{
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff; box-shadow:0 8px 16px rgba(2,6,23,.10);
    }
    .nav-link-custom.menu-open{
      background:#f1f5f9;
      color:#0f172a;
      border:1px solid #e2e8f0;
      box-shadow:none;
    }

    .sidebar .collapse > div{
      margin-left:4px !important;
      margin-top:4px !important;
      padding-left:4px;
      border-left:1px solid #e5e7eb;
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

    /* Global typography biar tidak kebesaran */
    h1{font-size:1.7rem}
    h2{font-size:1.45rem}
    h3{font-size:1.25rem}
    h4{font-size:1.12rem}
    h5{font-size:1rem}
    .card, .table, .form-control, .form-select, .btn{font-size:.88rem}

    /* Konsistensi tombol aksi pada semua tabel */
    .table th:last-child,
    .table td:last-child{
      white-space:nowrap;
    }
    .table td .btn,
    .table td .btn-act,
    .table td .btn-icon{
      border-radius:8px !important;
      min-height:31px;
      padding:.27rem .56rem;
      font-size:.78rem;
      font-weight:700;
      line-height:1.1;
      white-space:nowrap;
    }
    .table td .action-btns,
    .table td .actions-wrap{
      display:inline-flex;
      align-items:center;
      gap:6px;
      flex-wrap:nowrap !important;
      white-space:nowrap;
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
        left:-320px;
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
      @include('layouts.sidebar-menu')
    </aside>


    <div class="backdrop" id="backdrop"></div>

    <main class="content">
      <div class="content-inner">
        @yield('content')
      </div>
    </main>
  </div> 

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
