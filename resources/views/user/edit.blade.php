<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit User</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0; --bg:#f6f7fb; --card:#fff;
      --shadow:0 10px 30px rgba(15,23,42,.08);
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
      font-weight:800; letter-spacing:.2px;
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
      width:260px; background:var(--card);
      border-right:1px solid var(--line);
      padding:14px;
      position:sticky; top:56px;
      height:calc(100vh - 56px);
      overflow:auto;
    }

    .menu-title{
      font-size:.72rem; letter-spacing:.08em; color:var(--ink-600);
      text-transform:uppercase; margin:8px 0; font-weight:700;
    }

    .nav-link-custom{
      display:flex; align-items:center; gap:10px;
      padding:9px 10px; border-radius:14px; text-decoration:none;
      color:var(--ink); font-weight:600; font-size:.92rem; line-height:1;
      transition:.18s; white-space:nowrap;
    }
    .nav-link-custom i{ font-size:1.05rem; }
    .nav-link-custom:hover{ background:var(--brand-50); color:var(--brand-700); transform:translateX(2px); }
    .nav-link-custom.active{
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      color:#fff; box-shadow:0 16px 28px rgba(2,6,23,.12); font-weight:700;
    }

    .content{ flex:1; padding:18px 18px 22px; }

    /* Card */
    .card-wrap{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      box-shadow:var(--shadow);
      overflow:hidden;
    }
    .card-head{
      padding:14px 16px;
      border-bottom:1px solid rgba(226,232,240,.95);
      background:linear-gradient(180deg,#ffffff,#fbfdff);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      flex-wrap:wrap;
    }
    .title{
      margin:0;
      font-size:1.35rem;
      font-weight:900;
      letter-spacing:-.2px;
    }
    .sub{
      margin:2px 0 0;
      color:var(--ink-600);
      font-weight:600;
      font-size:.92rem;
    }
    .card-body{ padding:16px; }

    .btn-soft{
      height:38px; display:inline-flex; align-items:center; gap:8px;
      border-radius:999px; font-weight:800; padding:0 14px;
      background:#fff; color:var(--ink);
      border:1px solid rgba(226,232,240,.95);
      box-shadow:0 10px 26px rgba(15,23,42,.05);
      white-space:nowrap; text-decoration:none;
    }
    .btn-soft:hover{ background:var(--brand-50); transform:translateY(-1px); color:var(--brand-700); }

    .btn-brand{
      height:38px; display:inline-flex; align-items:center; gap:8px;
      border-radius:999px; font-weight:900; padding:0 16px;
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      border:0; color:#fff;
      box-shadow:0 16px 28px rgba(22,163,74,.18);
      text-decoration:none; white-space:nowrap;
    }
    .btn-brand:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }

    /* Form */
    .form-label{ font-weight:800; font-size:.9rem; }
    .form-control, .form-select{
      border-radius:14px;
      border:1px solid rgba(226,232,240,.95);
      padding:.7rem .85rem;
      font-weight:600;
      box-shadow:0 10px 26px rgba(15,23,42,.04);
    }
    .form-control:focus, .form-select:focus{
      border-color:rgba(22,163,74,.45);
      box-shadow:0 0 0 .2rem rgba(22,163,74,.12);
    }

    /* Mobile */
    .backdrop{
      display:none; position:fixed; inset:0;
      background:rgba(15,23,42,.38); z-index:1035;
    }
    .backdrop.show{ display:block; }
    @media(max-width:991px){
      .sidebar{ position:fixed; left:-290px; top:56px; height:calc(100vh - 56px); z-index:1040; transition:left .2s; }
      .sidebar.open{ left:0; }
      .content{ padding:14px; }
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
      </div>

    <div class="ms-auto">@include('navbar')</div>
  </div>
</nav>

<div class="app">

  <!-- SIDEBAR -->
  <aside class="sidebar" id="appSidebar">
      @include('layouts.sidebar-menu')
    </aside>

  <div class="backdrop" id="backdrop"></div>

  <!-- CONTENT -->
  <main class="content">

    <div class="card-wrap">
      <div class="card-head">
        <div>
          <h1 class="title">Edit User</h1>
          <div class="sub">Ubah data user (nama, email, role).</div>
        </div>
        <a href="{{ route('users.index') }}" class="btn-soft">
          <i class="bi bi-arrow-left"></i> Kembali ke Daftar User
        </a>
      </div>

      <div class="card-body">
        <form action="{{ route('users.update', $user->id) }}" method="POST" class="row g-3">
          @csrf
          @method('PUT')

          <div class="col-md-6">
            <label for="name" class="form-label">Nama</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
              <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
              <option value="peneliti" {{ old('role', $user->role) == 'peneliti' ? 'selected' : '' }}>Peneliti</option>
              <option value="bendahara" {{ old('role', $user->role) == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
            </select>
          </div>

          <div class="col-md-6"></div>

          <div class="col-12">
            <hr class="my-1">
            <p class="form-label" style="color:var(--ink-600);font-weight:700;font-size:.85rem;margin:8px 0 0;">
              <i class="bi bi-shield-lock me-1"></i> Ubah Password
              <span style="font-weight:500;color:#94a3b8;"> — kosongkan jika tidak ingin mengubah</span>
            </p>
          </div>

          <div class="col-md-6">
            <label for="password" class="form-label">Password Baru</label>
            <div class="input-group">
              <input type="password" id="password" name="password" class="form-control"
                     placeholder="Minimal 6 karakter" minlength="6"
                     style="border-top-right-radius:0;border-bottom-right-radius:0;">
              <button type="button" class="btn btn-outline-secondary" onclick="togglePw('password', this)"
                      style="border-top-right-radius:14px;border-bottom-right-radius:14px;border-color:rgba(226,232,240,.95);">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="col-md-6">
            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
            <div class="input-group">
              <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                     placeholder="Ketik ulang password baru" minlength="6"
                     style="border-top-right-radius:0;border-bottom-right-radius:0;">
              <button type="button" class="btn btn-outline-secondary" onclick="togglePw('password_confirmation', this)"
                      style="border-top-right-radius:14px;border-bottom-right-radius:14px;border-color:rgba(226,232,240,.95);">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          @if ($errors->any())
            <div class="col-12">
              <div class="alert alert-danger mb-0">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif

          <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn-brand">
              <i class="bi bi-check2-circle"></i> Update
            </button>
            <a href="{{ route('users.index') }}" class="btn-soft">
              <i class="bi bi-x-circle"></i> Batal
            </a>
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
  // sidebar mobile toggle
  const sidebar = document.getElementById('appSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const backdrop = document.getElementById('backdrop');

  const openSidebar = ()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
  const closeSidebar = ()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }

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

  @if (session('success'))
    Swal.fire({ icon:'success', title:'Sukses', text:'{{ session('success') }}', timer:2000, showConfirmButton:false });
  @endif

  @if (session('error'))
    Swal.fire({ icon:'error', title:'Gagal', text:'{{ session('error') }}' });
  @endif
</script>

</body>
</html>
