<!DOCTYPE html>
<html lang="id">
<head>
  @extends('layouts.app')
  <meta charset="UTF-8" />
  <title>Request Pembelian</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">

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
    .page-title{ font-size:1.5rem; font-weight:700; margin-bottom:4px; }
    .page-sub{ color:var(--ink-600); margin-bottom:18px; }

    .btn-brand{ background:var(--brand); border-color:var(--brand); color:#fff; }
    .btn-brand:hover{ background:var(--brand-700); border-color:var(--brand-700); }

    .table-wrap{ background:var(--card); border:1px solid var(--line); border-radius:18px; overflow:hidden; }
    .table-modern{ margin:0; vertical-align:middle; }
    .table-modern thead th{ background:#f9fafb; color:var(--ink-600); font-weight:700; border-bottom:1px solid var(--line); position:sticky; top:0; z-index:1; }
    .table-modern td,.table-modern th{ padding:.9rem .9rem; }

    .badge-status{ font-weight:600; letter-spacing:.2px; }
    .badge-wait{ background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
    .badge-acc{ background:#ecfdf5; color:#16a34a; border:1px solid #bbf7d0; }
    .badge-rej{ background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }

    @media (max-width:991.98px){
      .sidebar{ position:fixed; left:-280px; z-index:1040; transition:left .2s; }
      .sidebar.open{ left:0; }
      .content{ padding:18px; }
      .backdrop{ display:none; position:fixed; inset:0; background:rgba(15,23,42,.38); z-index:1035; }
      .backdrop.show{ display:block; }
    }
        /* Badge base */
    .badge-status{ font-weight:600; letter-spacing:.2px; border:1px solid transparent; padding:.35rem .6rem; border-radius:999px; }

    /* Request lifecycle */
    .badge-submit-request { background:#fff7ed; color:#c2410c; border-color:#fed7aa; }     /* oranye = diajukan */
    .badge-approve-request{ background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }     /* biru = disetujui PM/Admin */
    .badge-reject-request { background:#fef2f2; color:#dc2626; border-color:#fecaca; }     /* merah = ditolak */

    /* Payment lifecycle */
    .badge-submit-payment { background:#f5f3ff; color:#6d28d9; border-color:#ddd6fe; }     /* ungu = diajukan bayar */
    .badge-approve-payment{ background:#ecfdf5; color:#16a34a; border-color:#bbf7d0; }     /* hijau = approve bayar */
    .badge-reject-payment { background:#fdf2f8; color:#db2777; border-color:#fbcfe8; }     /* pink = reject bayar */

    /* Selesai */
    .badge-done           { background:#eefdfb; color:#0f766e; border-color:#99f6e4; }     /* teal = selesai */

  </style>
</head>
<body>
  <!-- Topbar -->
  <nav class="navbar topbar navbar-expand-lg">
    <div class="container-fluid">
      <button class="btn btn-light d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>
      <div class="brand-title">STAS-RG • Request Pembelian</div>
      <div class="ms-auto">@include('navbar')</div>
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
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2">
        <div>
          <div class="page-title">Request Pembelian</div>
          <div class="page-sub">Daftar pengajuan pembelian beserta statusnya.</div>
        </div>

        @if (Auth::user()->role != 'admin')
          <a href="{{ route('requestpembelian.create') }}" class="btn btn-brand shadow-soft">
            <i class="bi bi-plus-lg me-1"></i> Input Request Pembelian
          </a>
        @endif
      </div>

      @if ($message = Session::get('success'))
        <div class="alert alert-success mt-3">{{ $message }}</div>
      @endif
      @if ($message = Session::get('error'))
        <div class="alert alert-danger mt-3">{{ $message }}</div>
      @endif

      <div class="table-wrap mt-3">
        <div class="table-responsive">
          <table id="table" class="table table-modern table-striped align-middle">
            <thead>
              <tr>
                <th class="text-center" style="min-width:140px">Nomor Request</th>
                <th class="text-center" style="min-width:180px">Tim Penelitian</th>
                <th class="text-center" style="min-width:220px">Nama Barang</th>
                <th class="text-end"   style="min-width:140px">Total Harga</th>
                <th class="text-center" style="min-width:140px">Status</th>
                <th class="text-center" style="min-width:160px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($request_pembelian as $r)
                @php
                  $status = strtolower($r->status_request ?? '');

                  $badgeMap = [
                    'submit_request'  => ['badge' => 'badge-submit-request',  'label' => 'Submit Request'],
                    'approve_request' => ['badge' => 'badge-approve-request', 'label' => 'Approve Request'],
                    'reject_request'  => ['badge' => 'badge-reject-request',  'label' => 'Reject Request'],
                    'submit_payment'  => ['badge' => 'badge-submit-payment',  'label' => 'Submit Payment'],
                    'approve_payment' => ['badge' => 'badge-approve-payment', 'label' => 'Approve Payment'],
                    'reject_payment'  => ['badge' => 'badge-reject-payment',  'label' => 'Reject Payment'],
                    'done'            => ['badge' => 'badge-done',            'label' => 'Done'],
                  ];

                  $badgeClass = $badgeMap[$status]['badge'] ?? 'badge-submit-request';
                  $labelText  = $badgeMap[$status]['label'] ?? ucwords(str_replace('_',' ', $status));
                @endphp

                <tr>
                  <td class="text-center">{{ $r->no_request }}</td>
                  <td class="text-center">{{ $r->nama_project }}</td>
                  <td class="text-center">{{ $r->nama_barang }}</td>
                  <td class="text-end">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</td>
                  <td class="text-center">
                    <span class="badge badge-status {{ $badgeClass }}">
                      {{ $labelText }}
                    </span>
                  </td>
                  <td class="text-center">
                    <a href="{{ route('requestpembelian.detail', $r->id) }}" class="btn btn-success btn-sm">
                      Detail
                    </a>
                    <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete({{ $r->id }})">
                      Delete
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.querySelector('.backdrop');
    const openSidebar = ()=>{ sidebar.classList.add('open'); backdrop.classList.add('show'); }
    const closeSidebar = ()=>{ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
    toggleBtn?.addEventListener('click', ()=> sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    backdrop?.addEventListener('click', closeSidebar);

    // DataTables
    $(function(){
      new DataTable('#table', {
        paging: true, searching: true, ordering: true, info: true,
        language: {
          search: "Cari:", lengthMenu: "Tampil _MENU_ data",
          info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
          paginate: { previous: "‹", next: "›" }
        },
        columnDefs: [
          { targets: [0,1,2,4,5], className: 'text-center' },
          { targets: [3], className: 'text-end' }
        ]
      });
    });

    // SweetAlert delete (pakai route yang kamu pakai sekarang)
    function confirmDelete(id){
      Swal.fire({
        title:'Yakin ingin menghapus?', text:'Data tidak dapat dikembalikan!',
        icon:'warning', showCancelButton:true,
        confirmButtonColor:'#d33', cancelButtonColor:'#6c757d',
        confirmButtonText:'Ya, hapus!', cancelButtonText:'Batal'
      }).then((r)=>{
        if(r.isConfirmed){
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = '/requestpembelian/destroy/' + id;  // <- sama seperti punyamu
          const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          form.innerHTML = `<input type="hidden" name="_token" value="${csrf}">
                            <input type="hidden" name="_method" value="DELETE">`;
          document.body.appendChild(form); form.submit();
        }
      });
    }
  </script>
</body>
</html>
