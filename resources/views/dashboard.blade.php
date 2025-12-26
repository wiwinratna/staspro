<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

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

    /* KPI cards */
    .kpi-card{
      background:linear-gradient(160deg,var(--brand),var(--brand-700));
      color:#fff; border:0; border-radius:18px; padding:20px;
      box-shadow:0 10px 24px rgba(22,163,74,.18);
      transition:transform .18s ease, box-shadow .18s ease;
    }
    .kpi-card:hover{ transform:translateY(-2px); box-shadow:0 16px 34px rgba(22,163,74,.28); }
    .kpi-label{ font-weight:600; opacity:.95; }
    .kpi-value{ font-size:1.65rem; font-weight:800; line-height:1.15; margin-top:4px; }

    /* badge chip */
    .kpi-chip {
      display:inline-block; padding:.18rem .5rem; font-size:.8rem; font-weight:700;
      border-radius:999px; background:#ef4444; color:#fff;
    }
    .kpi-chip-dot {
      display:inline-block; width:.45rem; height:.45rem; border-radius:999px;
      background:#fff; opacity:.9; margin-right:.35rem;
    }

    /* Chart card */
    .chart-card{
      background:var(--card); border:1px solid var(--line); border-radius:18px; padding:18px;
      box-shadow:0 6px 16px rgba(15,23,42,.06);
      height: 420px; display:flex; flex-direction:column;
    }
    .chart-head{ display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
    .chart-body{ flex:1; min-height:0; }
    .chart-body canvas{ width:100% !important; height:100% !important; }

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
      <div class="brand-title">STAS-RG • Dashboard</div>
      <div class="ms-auto">
        @include('navbar')
      </div>
    </div>
  </nav>

  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="appSidebar">
      <div class="menu-title">Menu</div>
      <a class="nav-link-custom active" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a class="nav-link-custom" href="{{ route('project.index') }}"><i class="bi bi-kanban"></i> Project</a>
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
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2">
        <div>
          <div class="page-title">Dashboard</div>
          <div class="page-sub">Ringkasan metrik dan grafik per proyek.</div>
        </div>
      </div>

      <!-- KPI Row -->
      <div class="row g-3">
        @if (Auth::user()->role == 'admin')
        <div class="col-12 col-md-6 col-lg-4">
          <a href="{{ route('pencatatan_keuangan') }}" class="text-decoration-none">
            <div class="kpi-card h-100">
              <div class="kpi-label">Total Pencatatan Keuangan Bulan Ini</div>
              <div class="kpi-value">Rp {{ number_format($totalTransactions, 0, ',', '.') }}</div>
            </div>
          </a>
        </div>
        @endif

        <div class="col-12 col-md-6 col-lg-4">
          <a href="{{ route('project.index') }}" class="text-decoration-none">
            <div class="kpi-card h-100">
              <div class="kpi-label">Jumlah Project</div>
              <div class="kpi-value">{{ $totalProjects }}</div>
            </div>
          </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
          <a href="{{ route('requestpembelian.index') }}" class="text-decoration-none">
            <div class="kpi-card h-100">
              <div class="d-flex justify-content-between align-items-start">
                <div class="kpi-label">Total Request Pembelian</div>
                @if(($newRequests ?? 0) > 0)
                  <span class="kpi-chip">
                    <span class="kpi-chip-dot"></span>{{ $newRequests }} baru
                  </span>
                @endif
              </div>
              <div class="kpi-value mt-1">{{ $totalRequests }}</div>
            </div>
          </a>
        </div>
      </div>

      <!-- Chart -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="chart-card">
            <div class="chart-head">
              <h5 class="m-0">Grafik Pencatatan Keuangan per Proyek</h5>
            </div>
            <div class="chart-body">
              <canvas id="grafikPengeluaranProject"></canvas>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Sidebar toggle
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.querySelector('.backdrop');
    const openSidebar = ()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    const closeSidebar = ()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
    toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    backdrop?.addEventListener('click', closeSidebar);

    // Chart.js
    const ctx = document.getElementById('grafikPengeluaranProject').getContext('2d');
    const labels = {!! json_encode($namaProjects) !!};
    const dataVal = {!! json_encode($pengeluaranPerProject) !!};

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Total Pencatatan Keuangan (Rp)',
          data: dataVal,
          backgroundColor: 'rgba(22,163,74,0.25)',
          borderColor: 'rgba(22,163,74,1)',
          borderWidth: 2,
          borderRadius: 8
        }]
      },
      options: {
        maintainAspectRatio: false,
        plugins: {
          legend: { labels: { color: '#0f172a' } },
          tooltip: {
            callbacks: {
              label: (ctx) => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y || 0)
            }
          }
        },
        scales: {
          x: {
            ticks: { color: '#475569' },
            grid: { color: 'rgba(2,6,23,.06)' }
          },
          y: {
            beginAtZero: true,
            ticks: {
              color: '#475569',
              callback: (v)=> new Intl.NumberFormat('id-ID',{notation:'compact'}).format(v)
            },
            grid: { color: 'rgba(2,6,23,.06)' }
          }
        }
      }
    });
  </script>
</body>
</html>
