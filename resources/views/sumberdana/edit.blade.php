<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit Sumber Dana</title>

  <!-- Fonts & Icons -->
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

      --shadow:0 10px 30px rgba(15,23,42,.08);
      --shadow2:0 18px 40px rgba(15,23,42,.10);
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

    .tools-row{
      margin-top:14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
    }
    .tools-left, .tools-right{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }
    .tools-right{ margin-left:auto; }

    .btn-brand{
      height:38px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      border-radius:999px;
      font-weight:800;
      padding:0 14px;
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      border:0;
      box-shadow:0 16px 28px rgba(22,163,74,.18);
      color:#fff;
      white-space:nowrap;
      text-decoration:none;
    }
    .btn-brand:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }

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
      overflow:visible; /* IMPORTANT: biar dropdown tomselect gak kepotong */
    }
    .card-soft .card-header{
      background:#f8fafc;
      border-bottom:1px solid rgba(226,232,240,.95);
      font-weight:900;
      padding:14px 16px;
    }
    .card-soft .card-body{ padding:16px; overflow:visible; }

    .section-title{
      font-weight:900;
      color:var(--ink-600);
      text-transform:uppercase;
      letter-spacing:.06em;
      font-size:.82rem;
      margin-bottom:6px;
    }

    /* TomSelect tweak */
    .ts-control{
      border-radius:14px !important;
      border-color:rgba(226,232,240,.95) !important;
      padding:10px 12px !important;
      box-shadow:none !important;
      min-height:44px !important;
    }
    .ts-wrapper.multi .ts-control>div{
      border-radius:999px !important;
      padding:3px 10px !important;
      font-weight:800 !important;
      background:var(--brand-50) !important;
      border:1px solid rgba(22,163,74,.18) !important;
      color:var(--brand-700) !important;
    }
    .ts-dropdown{
      z-index:2000 !important;
      border-radius:16px !important;
      overflow:hidden !important;
      box-shadow:0 18px 40px rgba(15,23,42,.12) !important;
    }
    .ts-dropdown .ts-dropdown-content{
      max-height:260px !important;
    }

    /* Mobile */
    .backdrop{
      display:none;
      position:fixed;
      inset:0;
      background:rgba(15,23,42,.38);
      z-index:1035;
    }
    .backdrop.show{ display:block; }

    @media(max-width:991px){
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

    <!-- HERO -->
    <section class="hero">
      <div class="hero-inner">
        <div class="hero-left">
          <h1 class="title">Edit Sumber Dana</h1>
          <p class="sub">Perbarui data sumber dana dan subkategori terkait.</p>
        </div>

        <div class="tools-row">
          <div class="tools-left">
            <a href="{{ route('sumberdana.index') }}" class="btn btn-soft">
              <i class="bi bi-arrow-left-short"></i> Kembali ke Daftar
            </a>
          </div>

          <div class="tools-right">
            <a
              href="https://drive.google.com/file/d/1NicpoYzDkSk64F3HfVEDWt1tpk0WvrlI/view?usp=sharing"
              target="_blank"
              rel="noopener"
              class="btn btn-soft"
              title="Buka Manual Book"
            >
              <i class="bi bi-journal-bookmark"></i> Manual Book
            </a>
          </div>
        </div>
      </div>
    </section>

    @if ($errors->any())
      <div class="alert alert-danger mt-3">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- FORM -->
    <div class="card card-soft">
      <div class="card-header">Form Edit Sumber Dana</div>
      <div class="card-body">

        <!-- IMPORTANT: route kamu hanya support POST -->
        <form action="{{ route('sumberdana.update', $sumberdana->id) }}" method="POST">
          @csrf


          <div class="row g-3">
            <div class="col-md-4">
              <label for="tipe_project" class="form-label fw-semibold">Tipe Project</label>
              <select id="tipe_project" name="tipe_project" class="form-select">
                <option value="Penelitian" {{ old('tipe_project', $sumberdana->tipe_project) == 'Penelitian' ? 'selected' : '' }}>Penelitian</option>
                <option value="Abdimas"    {{ old('tipe_project', $sumberdana->tipe_project) == 'Abdimas' ? 'selected' : '' }}>Abdimas</option>
              </select>
            </div>

            <div class="col-md-4">
              <label for="nama_sumber_dana" class="form-label fw-semibold">Nama Sumber Dana</label>
              <input
                type="text"
                id="nama_sumber_dana"
                name="nama_sumber_dana"
                class="form-control"
                value="{{ old('nama_sumber_dana', $sumberdana->nama_sumber_dana) }}"
              >
            </div>

            <div class="col-md-4">
              <label for="jenis_pendanaan" class="form-label fw-semibold">Jenis Sumber Dana</label>
              <select id="jenis_pendanaan" name="jenis_pendanaan" class="form-select">
                <option value="internal"  {{ old('jenis_pendanaan', $sumberdana->jenis_pendanaan) == 'internal' ? 'selected' : '' }}>Internal</option>
                <option value="eksternal" {{ old('jenis_pendanaan', $sumberdana->jenis_pendanaan) == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
              </select>
            </div>
          </div>

          <div class="my-4" style="height:1px;background:var(--line);"></div>

          <div class="section-title">Subkategori</div>
          <div class="text-muted mb-2" style="font-size:.92rem;">
            Ketik untuk mencari/menambah subkategori. Bisa pilih lebih dari satu.
          </div>

          <select id="subkategori" name="subkategori[]" multiple>
            @php
              $oldSubs = collect(old('subkategori', $subkategori->pluck('nama')->toArray()));
            @endphp

            @if(!empty($listSubkategori))
              @foreach($listSubkategori as $item)
                @php
                  $nama = is_string($item)
                    ? $item
                    : (is_array($item) ? ($item['nama'] ?? '') : ($item->nama ?? ''));

                  $nama = trim((string) $nama);
                @endphp

                @if($nama !== '')
                  <option value="{{ $nama }}" @selected($oldSubs->contains($nama))>
                    {{ $nama }}
                  </option>
                @endif
              @endforeach
            @endif
          </select>

          <div class="mt-4 d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-brand">
              <i class="bi bi-check2-circle"></i> Simpan Perubahan
            </button>
            <a href="{{ route('sumberdana.index') }}" class="btn btn-soft">Batal</a>
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
  // sidebar mobile toggle
  const sidebar = document.getElementById('appSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const backdrop = document.getElementById('backdrop');

  const openSidebar = ()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
  const closeSidebar = ()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }

  toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
  backdrop?.addEventListener('click', closeSidebar);

  // TomSelect subkategori
  new TomSelect('#subkategori',{
    create:true,
    persist:false,
    plugins:['remove_button'],
    placeholder:'Cari / tambah subkategori…',
    maxItems:null,
    sortField:{ field:'text', direction:'asc' }
  });
</script>
</body>
</html>
