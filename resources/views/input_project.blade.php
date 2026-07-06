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
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

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

    /* Select2 Customization */
    .select2-container--bootstrap-5 .select2-selection {
      border-radius: 14px !important;
      border: 1px solid rgba(226,232,240,.95) !important;
      background-color: #fff !important;
      font-weight: 600;
      min-height: 46px !important;
      padding: 4px 6px !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
      box-shadow:0 0 0 .2rem rgba(22,163,74,.14);
      border-color:rgba(22,163,74,.45);
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      padding-top: 4px;
      padding-bottom: 4px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
      background: rgba(22,163,74,.12) !important;
      color: #166534 !important;
      border: 1px solid rgba(22,163,74,.20) !important;
      border-radius: 999px !important;
      padding: 4px 10px 4px 12px !important;
      margin: 0 !important;
      font-weight: 800;
      font-size: 0.85rem;
      display: flex !important;
      align-items: center;
      flex-direction: row-reverse;
      gap: 6px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
      color: #166534 !important;
      font-size: 1.1rem !important;
      font-weight: bold;
      border: none !important;
      background: transparent !important;
      margin: 0 !important;
      padding: 0 !important;
      display: flex;
      line-height: 1;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
      color: #dc2626 !important;
    }

    /* Select2 Dropdown Styling */
    .select2-dropdown {
      border-radius: 16px !important;
      border: 1px solid rgba(226,232,240,.95) !important;
      box-shadow: 0 10px 30px rgba(15,23,42,.08) !important;
      overflow: hidden;
      margin-top: 4px;
    }
    .select2-search--dropdown .select2-search__field {
      border-radius: 10px !important;
      border: 1px solid rgba(226,232,240,.95) !important;
      padding: 8px 14px !important;
      font-size: 0.95rem;
    }
    .select2-search--dropdown .select2-search__field:focus {
      outline: none;
      border-color: rgba(22,163,74,.45) !important;
      box-shadow: 0 0 0 .2rem rgba(22,163,74,.10) !important;
    }
    .select2-results__options {
      padding: 6px !important;
    }
    .select2-results__option {
      border-radius: 10px !important;
      padding: 10px 14px !important;
      margin-bottom: 2px;
      font-weight: 600;
      color: var(--ink-700);
      font-size: 0.92rem;
      transition: all 0.15s ease;
    }
    .select2-results__option--highlighted[aria-selected],
    .select2-results__option:hover {
      background-color: var(--brand-50) !important;
      color: var(--brand-700) !important;
    }
    .select2-results__option[aria-selected="true"] {
      background-color: rgba(22,163,74,.12) !important;
      color: #166534 !important;
      font-weight: 800;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__clear {
      width: 24px;
      height: 24px;
      background: #f1f5f9;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #64748b;
      margin-top: 8px;
      margin-right: 4px;
      transition: 0.2s;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__clear:hover {
      background: #fee2e2;
      color: #dc2626;
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
        </div>

      <div class="ms-auto">
        @include('navbar')
      </div>
    </div>
  </nav>

  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="appSidebar">
      @include('layouts.sidebar-menu')
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
              <div class="col-md-12">
                <label for="nama_project" class="form-label">Nama Project</label>
                <input type="text" id="nama_project" name="nama_project" class="form-control" placeholder="Cth: E-Sniffer"
                  value="{{ old('nama_project', $project->nama_project ?? '') }}" required>
              </div>

              <div class="col-md-3">
                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" 
                  value="{{ old('tanggal_mulai', isset($project->tanggal_mulai) && $project->tanggal_mulai ? \Carbon\Carbon::parse($project->tanggal_mulai)->format('Y-m-d') : '') }}" required>
              </div>

              <div class="col-md-3">
                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" 
                  value="{{ old('tanggal_selesai', isset($project->tanggal_selesai) && $project->tanggal_selesai ? \Carbon\Carbon::parse($project->tanggal_selesai)->format('Y-m-d') : '') }}" required>
              </div>

              <div class="col-md-3">
                <label for="tahun" class="form-label">Tahun</label>
                <input type="number" id="tahun" name="tahun" class="form-control"
                  value="{{ old('tahun', $project->tahun ?? '') }}" readonly required>
              </div>

              <div class="col-md-3">
                <label for="durasi" class="form-label">Durasi</label>
                <input type="text" id="durasi" name="durasi" class="form-control"
                  value="{{ old('durasi', $project->durasi ?? '') }}" readonly required>
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

            <!-- Skema Pendanaan -->
            @php
              $current_skema = isset($project) ? $project->skema_pendanaan_id : old('skema_pendanaan_id');
            @endphp
            <input type="hidden" id="is_edit_mode" value="{{ isset($project) ? '1' : '0' }}">
            <input type="hidden" id="project_id" value="{{ $project->id ?? '' }}">

            <div class="form-section-title"><i class="bi bi-diagram-3"></i> Skema Pendanaan</div>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="skema_pendanaan_id" class="form-label">Pilih Skema Pendanaan</label>
                <select id="skema_pendanaan_id" name="skema_pendanaan_id" class="form-select" {{ isset($project) ? 'disabled' : 'required' }}>
                  <option value="">-- Pilih Skema --</option>
                  @foreach($skema_pendanaan as $s)
                    <option value="{{ $s->id }}" {{ $current_skema == $s->id ? 'selected' : '' }}>
                      {{ $s->kode }} - {{ $s->nama }}
                    </option>
                  @endforeach
                </select>
                @if(isset($project))
                  <input type="hidden" name="skema_pendanaan_id" value="{{ $project->skema_pendanaan_id }}">
                  <div class="help-text mt-1 text-warning"><i class="bi bi-exclamation-triangle"></i> Skema pendanaan tidak dapat diubah setelah project dibuat.</div>
                @endif
              </div>
              <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border" id="skema_info_box" style="display:none;">
                  <div class="row text-muted" style="font-size:0.85rem">
                    <div class="col-4"><strong>Jenis Project:</strong><br><span id="info_jp">-</span></div>
                    <div class="col-4"><strong>Jenis Pendanaan:</strong><br><span id="info_jd">-</span></div>
                    <div class="col-4"><strong>Provider:</strong><br><span id="info_prov">-</span></div>
                  </div>
                </div>
              </div>
            </div>

            <hr class="my-4">

            <!-- SDGs -->
            <div class="form-section-title"><i class="bi bi-globe"></i> SDGs (Sustainable Development Goals)</div>
            <div class="row g-3">
              <div class="col-12">
                <label for="sdgs" class="form-label">Pilih SDGs yang didukung project...</label>
                <select id="sdgs" name="sdgs[]" class="form-control select2-sdgs" multiple>
                  @php
                    $selected_sdgs = isset($project) ? $project->sdgs->pluck('id')->toArray() : (old('sdgs') ?? []);
                  @endphp
                  @foreach ($sdgs as $sdg)
                    <option value="{{ $sdg->id }}" {{ in_array($sdg->id, $selected_sdgs) ? 'selected' : '' }}>
                      SDG {{ $sdg->nomor }} - {{ $sdg->nama }}
                    </option>
                  @endforeach
                </select>
                <div class="help-text mt-1">Dapat memilih lebih dari satu SDGs.</div>
              </div>
            </div>

            <hr class="my-4">

            <!-- Konfigurasi RAB -->
            <div id="rab_container" style="display:none;">
              <div class="form-section-title d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-check"></i> Konfigurasi Komponen RAB</span>
                @if(!isset($project))
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAddKomponen">
                  <i class="bi bi-plus"></i> Tambah Komponen
                </button>
                @endif
              </div>
              
              <div class="alert alert-info py-2" style="font-size:0.85rem">
                <i class="bi bi-info-circle"></i> Komponen di bawah ini adalah default dari Skema. Anda dapat menghapus atau mengubah nominalnya khusus untuk Project ini.
              </div>

              <div class="table-responsive border rounded bg-white">
                <table class="table table-hover align-middle mb-0" id="rabTable">
                  <thead class="table-light">
                    <tr>
                      <th width="40%">Komponen Biaya</th>
                      <th width="15%" class="text-center">Wajib?</th>
                      <th width="35%">Nominal Anggaran (Rp)</th>
                      <th width="10%" class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="rabTbody">
                    <!-- Dinamis dari JS -->
                  </tbody>
                  <tfoot class="table-light fw-bold">
                    <tr>
                      <td colspan="2" class="text-end">TOTAL RAB:</td>
                      <td id="totalRAB" class="text-start">Rp. 0</td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <!-- Modal Tambah Komponen Override -->
            <div class="modal fade" id="modalAddKomponen" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Tambah Komponen Biaya</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Pilih Komponen</label>
                      <select class="form-select" id="override_komponen_id">
                        <option value="">-- Pilih Komponen --</option>
                        @if(isset($komponen_biaya))
                          @foreach($komponen_biaya as $k)
                            <option value="{{ $k->id }}" data-nama="{{ $k->nama }}">{{ $k->nama }}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSumbitOverride">Tambahkan ke RAB</button>
                  </div>
                </div>
              </div>
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
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
      const skemaSelect = document.getElementById('skema_pendanaan_id');
      const skemaInfoBox = document.getElementById('skema_info_box');
      const infoJp = document.getElementById('info_jp');
      const infoJd = document.getElementById('info_jd');
      const infoProv = document.getElementById('info_prov');
      
      const rabContainer = document.getElementById('rab_container');
      const rabTbody = document.getElementById('rabTbody');
      const totalRAB = document.getElementById('totalRAB');
      const form = document.getElementById('projectForm');
      
      const isEditMode = document.getElementById('is_edit_mode').value === '1';
      const projectId = document.getElementById('project_id').value;

      function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
      }

      function parseRupiah(str) {
        return Number((str||'').toString().replace(/\D/g,'') || 0);
      }

      function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.input-nominal').forEach(inp => {
          total += parseRupiah(inp.value);
        });
        totalRAB.textContent = 'Rp. ' + formatRupiah(total);
      }

      function attachMasking() {
        document.querySelectorAll('.input-nominal').forEach(inp => {
          inp.addEventListener('input', function() {
            const digits = this.value.replace(/\D/g,'');
            if(!digits){ this.value=''; calculateTotal(); return; }
            this.value = 'Rp. ' + formatRupiah(Number(digits));
            calculateTotal();
          });
        });
      }

      function renderKomponenRow(k, nominal = '') {
        const tr = document.createElement('tr');
        const badge = k.is_wajib ? '<span class="badge bg-danger">Wajib</span>' : '<span class="badge bg-secondary">Opsional</span>';
        const deleteBtn = isEditMode ? '-' : `<button type="button" class="btn btn-sm btn-outline-danger btn-remove-komp"><i class="bi bi-x-lg"></i></button>`;
        const isReadonly = isEditMode ? 'readonly' : '';
        
        tr.innerHTML = `
          <td>
            <strong>${k.nama}</strong>
            <input type="hidden" name="komponen_id[]" value="${k.id}">
          </td>
          <td class="text-center">${badge}</td>
          <td>
            <input type="text" class="form-control input-nominal" name="nominal[]" placeholder="Rp. 0" value="${nominal}" ${isReadonly} ${k.is_wajib ? 'required' : ''}>
          </td>
          <td class="text-center">${deleteBtn}</td>
        `;
        rabTbody.appendChild(tr);
      }

      async function fetchSkemaDetails(skemaId) {
        if(!skemaId) {
          skemaInfoBox.style.display = 'none';
          rabContainer.style.display = 'none';
          return;
        }

        try {
          const res = await fetch(`/project/skema-details/${skemaId}`);
          const data = await res.json();
          
          skemaInfoBox.style.display = 'block';
          infoJp.textContent = data.skema.jenis_project?.nama || '-';
          infoJd.textContent = data.skema.jenis_pendanaan?.nama || '-';
          infoProv.textContent = data.skema.provider?.singkatan || '-';

          if (!isEditMode) {
            rabTbody.innerHTML = '';
            data.komponen.forEach(k => {
              renderKomponenRow(k);
            });
            attachMasking();
            calculateTotal();
            rabContainer.style.display = 'block';
          }
        } catch(e) {
          console.error(e);
        }
      }

      // Hapus komponen
      rabTbody.addEventListener('click', function(e) {
        if(e.target.closest('.btn-remove-komp')) {
          e.target.closest('tr').remove();
          calculateTotal();
        }
      });

      // Tambah komponen manual (override)
      document.getElementById('btnSumbitOverride')?.addEventListener('click', function() {
        const sel = document.getElementById('override_komponen_id');
        const id = sel.value;
        const nama = sel.options[sel.selectedIndex]?.getAttribute('data-nama');
        
        if(!id) return;
        
        // Cek duplikat di UI
        let duplicate = false;
        document.querySelectorAll('input[name="komponen_id[]"]').forEach(inp => {
          if(inp.value === id) duplicate = true;
        });
        
        if(duplicate) {
          alert("Komponen tersebut sudah ada di daftar RAB.");
          return;
        }

        renderKomponenRow({id: id, nama: nama, is_wajib: false});
        attachMasking();
        
        // close modal
        const modalEl = document.getElementById('modalAddKomponen');
        const modalIns = bootstrap.Modal.getInstance(modalEl);
        modalIns.hide();
        sel.value = "";
      });

      skemaSelect.addEventListener('change', function() {
        fetchSkemaDetails(this.value);
      });

      // Init on load
      if (skemaSelect.value) {
        fetchSkemaDetails(skemaSelect.value);
      }

      // Bersihkan masking sebelum disubmit
      form.addEventListener('submit', function(e) {
        document.querySelectorAll('.input-nominal').forEach(inp => {
          inp.value = parseRupiah(inp.value);
        });
      });

      // Khusus edit mode, load existing subkategori
      if (isEditMode && projectId) {
        rabContainer.style.display = 'block';
        fetch(`/project/${projectId}/subcategories`)
          .then(r => r.json())
          .then(items => {
            rabTbody.innerHTML = '';
            items.forEach(item => {
              renderKomponenRow({
                id: item.id_subkategori_sumberdana, // in edit mode we just display it
                nama: item.nama,
                is_wajib: true
              }, 'Rp. ' + formatRupiah(item.nominal));
            });
            attachMasking();
            calculateTotal();
          });
      }
    })();

    // Initialize Select2 for SDGs
    $(document).ready(function() {
      $('.select2-sdgs').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih SDGs yang didukung project...',
        allowClear: true,
        width: '100%'
      });

      // Auto hitung Tahun dan Durasi Project
      const tglMulai = document.getElementById('tanggal_mulai');
      const tglSelesai = document.getElementById('tanggal_selesai');
      const txtTahun = document.getElementById('tahun');
      const txtDurasi = document.getElementById('durasi');

      function calculateDates() {
        if (tglMulai.value && tglSelesai.value) {
          const d1 = new Date(tglMulai.value);
          const d2 = new Date(tglSelesai.value);
          
          if (d1.toString() !== 'Invalid Date') {
             txtTahun.value = d1.getFullYear();
          }
          
          if(d2 >= d1) {
            const diffTime = Math.abs(d2 - d1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // inclusive
            
            let durasiStr = diffDays + " Hari";
            if (diffDays >= 365) {
              const years = (diffDays / 365).toFixed(1);
              durasiStr += " (" + years.replace('.0', '') + " Tahun)";
            } else if (diffDays >= 30) {
              const months = (diffDays / 30).toFixed(1);
              durasiStr += " (" + months.replace('.0', '') + " Bulan)";
            }
            txtDurasi.value = durasiStr;
          } else {
            txtDurasi.value = "Tanggal tidak valid";
          }
        }
      }

      tglMulai.addEventListener('change', calculateDates);
      tglSelesai.addEventListener('change', calculateDates);
      
      // Hitung otomatis saat page load jika sudah ada isinya (misal saat edit form error/back)
      calculateDates();
    });
  </script>
</body>
</html>
