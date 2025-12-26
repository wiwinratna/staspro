<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <title>Laporan Keuangan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

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
    .action-card{ background:var(--card); border:1px solid var(--line); border-radius:18px; padding:16px; box-shadow:0 6px 16px rgba(15,23,42,.06); }

    /* Filters row */
    .filters{ display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
    .form-select-sm{ padding-top:.35rem; padding-bottom:.35rem; }

    /* Table */
    .table-wrap{ background:var(--card); border:1px solid var(--line); border-radius:18px; overflow:hidden; box-shadow:0 6px 16px rgba(15,23,42,.06); }
    .table-modern{ margin:0; vertical-align:middle; }
    .table-modern thead th{
      background:#f9fafb; color:var(--ink-600); font-weight:700;
      border-bottom:1px solid var(--line); position:sticky; top:0; z-index:1;
    }
    .table-modern tbody tr:hover{ background:#fafafa; }
    .table-modern td, .table-modern th{ padding:.85rem .9rem; }

    .btn-brand{ background:var(--brand); border-color:var(--brand); color:#fff; }
    .btn-brand:hover{ background:var(--brand-700); border-color:var(--brand-700); }
    .btn-outline-brand{ border-color:var(--brand); color:var(--brand-700); }
    .btn-outline-brand:hover{ background:var(--brand); color:#fff; }

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
      <div class="brand-title">STAS-RG • Laporan Keuangan</div>
      <div class="ms-auto">@include('navbar')</div>
    </div>
  </nav>

  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="appSidebar">
      <div class="menu-title">Menu</div>
      <a class="nav-link-custom" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a class="nav-link-custom" href="{{ route('project.index') }}"><i class="bi bi-kanban"></i> Project</a>
      <a class="nav-link-custom" href="{{ route('requestpembelian.index') }}"><i class="bi bi-bag-check"></i> Request Pembelian</a>

      @if (Auth::user()->role == 'admin')
        <div class="menu-title mt-3">Administrasi</div>
        <a class="nav-link-custom" href="{{ route('sumberdana.index') }}"><i class="bi bi-cash-coin"></i> Sumber Dana</a>
        <a class="nav-link-custom" href="{{ route('pencatatan_keuangan') }}"><i class="bi bi-journal-text"></i> Pencatatan Keuangan</a>
        <a class="nav-link-custom active" href="{{ route('laporan_keuangan') }}"><i class="bi bi-graph-up"></i> Laporan Keuangan</a>
        <a class="nav-link-custom" href="{{ route('users.index') }}"><i class="bi bi-people"></i> Management User</a>
      @endif
    </aside>
    <div class="backdrop" id="backdrop"></div>

    <!-- Main -->
    <main class="content">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-2">
        <div>
          <div class="page-title">Laporan Keuangan</div>
          <div class="page-sub">Filter, unduh, dan lihat ringkasan transaksi debit/kredit.</div>
        </div>
      </div>

      <!-- Filter + Download -->
      <div class="action-card mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
          <form id="filter-form" method="GET" action="{{ route('laporan_keuangan') }}" class="flex-grow-1">
            <div class="filters">
              <label class="fw-semibold me-1 mb-0">Filter:</label>

              <select name="tim_peneliti" id="tim_peneliti" class="form-select form-select-sm" style="max-width:260px">
                <option value="">Semua Tim Peneliti</option>
                @foreach ($projects as $project)
                  <option value="{{ $project->id }}" {{ request()->tim_peneliti == $project->id ? 'selected' : '' }}>
                    {{ $project->nama_project }}
                  </option>
                @endforeach
              </select>

              <select name="metode_pembayaran" id="metode_pembayaran" class="form-select form-select-sm" style="max-width:220px">
                <option value="">Semua Metode Pembayaran</option>
                <option value="cash" {{ request()->metode_pembayaran == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="transfer bank" {{ request()->metode_pembayaran == 'transfer bank' ? 'selected' : '' }}>Transfer Bank</option>
              </select>

              <select name="sumber_dana" id="sumber_dana" class="form-select form-select-sm" style="max-width:220px">
                <option value="">Semua Sumber Dana</option>
                <option value="internal"  {{ request()->sumber_dana == 'internal'  ? 'selected' : '' }}>Internal</option>
                <option value="eksternal" {{ request()->sumber_dana == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
              </select>

              <a href="{{ route('laporan_keuangan') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center" title="Reset">
                <i class="bi bi-arrow-counterclockwise"></i>
              </a>
            </div>
          </form>

          <div class="ms-auto d-flex gap-2">
            <a class="btn btn-success btn-sm"
               href="{{ route('laporan.export', 'excel') }}?tim_peneliti={{ request('tim_peneliti') }}&metode_pembayaran={{ request('metode_pembayaran') }}&sumber_dana={{ request('sumber_dana') }}">
              <i class="bi bi-file-earmark-excel me-1"></i> Unduh Excel
            </a>
            <a class="btn btn-danger btn-sm"
               href="{{ route('laporan.export', 'pdf') }}?tim_peneliti={{ request('tim_peneliti') }}&metode_pembayaran={{ request('metode_pembayaran') }}&sumber_dana={{ request('sumber_dana') }}">
              <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
            </a>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="table-wrap">
        <div class="table-responsive">
          <table class="table table-modern table-striped text-center align-middle">
            <thead>
              <tr>
                <th style="width:64px">No.</th>
                <th style="min-width:140px">Tanggal</th>
                <th style="min-width:200px">Tim Peneliti</th>
                <th style="min-width:260px">Deskripsi Pencatatan Keuangan</th>
                <th style="min-width:160px">Metode Pembayaran</th>
                <th style="min-width:140px">Sumber Dana</th>
                <th class="text-end" style="min-width:140px">Debit (Rp)</th>
                <th class="text-end" style="min-width:140px">Kredit (Rp)</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pencatatanKeuangans as $index => $pencatatanKeuangan)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $pencatatanKeuangan->created_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
                  <td>{{ $pencatatanKeuangan->project->nama_project ?? '-' }}</td>
                  <td class="text-start">{{ $pencatatanKeuangan->deskripsi_transaksi }}</td>
                  <td>{{ ucfirst($pencatatanKeuangan->metode_pembayaran) }}</td>
                  <td>{{ ucwords($pencatatanKeuangan->project->sumberDana->jenis_pendanaan ?? '-') }}</td>
                  <td class="text-end">
                    @if($pencatatanKeuangan->jenis_transaksi === 'pemasukan')
                      Rp. {{ number_format($pencatatanKeuangan->jumlah_transaksi, 0, ',', '.') }}
                    @endif
                  </td>
                  <td class="text-end">
                    @if($pencatatanKeuangan->jenis_transaksi === 'pengeluaran')
                      Rp. {{ number_format($pencatatanKeuangan->jumlah_transaksi, 0, ',', '.') }}
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th colspan="6" class="text-end">Total</th>
                <th class="text-end">Rp. {{ number_format($totalDebit, 0, ',', '.') }}</th>
                <th class="text-end">Rp. {{ number_format($totalKredit, 0, ',', '.') }}</th>
              </tr>
            </tfoot>
          </table>
        </div>
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

    // Auto submit filters
    document.addEventListener('DOMContentLoaded', () => {
      ['tim_peneliti','metode_pembayaran','sumber_dana'].forEach(id=>{
        document.getElementById(id)?.addEventListener('change', ()=> document.getElementById('filter-form').submit());
      });
    });
  </script>
</body>
</html>
