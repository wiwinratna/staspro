<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tambah Sumber Dana</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet"/>

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

    *{ box-sizing:border-box; }
    body{
      margin:0;
      background:var(--bg);
      font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      color:var(--ink);
      font-weight:500;
    }

    /* Topbar (sama dashboard) */
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
    }
    .brand-badge{
      font-size:.72rem; font-weight:700;
      padding:.22rem .55rem;
      border-radius:999px;
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

    /* Hero (sama dashboard) */
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
    .hero-row{
      position:relative; z-index:2;
      display:flex; align-items:flex-start; justify-content:space-between;
      gap:14px; flex-wrap:wrap;
    }
    .hero-left{ min-width:260px; }
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

    /* Button kecil ala dashboard */
    .btn-pill{
      height:36px;
      display:inline-flex; align-items:center; gap:8px;
      padding:0 14px;
      border-radius:999px;
      font-weight:700;
      border:1px solid rgba(226,232,240,.95);
      text-decoration:none;
      white-space:nowrap;
      transition:.15s;
    }
    .btn-back{
      background:#fff;
      color:var(--ink);
      box-shadow:0 12px 24px rgba(15,23,42,.06);
    }
    .btn-back:hover{
      background:var(--brand-50);
      color:var(--brand-700);
      transform:translateY(-1px);
    }

    /* Card (lebih dashboard) */
    .card-soft{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      box-shadow:var(--shadow);
      overflow:hidden;
    }
    .card-soft .card-header{
      background:
        radial-gradient(700px 140px at 0% 0%, rgba(22,163,74,.10), transparent 60%),
        linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.78));
      border-bottom:1px solid rgba(226,232,240,.95);
      font-weight:800;
    }

    /* Form tweak */
    .form-control, .form-select{
      border-radius:14px;
      border:1px solid rgba(226,232,240,.95);
      font-weight:600;
    }
    .form-text{ color:var(--ink-600); font-weight:500; }
    .form-section-title{
      font-weight:800;
      font-size:1.02rem;
      margin:12px 0 8px;
      color:var(--ink);
    }

    .btn-primary-soft{
      height:38px;
      border-radius:999px;
      font-weight:800;
      padding:0 14px;
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      border:0;
      box-shadow:0 16px 28px rgba(22,163,74,.18);
      white-space:nowrap;
    }
    .btn-primary-soft:hover{ filter:brightness(.98); transform:translateY(-1px); }
    .btn-outline-soft{
      height:38px;
      border-radius:999px;
      font-weight:800;
      padding:0 14px;
      background:#fff;
      border:1px solid rgba(226,232,240,.95);
      box-shadow:0 12px 24px rgba(15,23,42,.06);
    }
    .btn-outline-soft:hover{
      background:var(--brand-50);
      color:var(--brand-700);
      transform:translateY(-1px);
    }

    /* Tom Select biar nyatu */
    .ts-wrapper .ts-control{
      border-radius:14px !important;
      border:1px solid rgba(226,232,240,.95) !important;
      padding:10px 12px !important;
      box-shadow:none !important;
      font-weight:600;
    }
    .ts-dropdown{
      border-radius:14px !important;
      border:1px solid rgba(226,232,240,.95) !important;
      overflow:hidden;
    }

    /* Mobile sidebar (sama dashboard) */
    .backdrop{
      display:none;
      position:fixed;
      inset:0;
      background:rgba(15,23,42,.38);
      z-index:1035;
    }
    .backdrop.show{ display:block; }

    @media (max-width: 991.98px){
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
  <!-- TOPBAR -->
  <nav class="navbar topbar">
    <div class="container-fluid">
      <button class="btn btn-outline-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>

      <div class="brand">
        <span>STAS-RG</span>
        <span class="brand-badge">{{ Auth::user()->role === 'admin' ? 'ADMIN' : 'PENELITI' }}</span>
      </div>

      <div class="ms-auto">
        @include('navbar')
      </div>
    </div>
  </nav>

  <div class="app">
    <!-- SIDEBAR -->
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

    <!-- CONTENT -->
    <main class="content">

      <!-- HERO -->
      <section class="hero">
        <div class="hero-row">
          <div class="hero-left">
            <h1 class="title">Tambah Sumber Dana</h1>
            <p class="sub">Buat sumber dana dan tentukan subkategori pendanaannya.</p>
          </div>

          <div>
            <a href="{{ route('sumberdana.index') }}" class="btn-pill btn-back">
              <i class="bi bi-arrow-left-short"></i> Kembali ke Daftar
            </a>
          </div>
        </div>
      </section>

      @if ($errors->any())
        <div class="alert alert-danger">
          <strong>Terjadi kesalahan:</strong>
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card card-soft">
        <div class="card-header">Form Tambah Sumber Dana</div>
        <div class="card-body">
          <form action="{{ route('sumberdana.store') }}" method="POST">
            @csrf

            <div class="row g-3">
              <div class="col-md-6">
                <label for="nama_sumber_dana" class="form-label fw-semibold">Nama Sumber Dana</label>
                <input type="text"
                       id="nama_sumber_dana"
                       name="nama_sumber_dana"
                       class="form-control @error('nama_sumber_dana') is-invalid @enderror"
                       placeholder="Contoh: DRPTM, KEDAIREKA, LPDP"
                       value="{{ old('nama_sumber_dana') }}">
                <div class="form-text">Harus unik & belum pernah digunakan.</div>
                @error('nama_sumber_dana') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="jenis_pendanaan" class="form-label fw-semibold">Jenis Pendanaan</label>
                <select id="jenis_pendanaan"
                        name="jenis_pendanaan"
                        class="form-select @error('jenis_pendanaan') is-invalid @enderror">
                  <option value="internal"  {{ old('jenis_pendanaan')=='internal'  ? 'selected' : '' }}>Internal</option>
                  <option value="eksternal" {{ old('jenis_pendanaan')=='eksternal' ? 'selected' : '' }}>Eksternal</option>
                </select>
                <div class="form-text">Pilih jenis sesuai sumber dana.</div>
                @error('jenis_pendanaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <hr class="my-4">

            <div class="form-section-title">Subkategori</div>
            <p class="text-muted mb-2" style="font-weight:500;">
              Ketik untuk mencari/menambah subkategori. Bisa pilih lebih dari satu.
              <em>(Jika memilih yang sudah ada, nilainya tetap akan disimpan sebagai teks nama agar cocok dengan validasi controller kamu.)</em>
            </p>

            <select id="subkategori" name="subkategori[]" multiple autocomplete="off">
              @if(!empty($listSubkategori))
                @foreach($listSubkategori as $item)
                  <option value="{{ $item->nama }}"
                    @if(collect(old('subkategori', []))->contains($item->nama)) selected @endif>
                    {{ $item->nama }}
                  </option>
                @endforeach
              @endif
            </select>

            <div class="mt-4 d-flex flex-wrap gap-2">
              <button type="submit" class="btn btn-primary-soft text-white">
                <i class="bi bi-check2-circle"></i> Submit
              </button>

              <a href="{{ route('sumberdana.index') }}" class="btn btn-outline-soft">
                Batal
              </a>
            </div>

          </form>
        </div>
      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
  <script>
    // Sidebar toggle (mobile)
    const sidebar=document.getElementById('appSidebar');
    const toggleBtn=document.getElementById('sidebarToggle');
    const backdrop=document.getElementById('backdrop');

    const openSidebar=()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    const closeSidebar=()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }

    toggleBtn?.addEventListener('click',()=> sidebar.classList.contains('open')?closeSidebar():openSidebar());
    backdrop?.addEventListener('click',closeSidebar);

    // Tom Select
    new TomSelect('#subkategori', {
      create: true,
      persist: false,
      plugins: ['remove_button'],
      placeholder: 'Cari / tambah subkategori…',
      maxItems: null,
      sortField: {field:'text', direction:'asc'},
      render: {
        option: function(data, escape){
          return '<div>' + escape(data.text) + '</div>';
        }
      }
    });
  </script>
</body>
</html>
