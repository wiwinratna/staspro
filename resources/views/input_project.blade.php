<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ isset($project) ? 'Edit Project' : 'Tambah Project' }}</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

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
    .form-section-title{ font-weight:700; font-size:1rem; margin:14px 0 10px; color:var(--ink); }

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
          <div class="page-title">{{ isset($project) ? 'Edit Project' : 'Tambah Project' }}</div>
          <div class="page-sub">Lengkapi detail project penelitian dan pendanaannya.</div>
        </div>
        <a href="{{ route('project.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left-short me-1"></i> Kembali ke Daftar
        </a>
      </div>

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

            <div class="form-section-title">Sumber Dana</div>
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
              <div class="form-section-title">Detail Pendanaan</div>
              <div id="subkategori_pendanaan" class="row g-3"></div>
              <div class="help-text">Isi nominal tiap sub-kategori (otomatis diformat Rupiah).</div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button class="btn btn-brand px-4" type="submit">
                <i class="bi bi-check2-circle me-1"></i> {{ isset($project) ? 'Update' : 'Submit' }}
              </button>
              <a href="{{ route('project.index') }}" class="btn btn-outline-secondary">Batal</a>
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
