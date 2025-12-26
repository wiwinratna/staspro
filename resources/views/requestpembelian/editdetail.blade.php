<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Request Pembelian</title>

  <!-- Fonts & CSS -->
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
      display:flex; align-items:center; gap:10px;
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

    /* HERO */
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
      font-size:1.55rem;
      font-weight:800;
      margin:0;
      letter-spacing:-.2px;
    }
    .hero-left .sub{
      margin:6px 0 0;
      color:var(--ink-600);
      font-weight:500;
    }

    .tools-row{
      margin-top:14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
    }
    .btn-soft{
      height:38px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      border-radius:999px;
      font-weight:800;
      padding:0 14px;
      background:#fff;
      color:var(--ink);
      border:1px solid rgba(226,232,240,.95);
      box-shadow:0 10px 26px rgba(15,23,42,.05);
      white-space:nowrap;
      text-decoration:none;
    }
    .btn-soft:hover{
      background:var(--brand-50);
      transform:translateY(-1px);
      color:var(--brand-700);
      border-color:rgba(226,232,240,.95);
    }

    /* Card */
    .card-soft{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      box-shadow:var(--shadow);
    }
    .card-soft .card-body{ padding:18px; }

    .form-label{
      font-weight:800;
      color:var(--ink);
      font-size:.92rem;
    }
    .form-control, .form-select{
      border-radius:14px;
      border:1px solid rgba(226,232,240,.95);
      padding:.65rem .85rem;
      font-weight:600;
    }
    .help-text{ color:var(--ink-600); font-size:.85rem; font-weight:500; }

    /* Mobile sidebar */
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

  @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        <span class="brand-badge">{{ Auth::user()->role === 'admin' ? 'ADMIN' : 'PENELITI' }}</span>
      </div>

      <div class="ms-auto">
        @include('navbar')
      </div>
    </div>
  </nav>

  @php
    // Ambil status header item ini
    $header = \App\Models\RequestpembelianHeader::find($detail->id_request_pembelian_header);

    $rawStatus = $header->status_request ?? '';
    $status = strtolower(trim($rawStatus));
    $status = str_replace(' ', '_', $status);

    // RULE: hanya boleh edit saat submit_request
    $canEditThis = ($status === 'submit_request');

    // optional: admin boleh bypass? (kalau kamu mau admin tetap bisa edit, ubah jadi: Auth::user()->role==='admin' || $status==='submit_request'
    // tapi kamu bilang: "harusnya kalo submit request baru boleh edit" -> jadi kita patenin.
  @endphp

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

    <!-- Main -->
    <main class="content">

      <!-- HERO -->
      <section class="hero">
        <div class="hero-inner">
          <div class="hero-left">
            <h1 class="title">Edit Item Request Pembelian</h1>
            <p class="sub">Perbarui detail item request pembelian (hanya saat status <b>Submit Request</b>).</p>
          </div>

          <div class="tools-row">
            <div></div>
            <a href="{{ route('requestpembelian.detail', $detail->id_request_pembelian_header) }}" class="btn-soft">
              <i class="bi bi-arrow-left-short"></i> Kembali
            </a>
          </div>
        </div>
      </section>

      @if(!$canEditThis)
        <div class="alert alert-warning">
          <div class="fw-bold mb-1"><i class="bi bi-lock me-1"></i> Item terkunci</div>
          Item hanya bisa diedit saat status request masih <b>Submit Request</b>.
          Status sekarang: <b>{{ strtoupper($status) }}</b>.
        </div>
      @endif

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger">
          <div class="fw-bold mb-1">Gagal menyimpan:</div>
          <ul class="mb-0">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card card-soft">
        <div class="card-body">
          <form action="{{ route('requestpembelian.updatedetail', $detail->id) }}" method="POST" class="row g-3" id="formEditItem">
            @csrf
            <input type="hidden" name="id_request_pembelian_header" value="{{ $detail->id_request_pembelian_header }}">

            <div class="col-md-6">
              <label for="nama_barang" class="form-label">Nama Barang</label>
              <input type="text" id="nama_barang" name="nama_barang" value="{{ $detail->nama_barang }}"
                     class="form-control" required @if(!$canEditThis) disabled @endif>
            </div>

            <div class="col-md-3">
              <label for="kuantitas" class="form-label">Kuantitas</label>
              <input type="number" id="kuantitas" name="kuantitas" value="{{ $detail->kuantitas }}" min="1"
                     class="form-control" required @if(!$canEditThis) disabled @endif>
            </div>

            <div class="col-md-3">
              <label for="harga" class="form-label">Harga</label>
              <input type="text" id="harga" name="harga" value="{{ number_format($detail->harga,0,',','.') }}"
                     class="form-control" placeholder="Rp 0" required @if(!$canEditThis) disabled @endif>
              <div class="help-text">Otomatis diformat Rp saat mengetik.</div>
            </div>

            <div class="col-12">
              <label for="link_pembelian" class="form-label">Link Pembelian</label>
              <input type="url" id="link_pembelian" name="link_pembelian" value="{{ $detail->link_pembelian }}"
                     class="form-control" placeholder="https://..." required @if(!$canEditThis) disabled @endif>
            </div>

            <div class="col-md-6">
              <label for="id_subkategori_sumberdana" class="form-label">Subkategori Sumber Dana</label>
              <select class="form-select" id="id_subkategori_sumberdana" name="id_subkategori_sumberdana" @if(!$canEditThis) disabled @endif>
                <option value="">-- Pilih Subkategori --</option>
                @foreach ($subkategori as $sub)
                  <option value="{{ $sub->id }}" {{ $detail->id_subkategori_sumberdana == $sub->id ? 'selected' : '' }}>
                    {{ $sub->nama }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Bukti Bayar</label>
              @if(!empty($detail->bukti_bayar))
                <div class="help-text mt-1">
                  Bukti saat ini:
                  <a href="{{ asset('bukti_bayar/'.$detail->bukti_bayar) }}" target="_blank" rel="noreferrer">Lihat</a>
                </div>
              @else
                <div class="help-text mt-1">Belum ada bukti bayar. Upload dilakukan di menu <b>Upload Bukti</b> saat status Approve Request.</div>
              @endif
            </div>

            <div class="col-12 d-flex gap-2 mt-2">
              @if($canEditThis)
                <button class="btn btn-success">
                  <i class="bi bi-check2-circle me-1"></i> Submit
                </button>
              @endif
              <a href="{{ route('requestpembelian.detail', $detail->id_request_pembelian_header) }}" class="btn btn-outline-secondary">Batal</a>
            </div>

          </form>
        </div>
      </div>

    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Sidebar toggle (mobile)
    const sidebar=document.getElementById('appSidebar');
    const toggleBtn=document.getElementById('sidebarToggle');
    const backdrop=document.getElementById('backdrop');
    const openSidebar=()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    const closeSidebar=()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
    toggleBtn?.addEventListener('click',()=> sidebar.classList.contains('open')?closeSidebar():openSidebar());
    backdrop?.addEventListener('click',closeSidebar);

    // Format Rupiah untuk input harga
    (function(){
      const el=document.getElementById('harga');
      const form=document.getElementById('formEditItem');
      if(!el || !form) return;

      const toDigits = (s) => (s || '').toString().replace(/\D/g,'');
      const fmt = (n) => n ? ('Rp ' + new Intl.NumberFormat('id-ID').format(Number(n))) : '';

      // set awal
      const raw = toDigits(el.value);
      el.value = fmt(raw);

      el.addEventListener('input', () => {
        const digits = toDigits(el.value);
        el.value = fmt(digits);
      });

      form.addEventListener('submit', () => {
        el.value = toDigits(el.value); // kirim angka murni
      });
    })();
  </script>
</body>
</html>
