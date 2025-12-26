<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ isset($project) ? 'Edit Project' : 'Tambah Project' }}</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <meta name="csrf-token" content="{{ csrf_token() }}">

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

    .nav-badge{
      margin-left:auto;
      min-width:20px; height:20px;
      padding:0 6px;
      border-radius:999px;
      display:inline-flex; align-items:center; justify-content:center;
      font-size:.72rem; font-weight:800;
      background:var(--danger);
      color:#fff;
      box-shadow:0 10px 18px rgba(239,68,68,.22);
    }

    .content{ flex:1; padding:18px 18px 22px; }

    /* HERO ala dashboard */
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
    .tools-left{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }
    .tools-right{
      margin-left:auto;
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }

    .btn-apply{
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
      white-space:nowrap;
    }
    .btn-apply:hover{ filter:brightness(.98); transform:translateY(-1px); }
    .btn-apply i{ line-height:1; }

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

    @media(max-width:991.98px){
      .tools-right{ width:100%; margin-left:0; justify-content:flex-start; }
    }

    /* Form card */
    .form-card{
      background:var(--card);
      border:1px solid rgba(226,232,240,.95);
      border-radius:22px;
      box-shadow:var(--shadow);
      overflow:hidden;
    }
    .form-card .card-header{
      padding:14px 16px;
      background:
        radial-gradient(700px 140px at 0% 0%, rgba(22,163,74,.10), transparent 60%),
        linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.78));
      border-bottom:1px solid rgba(226,232,240,.95);
      font-weight:800;
    }
    .form-card .card-body{ padding:18px 16px; }

    .form-label{ font-weight:700; color:rgba(15,23,42,.88); }
    .form-control, .form-select{
      border-radius:14px;
      border:1px solid rgba(226,232,240,.95);
      font-weight:600;
    }
    .form-control:focus, .form-select:focus{
      box-shadow:0 0 0 .2rem rgba(22,163,74,.14);
      border-color:rgba(22,163,74,.45);
    }

    .form-section-title{
      font-weight:800;
      font-size:1.02rem;
      margin:14px 0 10px;
      color:var(--ink);
      display:flex;
      align-items:center;
      gap:8px;
    }

    .btn-brand{
      height:38px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      border-radius:999px;
      font-weight:800;
      padding:0 16px;
      background:linear-gradient(135deg,var(--brand-700),var(--brand));
      border:0;
      box-shadow:0 16px 28px rgba(22,163,74,.18);
      color:#fff;
    }
    .btn-brand:hover{ filter:brightness(.98); transform:translateY(-1px); color:#fff; }

    .help-text{ color:var(--ink-600); font-size:.85rem; font-weight:600; }

    /* mobile sidebar */
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

    <!-- Main Content -->
    <main class="content">

      <!-- HERO -->
      <section class="hero">
        <div class="hero-inner">
          <div class="hero-left">
            <h1 class="title">{{ isset($project) ? 'Edit Project' : 'Tambah Project' }}</h1>
            <p class="sub">Lengkapi detail project penelitian dan pendanaannya.</p>
          </div>

          <div class="tools-row">
            <div class="tools-left"></div>

            <div class="tools-right">
              <a href="{{ route('project.index') }}" class="btn btn-sm btn-soft">
                <i class="bi bi-arrow-left-short"></i> Kembali ke Daftar
              </a>
            </div>
          </div>
        </div>
      </section>

      @if ($message = Session::get('success'))
        <div class="alert alert-success">{{ $message }}</div>
      @endif
      @if ($message = Session::get('error'))
        <div class="alert alert-danger">{{ $message }}</div>
      @endif

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
          <strong>{{ isset($project) ? 'Form Edit Project' : 'Form Tambah Project' }}</strong>
        </div>

        <div class="card-body">
          <form id="projectForm" action="{{ isset($project) ? route('project.update', $project->id) : route('project.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($project)) @method('PUT') @endif

            <div class="row g-3">
              <div class="col-md-6">
                <label for="nama_project" class="form-label">Nama Project</label>
                <input type="text" id="nama_project" name="nama_project" class="form-control" placeholder="Cth: E-Sniffer"
                  value="{{ old('nama_project', $project->nama_project ?? '') }}" required>
              </div>

              <div class="col-md-3">
                <label for="tahun" class="form-label">Tahun</label>
                <input type="number" id="tahun" name="tahun" class="form-control" placeholder="Contoh: 2025" min="2015" max="2035"
                  value="{{ old('tahun', $project->tahun ?? '') }}" required>
              </div>

              <div class="col-md-3">
                <label for="durasi" class="form-label">Durasi Project</label>
                <input type="text" id="durasi" name="durasi" class="form-control" placeholder="Cth: 6 Bulan / 1 Tahun"
                  value="{{ old('durasi', $project->durasi ?? '') }}" required>
              </div>

              <div class="col-12">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4" placeholder="Deskripsi singkat project..." required>{{ old('deskripsi', $project->deskripsi ?? '') }}</textarea>
              </div>

              <div class="col-md-6">
                <label for="file_proposal" class="form-label">File Proposal (PDF)</label>
                <input type="file" id="file_proposal" name="file_proposal" class="form-control" accept=".pdf" @if(!isset($project)) required @endif>
                @if(isset($project) && $project->file_proposal)
                  <div class="help-text mt-1">File saat ini: <a href="{{ asset('storage/' . $project->file_proposal) }}" target="_blank">Lihat Proposal</a></div>
                @endif
              </div>

              <div class="col-md-6">
                <label for="file_rab" class="form-label">File RAB (XLSX)</label>
                <input type="file" id="file_rab" name="file_rab" class="form-control" accept=".xlsx" @if(!isset($project)) required @endif>
                @if(isset($project) && $project->file_rab)
                  <div class="help-text mt-1">File saat ini: <a href="{{ asset('storage/' . $project->file_rab) }}" target="_blank">Lihat RAB</a></div>
                @endif
              </div>
            </div>

            <hr class="my-4">

            <!-- Sumber Dana -->
            @php
              $current_sumber_dana = isset($project) && $project->sumberDana ? ($project->sumberDana->jenis_pendanaan ?? 'internal') : old('sumber_dana','internal');
            @endphp
            <input type="hidden" id="current_sumber_dana" value="{{ $current_sumber_dana }}">
            <input type="hidden" id="current_kategori_id" value="{{ $project->id_sumber_dana ?? '' }}">

            <div class="form-section-title"><i class="bi bi-cash-coin"></i> Sumber Dana</div>

            <div class="row g-3">
              <div class="col-md-4">
                <label for="sumber_dana" class="form-label">Jenis Pendanaan</label>
                <select id="sumber_dana" name="sumber_dana" class="form-select">
                  <option value="internal" {{ $current_sumber_dana=='internal' ? 'selected' : '' }}>Internal</option>
                  <option value="eksternal" {{ $current_sumber_dana=='eksternal' ? 'selected' : '' }}>Eksternal</option>
                </select>
              </div>

              <div class="col-md-8" id="wrap-kategori-internal" style="{{ $current_sumber_dana=='eksternal' ? 'display:none' : '' }}">
                <label class="form-label">Kategori Pendanaan Internal</label>
                <select name="kategori_pendanaan_internal" id="kategori_pendanaan_internal" class="form-select">
                  @foreach ($sumber_internal as $si)
                    <option value="{{ $si->id }}"
                      {{ (isset($project) && ($project->id_sumber_dana==$si->id)) || old('kategori_pendanaan_internal')==$si->id ? 'selected':'' }}>
                      {{ $si->nama_sumber_dana }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-8" id="wrap-kategori-eksternal" style="{{ $current_sumber_dana=='internal' ? 'display:none' : '' }}">
                <label class="form-label">Kategori Pendanaan Eksternal</label>
                <select name="kategori_pendanaan_eksternal" id="kategori_pendanaan_eksternal" class="form-select">
                  @foreach ($sumber_eksternal as $se)
                    <option value="{{ $se->id }}"
                      {{ (isset($project) && ($project->id_sumber_dana==$se->id)) || old('kategori_pendanaan_eksternal')==$se->id ? 'selected':'' }}>
                      {{ $se->nama_sumber_dana }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <!-- Subkategori (auto) -->
            <div id="subkategori_pendanaan_container" class="mt-3" style="display:none;">
              <div class="form-section-title"><i class="bi bi-list-check"></i> Detail Pendanaan</div>
              <div id="subkategori_pendanaan" class="row g-3"></div>
              <div class="help-text">Isi nominal tiap sub-kategori (otomatis diformat Rupiah).</div>
            </div>

            <div class="d-flex gap-2 mt-4 flex-wrap">
              <button class="btn btn-brand" type="submit">
                <i class="bi bi-check2-circle"></i> {{ isset($project) ? 'Update' : 'Submit' }}
              </button>
              <a href="{{ route('project.index') }}" class="btn btn-sm btn-soft">Batal</a>
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
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('backdrop');
    function openSidebar(){ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    function closeSidebar(){ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
    toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    backdrop?.addEventListener('click', closeSidebar);
  </script>

  <script>
    (function(){
      const sumberDana = document.getElementById('sumber_dana');
      const wrapInt   = document.getElementById('wrap-kategori-internal');
      const wrapEks   = document.getElementById('wrap-kategori-eksternal');
      const selInt    = document.getElementById('kategori_pendanaan_internal');
      const selEks    = document.getElementById('kategori_pendanaan_eksternal');
      const contWrap  = document.getElementById('subkategori_pendanaan_container');
      const contBody  = document.getElementById('subkategori_pendanaan');

      const isEditMode = {!! isset($project) ? 'true' : 'false' !!};
      const projectId  = {!! isset($project) ? (int)$project->id : 'null' !!};

      function rupiahMask(el){
        el.addEventListener('input', function(){
          const digits = this.value.replace(/\D/g,'');
          if(!digits){ this.value=''; return; }
          this.value = 'Rp. ' + new Intl.NumberFormat('id-ID').format(Number(digits));
        });
      }
      function toNumber(str){ return Number((str||'').toString().replace(/\D/g,'') || 0); }

      function buildFields(items){
        contBody.innerHTML = '';
        items.forEach(item=>{
          const col = document.createElement('div');
          col.className = 'col-md-6';
          col.innerHTML = `
            <label class="form-label" for="${item.nama_form}">${item.nama}</label>
            <input type="text" class="form-control rupiah" id="${item.nama_form}" name="${item.nama_form}" placeholder="Cth: Rp. 1.000.000">
          `;
          contBody.appendChild(col);
        });
        contWrap.style.display = items.length ? 'block' : 'none';
        contBody.querySelectorAll('.rupiah').forEach(rupiahMask);
      }

      async function loadSubkategoriPendanaan(katId){
        if(!katId) return;
        try{
          const res = await fetch(`/project/sumberdana/${katId}`);
          const data = await res.json(); // [{nama, nama_form}, ...]
          buildFields(data || []);

          if(isEditMode && projectId){
            const r2 = await fetch(`/project/${projectId}/subcategories`);
            const exists = await r2.json(); // [{nama_form, nominal}, ...]
            exists?.forEach(sc=>{
              const input = document.querySelector(`[name="${sc.nama_form}"]`);
              if(input){
                input.value = sc.nominal ? ('Rp. ' + new Intl.NumberFormat('id-ID').format(sc.nominal)) : '';
              }
            });
          }
        }catch(e){
          console.error('loadSubkategoriPendanaan error', e);
          contWrap.style.display='none';
        }
      }

      function toggleKategori(){
        if(sumberDana.value==='eksternal'){
          wrapInt.style.display='none';
          wrapEks.style.display='block';
          loadSubkategoriPendanaan(selEks.value);
        }else{
          wrapEks.style.display='none';
          wrapInt.style.display='block';
          loadSubkategoriPendanaan(selInt.value);
        }
      }

      // bersihkan rupiah -> angka polos saat submit
      document.getElementById('projectForm').addEventListener('submit', function(){
        this.querySelectorAll('.rupiah').forEach(inp=> inp.value = toNumber(inp.value));
      });

      sumberDana.addEventListener('change', toggleKategori);
      selInt.addEventListener('change', ()=> sumberDana.value==='internal' && loadSubkategoriPendanaan(selInt.value));
      selEks.addEventListener('change', ()=> sumberDana.value==='eksternal' && loadSubkategoriPendanaan(selEks.value));

      // init
      toggleKategori();
    })();
  </script>
</body>
</html>
