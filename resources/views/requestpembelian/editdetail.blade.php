<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Request Pembelian</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    :root{
      --brand:#16a34a; --brand-700:#15803d; --brand-50:#ecfdf5;
      --ink:#0f172a; --ink-600:#475569; --line:#e2e8f0; --bg:#f6f7fb; --card:#fff;
    }
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
    .page-title{ font-size:1.5rem; font-weight:700; } /* tebal */
    .page-sub{ color:var(--ink-600); }

    /* Card form */
    .card-soft{ background:var(--card); border:1px solid var(--line); border-radius:18px; box-shadow:0 8px 22px rgba(15,23,42,.06); }
    .help-text{ color:var(--ink-600); font-size:.85rem; }

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
      <div class="brand-title">STAS-RG • Edit Request Pembelian</div>
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
      <a class="nav-link-custom active" href="{{ route('requestpembelian.index') }}"><i class="bi bi-bag-check"></i> Request Pembelian</a>

      @if (Auth::user()->role == 'admin')
        <div class="menu-title mt-3">Administrasi</div>
        <a class="nav-link-custom" href="{{ route('sumberdana.index') }}"><i class="bi bi-cash-coin"></i> Sumber Dana</a>
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
          <div class="page-title">Edit Request Pembelian</div>
          <div class="page-sub">Perbarui detail item request pembelian.</div>
        </div>
        <a href="{{ route('requestpembelian.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left-short me-1"></i> Kembali ke Daftar
        </a>
      </div>

      <div class="card card-soft">
        <div class="card-body">
          <form action="{{ route('requestpembelian.updatedetail', $detail->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
            @csrf
            <input type="hidden" name="id_request_pembelian_header" value="{{ $detail->id_request_pembelian_header }}">

            <div class="col-md-6">
              <label for="nama_barang" class="form-label">Nama Barang</label>
              <input type="text" id="nama_barang" name="nama_barang" value="{{ $detail->nama_barang }}" class="form-control" required>
            </div>

            <div class="col-md-3">
              <label for="kuantitas" class="form-label">Kuantitas</label>
              <input type="number" id="kuantitas" name="kuantitas" value="{{ $detail->kuantitas }}" min="1" class="form-control" required>
            </div>

            <div class="col-md-3">
              <label for="harga" class="form-label">Harga</label>
              <input type="text" id="harga" name="harga" value="{{ number_format($detail->harga,0,',','.') }}" class="form-control" placeholder="Rp 0" required>
              <div class="help-text">Otomatis diformat Rp saat mengetik.</div>
            </div>

            <div class="col-12">
              <label for="link_pembelian" class="form-label">Link Pembelian</label>
              <input type="url" id="link_pembelian" name="link_pembelian" value="{{ $detail->link_pembelian }}" class="form-control" placeholder="https://..." required>
            </div>

            <div class="col-md-6">
              <label for="id_subkategori_sumberdana" class="form-label">Subkategori Sumber Dana</label>
              <select class="form-select" id="id_subkategori_sumberdana" name="id_subkategori_sumberdana">
                <option value="">-- Pilih Subkategori --</option>
                @foreach ($subkategori as $sub)
                  <option value="{{ $sub->id }}" {{ $detail->id_subkategori_sumberdana == $sub->id ? 'selected' : '' }}>
                    {{ $sub->nama }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="bukti_bayar" class="form-label">Bukti Bayar</label>
              <input type="file" id="bukti_bayar" name="bukti_bayar" class="form-control" accept="image/*,application/pdf">
              @if(!empty($detail->bukti_bayar))
                <div class="help-text mt-1">
                  Bukti saat ini: 
                  <a href="{{ asset('bukti_bayar/'.$detail->bukti_bayar) }}" target="_blank">Lihat</a>
                </div>
              @endif
            </div>

            <div class="col-12 d-flex gap-2 mt-2">
              <button class="btn btn-success">
                <i class="bi bi-check2-circle me-1"></i> Submit
              </button>
              <a href="{{ route('requestpembelian.index') }}" class="btn btn-outline-secondary">Batal</a>
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
    const backdrop=document.querySelector('.backdrop');
    const openSidebar=()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    const closeSidebar=()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
    toggleBtn?.addEventListener('click',()=> sidebar.classList.contains('open')?closeSidebar():openSidebar());
    backdrop?.addEventListener('click',closeSidebar);

    // Format Rupiah untuk input harga (tanpa ubah nama field)
    (function(){
      const el=document.getElementById('harga');
      if(!el) return;

      // jika datang dalam format 50.000 -> tampilkan sebagai Rp 50.000
      const raw = el.value.replace(/\D/g,'');
      el.value = raw ? 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(raw)) : '';

      el.addEventListener('input', () => {
        const digits = el.value.replace(/\D/g,'');
        el.value = digits ? 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(digits)) : '';
      });

      // sebelum submit, kembalikan murni angka (tanpa Rp/titik)
      el.form?.addEventListener('submit', () => {
        el.value = el.value.replace(/\D/g,'');
      });
    })();
  </script>
</body>
</html>
