<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Request Pembelian</title>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

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
    .menu-title{ font-size:.8rem; letter-spacing:.06em; color:var(--ink-600); text-transform:uppercase; margin: 6px 0 10px; font-weight:600; }
    .nav-link-custom{ display:flex; align-items:center; gap:10px; padding:10px 12px; color:var(--ink); border-radius:12px; text-decoration:none; transition:all .18s; font-weight:500; }
    .nav-link-custom:hover{ background:var(--brand-50); color:var(--brand-700); }
    .nav-link-custom.active{ background:var(--brand); color:#fff; box-shadow:0 6px 16px rgba(22,163,74,.18); }

    .content{ flex:1; padding:24px; }

    /* Page header */
    .page-title{ font-size:1.5rem; font-weight:700; }  /* <= tebal */
    .page-sub{ color:var(--ink-600); }

    /* Cards & table */
    .card-soft{ background:var(--card); border:1px solid var(--line); border-radius:18px; box-shadow:0 8px 22px rgba(15,23,42,.06); }
    .btn-icon{ padding:.35rem .5rem; line-height:1; display:inline-flex; align-items:center; gap:.25rem }
    .btn-icon i{ font-size:1rem }
    td.actions{ white-space:nowrap; width:1%; }

    /* Mobile sidebar */
    @media (max-width: 991.98px){
      .sidebar{ position:fixed; left:-280px; z-index:1040; transition:left .2s; }
      .sidebar.open{ left:0; }
      .content{ padding:18px; }
      .backdrop{ display:none; position:fixed; inset:0; background:rgba(15,23,42,.38); z-index:1035; }
      .backdrop.show{ display:block; }
    }
  </style>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
  <!-- Topbar -->
  <nav class="navbar topbar navbar-expand-lg">
    <div class="container-fluid">
      <button class="btn btn-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>
      <div class="brand-title">STAS-RG • Request Pembelian</div>
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
          <div class="page-title">Request Pembelian</div>
          <div class="page-sub">Tambah / tinjau item serta ubah status request.</div>
        </div>
        <a href="{{ route('requestpembelian.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left-short me-1"></i> Kembali ke Daftar
        </a>
      </div>

      <!-- Ringkasan -->
      <div class="card card-soft mb-3">
        <div class="card-body">
          <div class="row g-3 align-items-center">
            <div class="col-lg-8">
              <label class="form-label mb-1">Tim Penelitian</label>
              <select class="form-select" disabled>
                @foreach ($project as $p)
                  <option value="{{ $p->id }}" {{ $p->id == $request_pembelian->id_project ? 'selected' : '' }}>
                    {{ $p->nama_project }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-lg-4">
              <label class="form-label mb-1">Tanggal Request</label>
              <input type="date" class="form-control" value="{{ $request_pembelian->tgl_request }}" disabled>
            </div>
          </div>
        </div>
      </div>

      <!-- Ubah Status (Admin) -->
      @if (Auth::user()->role == 'admin')
      <div class="card card-soft mb-3">
        <div class="card-body">
          <form action="{{ route('requestpembelian.changestatus') }}" method="POST" class="row g-2 align-items-end">
            @csrf
            <input type="hidden" name="id_request_pembelian_header" value="{{ $request_pembelian->id }}">
            <div class="col-lg-6">
              <label class="form-label mb-1">Ubah Status</label>
              <select class="form-select" id="status_request" name="status_request" @if(count($detail)==0) disabled @endif>
                <option value="approve_request" {{ $request_pembelian->status_request=='approve_request' ? 'selected' : '' }}>Menyetujui Request</option>
                <option value="reject_request"  {{ $request_pembelian->status_request=='reject_request'  ? 'selected' : '' }}>Menolak Request</option>
                <option value="done"            {{ in_array($request_pembelian->status_request,['approve_payment','done']) ? 'selected' : '' }}>Menyetujui Bukti Pembayaran</option>
                <option value="reject_payment"  {{ $request_pembelian->status_request=='reject_payment'  ? 'selected' : '' }}>Menolak Bukti Pembayaran</option>
              </select>
            </div>
            <div class="col-lg-4" id="keterangan_reject_wrap">
              <label class="form-label mb-1">Keterangan (jika menolak)</label>
              <input type="text" class="form-control" id="keterangan_reject" name="keterangan_reject"
                     value="{{ $request_pembelian->keterangan_reject }}" placeholder="Masukkan alasan penolakan">
            </div>
            <div class="col-lg-2 text-lg-end">
              <button class="btn btn-success w-100">
                <i class="bi bi-check-circle me-1"></i> Submit
              </button>
            </div>
          </form>
        </div>
      </div>
      @endif

      <!-- Tabel Detail -->
      <div class="card card-soft">
        <div class="card-body">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Nama Barang</th>
                <th class="text-center">Qty</th>
                <th>Harga</th>
                <th>Link Pembelian</th>
                <th>Subkategori</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($detail as $d)
              <tr>
                <td>{{ $d->nama_barang }}</td>
                <td class="text-center">{{ $d->kuantitas }}</td>
                <td>Rp {{ number_format($d->harga,0,',','.') }}</td>
                <td><a href="{{ $d->link_pembelian }}" target="_blank">Lihat Link</a></td>
                <td>
                  @if ($d->id_subkategori_sumberdana)
                    @foreach ($subkategori as $sub)
                      @if ($sub->id == $d->id_subkategori_sumberdana) {{ $sub->nama }} @endif
                    @endforeach
                  @else - @endif
                </td>
                <td class="actions text-center">
                  @if ($d->bukti_bayar)
                    <a href="{{ asset('bukti_bayar/'.$d->bukti_bayar) }}" class="btn btn-success btn-icon" title="Lihat Bukti">
                      <i class="bi bi-receipt-cutoff"></i>
                    </a>
                  @else
                    <a href="{{ route('requestpembelian.addbukti',$d->id) }}" class="btn btn-primary btn-icon" title="Tambah Bukti">
                      <i class="bi bi-upload"></i>
                    </a>
                  @endif
                  <a href="{{ route('requestpembelian.editdetail',$d->id) }}" class="btn btn-warning btn-icon" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <a href="{{ route('requestpembelian.destroydetail',$d->id) }}" class="btn btn-danger btn-icon"
                     onclick="return confirm('Yakin ingin menghapus item ini?')" title="Hapus">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>
              @endforeach

              @if (Auth::user()->role != 'admin')
              <tr>
                <form action="{{ route('requestpembelian.storedetail') }}" method="POST">
                  @csrf
                  <input type="hidden" name="id_request_pembelian_header" value="{{ $request_pembelian->id }}">
                  <td><input type="text" name="nama_barang" placeholder="Nama Barang" class="form-control" required></td>
                  <td><input type="number" name="kuantitas" class="form-control" min="1" required></td>
                  <td><input type="text" id="harga" name="harga" placeholder="Harga" class="form-control" required></td>
                  <td><input type="url" name="link_pembelian" placeholder="Link Pembelian" class="form-control" required></td>
                  <td>
                    <select name="id_subkategori_sumberdana" class="form-select">
                      <option value="">-- Pilih Subkategori --</option>
                      @foreach ($subkategori as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->nama }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><button type="submit" class="btn btn-success btn-sm">Tambah</button></td>
                </form>
              </tr>
              @endif
            </tbody>
          </table>
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

    // Alasan reject only when needed
    (function(){
      const sel=document.getElementById('status_request');
      const wrap=document.getElementById('keterangan_reject_wrap');
      function toggle(){ wrap.style.display=(sel && (sel.value==='reject_request'||sel.value==='reject_payment'))?'':'none'; }
      sel?.addEventListener('change',toggle); toggle();
    })();

    // Format rupiah untuk input "harga" (baris tambah)
    (function(){
      const harga=document.getElementById('harga');
      if(!harga) return;
      harga.addEventListener('input',()=>{
        let v=harga.value.replace(/[^0-9]/g,'');
        harga.value=v?('Rp. '+new Intl.NumberFormat('id-ID').format(v)):'';
      });
      harga.closest('form')?.addEventListener('submit',()=>{ harga.value=harga.value.replace(/[^0-9]/g,''); });
    })();
  </script>
</body>
</html>
