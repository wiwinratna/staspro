<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah User</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0; --bg:#f6f7fb; --card:#ffffff;
    }
    *{box-sizing:border-box}
    body{ background:var(--bg); font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; color:var(--ink); }

    /* Topbar */
    .topbar{ background:linear-gradient(135deg,var(--brand-700),var(--brand)); color:#fff; }

    /* Shell */
    .app{ display:flex; min-height:calc(100vh - 56px); }
    .sidebar{
      width:260px; background:var(--card); border-right:1px solid var(--line);
      padding:18px; position:sticky; top:0; height:calc(100vh - 56px);
    }
    .menu-title{
      font-size:.8rem; letter-spacing:.06em; color:var(--ink-600);
      text-transform:uppercase; margin:6px 0 10px; font-weight:600;
    }
    .nav-link-custom{
      display:flex; align-items:center; gap:10px; padding:10px 12px;
      color:var(--ink); border-radius:12px; text-decoration:none; transition:all .18s;
      font-weight:500;
    }
    .nav-link-custom:hover{ background:var(--brand-50); color:var(--brand-700); }
    .nav-link-custom.active{ background:var(--brand); color:#fff; box-shadow:0 6px 16px rgba(22,163,74,.18); }

    .content{ flex:1; padding:24px; }
    .page-title{ font-size:1.5rem; font-weight:700; margin-bottom:4px; }
    .page-sub{ color:var(--ink-600); margin-bottom:18px; }

    /* Card form */
    .form-card{
      background:var(--card); border:1px solid var(--line); border-radius:18px;
      box-shadow:0 8px 22px rgba(15,23,42,.06);
    }
    .form-card .card-header{ background:#fff; border-bottom:1px solid var(--line); }
    .form-card .card-body{ padding:20px; }

    .btn-brand{ background:var(--brand); border-color:var(--brand); color:#fff; }
    .btn-brand:hover{ background:var(--brand-700); border-color:var(--brand-700); }
    .help-text{ color:var(--ink-600); font-size:.85rem; }

    @media (max-width: 991.98px){
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
      <div class="ms-auto">
        @include('navbar')
      </div>
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

      @if (Auth::user()->role == 'admin')
        <div class="menu-title mt-3">Administrasi</div>
        <a class="nav-link-custom {{ request()->routeIs('sumberdana.*') ? 'active' : '' }}" href="{{ route('sumberdana.index') }}">
          <i class="bi bi-cash-coin"></i> Sumber Dana
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

    <!-- Main Content -->
    <main class="content">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-2">
        <div>
          <div class="page-title">Tambah User</div>
          <div class="page-sub">Tambahkan akun baru untuk admin atau peneliti.</div>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left-short me-1"></i> Kembali ke Daftar
        </a>
      </div>

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card form-card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Form Tambah User</strong>
        </div>

        <div class="card-body">
          <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control"
                       value="{{ old('name') }}" placeholder="Nama lengkap" required>
              </div>

              <div class="col-md-6">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" class="form-control"
                       value="{{ old('email') }}" placeholder="nama@email.com" required>
              </div>

              <div class="col-md-6">
                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                <select class="form-select" id="role" name="role" required>
                  <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>Admin</option>
                  <option value="peneliti" {{ old('role')=='peneliti' ? 'selected' : '' }}>Peneliti</option>
                </select>
                <div class="help-text mt-1">Role menentukan hak akses menu di sistem.</div>
              </div>

              {{-- OPTIONAL kalau kamu memang mau auto password:
              <div class="col-md-6">
                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                <input type="text" id="password" name="password" class="form-control"
                       value="{{ old('password') }}" placeholder="Password otomatis..." required>
                <div class="help-text mt-1">Bisa otomatis terisi sesuai role.</div>
              </div>
              --}}

              <div class="d-flex gap-2 mt-4">
                <button class="btn btn-brand px-4" type="submit">
                  <i class="bi bi-check2-circle me-1"></i> Submit
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('backdrop');
    function openSidebar(){ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    function closeSidebar(){ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
    toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    backdrop?.addEventListener('click', closeSidebar);
  </script>

  {{-- ✅ SweetAlert dari session --}}
  <script>
    @if (session('success'))
      Swal.fire({ icon:'success', title:'Sukses', text:@json(session('success')), confirmButtonText:'OK' });
    @endif

    @if (session('error'))
      Swal.fire({ icon:'error', title:'Gagal', text:@json(session('error')), confirmButtonText:'OK' });
    @endif
  </script>

  {{-- ❌ Script lama dihapus karena field #password tidak ada.
      Kalau kamu mau fitur auto password, aktifkan blok password di atas,
      lalu pakai script ini: --}}
  {{--
  <script>
    document.getElementById('role').addEventListener('change', function() {
      const role = this.value;
      const passwordField = document.getElementById('password');
      if (!passwordField) return;
      passwordField.value = role === 'admin' ? 'Admin@123' : 'User@123';
    });
  </script>
  --}}
</body>
</html>
