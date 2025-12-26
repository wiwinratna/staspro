<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tambah Sumber Dana</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet"/>

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0; --bg:#f6f7fb; --card:#fff;
    }
    *{box-sizing:border-box}
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
    .page-title{ font-size:1.5rem; font-weight:700; }
    .page-sub{ color:var(--ink-600); }

    /* Card */
    .card-soft{ background:var(--card); border:1px solid var(--line); border-radius:18px; box-shadow:0 8px 22px rgba(15,23,42,.06); }
    .form-section-title{ font-weight:700; font-size:1rem; margin:12px 0 8px; color:var(--ink); }

    /* Mobile sidebar */
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
      <div class="brand-title">STAS-RG • Sumber Dana</div>
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
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <div class="page-title">Tambah Sumber Dana</div>
          <div class="page-sub">Buat sumber dana dan tentukan subkategori pendanaannya.</div>
        </div>
        <a href="{{ route('sumberdana.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left-short me-1"></i> Kembali ke Daftar
        </a>
      </div>

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
        <div class="card-header"><strong>Form Tambah Sumber Dana</strong></div>
        <div class="card-body">
          <form action="{{ route('sumberdana.store') }}" method="POST">
            @csrf

            <div class="row g-3">
              <div class="col-md-6">
                <label for="nama_sumber_dana" class="form-label">Nama Sumber Dana</label>
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
                <label for="jenis_pendanaan" class="form-label">Jenis Pendanaan</label>
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
            <p class="text-muted mb-2">
              Ketik untuk mencari/menambah subkategori. Bisa pilih lebih dari satu.  
              <em>(Jika memilih yang sudah ada, nilainya tetap akan disimpan sebagai teks nama agar cocok dengan validasi controller kamu.)</em>
            </p>

            <select id="subkategori"
                    name="subkategori[]"
                    multiple
                    autocomplete="off">
              @if(!empty($listSubkategori))
                @foreach($listSubkategori as $item)
                  <!-- value pakai nama supaya cocok dengan store() -->
                  <option value="{{ $item->nama }}"
                    @if(collect(old('subkategori', []))->contains($item->nama)) selected @endif>
                    {{ $item->nama }}
                  </option>
                @endforeach
              @endif
            </select>

            <div class="mt-4 d-flex gap-2">
              <button class="btn btn-success">
                <i class="bi bi-check2-circle me-1"></i> Submit
              </button>
              <a href="{{ route('sumberdana.index') }}" class="btn btn-outline-secondary">Batal</a>
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

    // Tom Select untuk subkategori (searchable + multi + bisa tambah baru)
    new TomSelect('#subkategori', {
      create: true,                       // boleh ketik baru
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
