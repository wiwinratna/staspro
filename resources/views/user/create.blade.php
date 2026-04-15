<!DOCTYPE html>
<html lang="id">
<head>
  {{-- @extends('layouts.app')  ❌ hapus biar nggak dobel layout --}}
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah User</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0;
      --bg:#f6f7fb; --card:#ffffff;
      --shadow: 0 10px 30px rgba(15,23,42,.08);
      --radius: 18px;
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
      display:flex; align-items:center; gap:10px;
      font-weight:800; letter-spacing:.2px;
      white-space:nowrap;
    }
    .brand-badge{
      font-size:.72rem; font-weight:800;
      padding:.22rem .55rem; border-radius:999px;
      background:rgba(255,255,255,.16);
      border:1px solid rgba(255,255,255,.22);
    }

    /* Layout */
    .app{ display:flex; min-height:calc(100vh - 56px); }
    .sidebar{
      width:260px;
      background:var(--card);
      border-right:1px solid var(--line);
      padding:14px;
      position:sticky;
      top:56px;
      height:calc(100vh - 56px);
      overflow:auto;
    }

    .menu-title{
      font-size:.72rem;
      letter-spacing:.08em;
      color:var(--ink-600);
      text-transform:uppercase;
      margin:10px 0 8px;
      font-weight:800;
    }

    .nav-link-custom{
      display:flex; align-items:center; gap:10px;
      padding:9px 10px;
      border-radius:14px;
      text-decoration:none;
      color:var(--ink);
      font-weight:650;
      font-size:.92rem;
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
      font-weight:800;
    }

    .content{ flex:1; padding:18px 18px 22px; }

    /* Header/Hero */
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
    .hero h1{
      font-size:1.55rem;
      font-weight:850;
      margin:0;
      letter-spacing:-.2px;
    }
    .hero p{
      margin:6px 0 0;
      color:var(--ink-600);
      font-weight:500;
    }

    /* Card Form */
    .form-card{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      box-shadow:var(--shadow);
      overflow:hidden;
    }
    .form-card .card-header{
      background:#fff;
      border-bottom:1px solid rgba(226,232,240,.95);
      padding:14px 18px;
      font-weight:850;
    }
    .form-card .card-body{ padding:18px; }

    /* Inputs */
    .form-control, .form-select{
      border-radius:14px;
      padding:.72rem .9rem;
      border:1px solid rgba(226,232,240,.95);
      font-weight:650;
    }
    .form-control:focus, .form-select:focus{
      border-color:rgba(22,163,74,.45);
      box-shadow:0 0 0 .2rem rgba(22,163,74,.12);
    }
    .help-text{
      color:var(--ink-600);
      font-size:.85rem;
      font-weight:550;
    }

    /* Buttons */
    .btn-brand{
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      border:0;
      color:#fff;
      font-weight:850;
      border-radius:999px;
      padding:.62rem 1.05rem;
      box-shadow:0 14px 26px rgba(22,163,74,.18);
    }
    .btn-brand:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }
    .btn-ghost{
      border-radius:999px;
      font-weight:850;
      padding:.62rem 1.05rem;
    }

    /* Mobile sidebar */
    .backdrop{
      display:none; position:fixed; inset:0;
      background:rgba(15,23,42,.38); z-index:1035;
    }
    .backdrop.show{ display:block; }
    @media (max-width: 991.98px){
      .sidebar{
        position:fixed; left:-290px; top:56px;
        height:calc(100vh - 56px);
        z-index:1040; transition:left .2s;
      }
      .sidebar.open{ left:0; }
      .content{ padding:14px; }
    }
  </style>
</head>

<body>
  <!-- Topbar -->
  <nav class="navbar topbar">
    <div class="container-fluid">
      <button class="btn btn-outline-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>

      <div class="brand">
        <span>STAS-RG</span>
        </div>

      <div class="ms-auto">@include('navbar')</div>
    </div>
  </nav>

  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="appSidebar">
      @include('layouts.sidebar-menu')
    </aside>

    <div class="backdrop" id="backdrop"></div>

    <!-- Content -->
    <main class="content">
      <section class="hero d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
          <h1>Tambah User</h1>
          <p>Tambahkan akun baru untuk admin atau peneliti.</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-light btn-ghost">
          <i class="bi bi-arrow-left-short me-1"></i> Kembali
        </a>
      </section>

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="form-card card">
        <div class="card-header">Form Tambah User</div>

        <div class="card-body">
          <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control"
                       value="{{ old('name') }}" placeholder="Nama lengkap" required>
              </div>

              <div class="col-md-6">
                <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" class="form-control"
                       value="{{ old('email') }}" placeholder="nama@email.com" required>
              </div>

              <div class="col-md-6">
                <label for="role" class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                <select class="form-select" id="role" name="role" required>
                  <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>Admin</option>
                  <option value="peneliti" {{ old('role')=='peneliti' ? 'selected' : '' }}>Peneliti</option>
                  <option value="bendahara" {{ old('role')=='bendahara' ? 'selected' : '' }}>Bendahara</option>
                </select>
                <div class="help-text mt-1">Role menentukan hak akses menu di sistem.</div>
              </div>

              <div class="col-md-6"></div>

              <div class="col-md-6">
                <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" id="password" name="password" class="form-control"
                         placeholder="Minimal 6 karakter" required minlength="6"
                         style="border-top-right-radius:0;border-bottom-right-radius:0;">
                  <button type="button" class="btn btn-outline-secondary" onclick="togglePw('password', this)"
                          style="border-top-right-radius:14px;border-bottom-right-radius:14px;border-color:rgba(226,232,240,.95);">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <div class="col-md-6">
                <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                         placeholder="Ketik ulang password" required minlength="6"
                         style="border-top-right-radius:0;border-bottom-right-radius:0;">
                  <button type="button" class="btn btn-outline-secondary" onclick="togglePw('password_confirmation', this)"
                          style="border-top-right-radius:14px;border-bottom-right-radius:14px;border-color:rgba(226,232,240,.95);">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <div class="d-flex gap-2 mt-4">
                <button class="btn btn-brand" type="submit">
                  <i class="bi bi-check2-circle me-1"></i> Submit
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-ghost">Batal</a>
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

    function togglePw(id, btn){
      const inp = document.getElementById(id);
      const icon = btn.querySelector('i');
      if(inp.type === 'password'){
        inp.type = 'text';
        icon.classList.replace('bi-eye','bi-eye-slash');
      } else {
        inp.type = 'password';
        icon.classList.replace('bi-eye-slash','bi-eye');
      }
    }
  </script>

  <script>
    @if (session('success'))
      Swal.fire({ icon:'success', title:'Sukses', text:@json(session('success')), confirmButtonText:'OK' });
    @endif
    @if (session('error'))
      Swal.fire({ icon:'error', title:'Gagal', text:@json(session('error')), confirmButtonText:'OK' });
    @endif
  </script>
</body>
</html>
