<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <title>Sumber Dana</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0; --bg:#f6f7fb; --card:#fff;
    }
    *{ box-sizing:border-box; }
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

    .action-card{ background:var(--card); border:1px solid var(--line); border-radius:18px; }
    .table-wrap{ background:var(--card); border:1px solid var(--line); border-radius:18px; overflow:hidden; }
    .table-modern{ margin:0; vertical-align:middle; }
    .table-modern thead th{ background:#f9fafb; color:var(--ink-600); font-weight:700; border-bottom:1px solid var(--line); position:sticky; top:0; z-index:1; }
    .table-modern td,.table-modern th{ padding:.9rem .9rem; }
    .shadow-soft{ box-shadow:0 6px 16px rgba(15,23,42,.06); }

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
      <div class="brand-title">STAS-RG • Administrasi</div>
      <div class="ms-auto">
        @include('navbar')
      </div>
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
        <a class="nav-link-custom active" href="{{ route('sumberdana.index') }}"><i class="bi bi-cash-coin"></i> Sumber Dana</a>
        <a class="nav-link-custom" href="{{ route('pencatatan_keuangan') }}"><i class="bi bi-journal-text"></i> Pencatatan Keuangan</a>
        <a class="nav-link-custom" href="{{ route('laporan_keuangan') }}"><i class="bi bi-graph-up"></i> Laporan Keuangan</a>
        <a class="nav-link-custom" href="{{ route('users.index') }}"><i class="bi bi-people"></i> Management User</a>
      @endif
    </aside>
    <div class="backdrop" id="backdrop"></div>

    <!-- Main -->
    <main class="content">
      <!-- Header -->
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2">
        <div>
          <div class="page-title">Sumber Dana</div>
          <div class="page-sub">Kelola daftar sumber & jenis pendanaan.</div>
        </div>

        <a href="{{ route('sumberdana.create') }}" class="btn btn-brand shadow-soft">
          <i class="bi bi-plus-lg me-1"></i> Input Sumber Dana
        </a>
      </div>

      <!-- Notif -->
      @if ($message = Session::get('success'))
        <div class="alert alert-success mt-3 mb-0">{{ $message }}</div>
      @endif
      @if ($message = Session::get('error'))
        <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
      @endif

      <!-- Table -->
      <div class="table-wrap shadow-soft mt-3">
        <div class="table-responsive">
          <table id="table" class="table table-modern table-striped align-middle">
            <thead>
              <tr>
                <th>Nama Sumber Dana</th>
                <th class="text-center">Jenis Pendanaan</th>
                <th class="text-center" style="width:140px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($sumberdana as $r)
                <tr>
                  <td>{{ $r->nama_sumber_dana }}</td>
                  <td class="text-center">{{ Str::title($r->jenis_pendanaan) }}</td>
                  <td class="text-center">
                    <a href="{{ route('sumberdana.edit', $r->id) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Edit">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('sumberdana.destroy', $r->id) }}" method="POST" class="d-inline" data-id="{{ $r->id }}">
                      @csrf @method('DELETE')
                      <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $r->id }})" data-bs-toggle="tooltip" title="Hapus">
                        <i class="bi bi-trash-fill"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.querySelector('.backdrop');
    function closeSidebar(){ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
    function openSidebar(){ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    backdrop?.addEventListener('click', closeSidebar);

    // DataTable
    $(function(){
      const dt = new DataTable('#table', {
        paging: true,
        searching: true,
        info: true,
        ordering: true,
        language: {
          search: "Cari:",
          lengthMenu: "Tampil _MENU_ data",
          info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
          paginate: { previous: "‹", next: "›" }
        },
      });
    });

    // SweetAlert Delete
    function confirmDelete(id){
      Swal.fire({
        title: 'Hapus data ini?',
        text: 'Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
      }).then((result)=>{
        if(result.isConfirmed){
          const form = document.querySelector(`form[data-id="${id}"]`);
          form?.submit();
        }
      });
    }
  </script>
</body>
</html>
